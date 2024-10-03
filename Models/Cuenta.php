<?php
namespace App\Models;
use PDO;
use PDOException;
class Cuenta {
    private $conn;
    private $table_cuentas = "cuentas"; // nombre de la tabla
    private $table_transacciones = "transacciones"; // nombre de la tabla transaccion historial
    private $table_titulares = "titulares"; // nombre de la tabla
    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarCuentas() {
        // Consulta para obtener las cuentas con los titulares asociados
        $query = "SELECT 
                    c.id, 
                    c.saldo, 
                    c.tipoCuenta, 
                    t.nombre as titularCuenta, 
                    t.direccion 
                  FROM " . $this->table_cuentas . " c 
                  INNER JOIN $this->table_titulares t ON c.id = t.id"; // Relacionando cuentas con titulares     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function listarDetalleCuentas($id) {
        // Consulta para obtener los datos de la cuenta
        $queryCuenta = "SELECT id, saldo, tipoCuenta FROM " . $this->table_cuentas . " WHERE id = :id";
        $stmtCuenta = $this->conn->prepare($queryCuenta);
        $stmtCuenta->bindParam(':id', $id);
        $stmtCuenta->execute();
        
        $cuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC);
    
        if ($cuenta) {
            // Consulta para obtener el historial de transacciones
            $queryTransacciones = "SELECT * FROM " . $this->table_transacciones . " WHERE cuenta_id = :id";
            $stmtTransacciones = $this->conn->prepare($queryTransacciones);
            $stmtTransacciones->bindParam(':id', $id);
            $stmtTransacciones->execute();
            $transacciones = $stmtTransacciones->fetchAll(PDO::FETCH_ASSOC);
    
            // Añadir el historial de transacciones a la cuenta
            $cuenta['historialTransacciones'] = $transacciones;
    
            return $cuenta;
        } else {
            return null; // Si no se encuentra la cuenta
        }
    }
    
    public function depositar($id, $monto) {
        // Comienza la transacción
        $this->conn->beginTransaction();
        $tipo = "depósito";
        try {
            // Obtén el saldo actual de la cuenta
            $query = "SELECT saldo FROM " . $this->table_cuentas . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cuenta) {
                // Calcula el nuevo saldo
                $nuevoSaldo = $cuenta['saldo'] + $monto;

                // Actualiza el saldo en la base de datos
                $query = "UPDATE " . $this->table_cuentas . " SET saldo = :nuevoSaldo WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':nuevoSaldo', $nuevoSaldo);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                 // Llama a la función para registrar la transacción
                if ($this->modTransaccion($id, $monto,$cuenta['saldo'],$nuevoSaldo, $tipo)) {
                    // Confirma la transacción solo si se guarda correctamente el historial
                    $this->conn->commit();
                    return true;
                } else {
                    // Si falla al guardar la transacción en el historial, deshacer la transacción
                    $this->conn->rollBack();
                    return false;
                }
            } else {
                // Si no se encuentra la cuenta, deshace la transacción
                $this->conn->rollBack();
                return false; // O lanzar una excepción
            }
        } catch (PDOException $e) {
            // En caso de error, deshace la transacción
            $this->conn->rollBack();
            throw $e; // Lanza la excepción para manejo posterior
        }
    }
    public function transferir($id, $monto,$cuentaDestinoId) {
        // Comienza la transacción
        $this->conn->beginTransaction();
        $tipo = "transferencia";
        try {
            // Obtén el saldo actual de la cuenta
            $query = "SELECT id, saldo, tipoCuenta FROM " . $this->table_cuentas . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cuenta) {
                    $nuevoSaldo_estado=true;
                    $envio = $monto;
                    if($cuenta['tipoCuenta']=="CuentaEstandar"){ 
                         // Calcula el nuevo saldo
                        $nuevoSaldo = $cuenta['saldo'] - $monto*1.01;
                        if($nuevoSaldo<100){
                            $nuevoSaldo_estado=false;
                        }
                    }else{
                         // Calcula el nuevo saldo
                        $nuevoSaldo = $cuenta['saldo'] - $monto;
                    }  
                    if($nuevoSaldo_estado){
                        // Actualiza el saldo en la base de datos
                        $query = "UPDATE " . $this->table_cuentas . " SET saldo = :nuevoSaldo WHERE id = :id";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bindParam(':nuevoSaldo', $nuevoSaldo);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        // Llama a la función para registrar la transacción
                        if ($this->modTransaccion($id, $monto,$cuenta['saldo'],$nuevoSaldo, $tipo) && 
                            $this->modTransferir($cuentaDestinoId, $envio)) {
                            // Confirma la transacción solo si se guarda correctamente el historial
                            $this->conn->commit();
                            return 1;
                        } else {
                            // Si falla al guardar la transacción en el historial, deshacer la transacción
                            $this->conn->rollBack();
                            return 1;
                        }
                    }else {
                        // Si falla al guardar la transacción en el historial, deshacer la transacción
                        $this->conn->rollBack();
                        return 2;
                    }
            } else {
                // Si no se encuentra la cuenta, deshace la transacción
                $this->conn->rollBack();
                return 3; // O lanzar una excepción
            }
        } catch (PDOException $e) {
            // En caso de error, deshace la transacción
            $this->conn->rollBack();
            throw $e; // Lanza la excepción para manejo posterior
        }
    }
    public function retirar($id, $monto) {
        // Comienza la transacción
        $this->conn->beginTransaction();
        $tipo = "Retiro";
        try {
            // Obtén el saldo actual de la cuenta
            $query = "SELECT saldo, tipoCuenta FROM " . $this->table_cuentas . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cuenta) {
                    $nuevoSaldo_estado=true;
                    if($cuenta['tipoCuenta']=="CuentaEstandar"){ 
                         // Calcula el nuevo saldo
                        $nuevoSaldo = $cuenta['saldo'] - $monto*1.02;
                        if($nuevoSaldo<100){
                            $nuevoSaldo_estado=false;
                        }
                    }else{
                         // Calcula el nuevo saldo
                        $nuevoSaldo = $cuenta['saldo'] - $monto;
                    }  

                    if($nuevoSaldo_estado){
                        // Actualiza el saldo en la base de datos
                        $query = "UPDATE " . $this->table_cuentas . " SET saldo = :nuevoSaldo WHERE id = :id";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bindParam(':nuevoSaldo', $nuevoSaldo);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        // Llama a la función para registrar la transacción
                        if ($this->modTransaccion($id, $monto,$cuenta['saldo'],$nuevoSaldo, $tipo)) {
                            // Confirma la transacción solo si se guarda correctamente el historial
                            $this->conn->commit();
                            return 1;
                        } else {
                            // Si falla al guardar la transacción en el historial, deshacer la transacción
                            $this->conn->rollBack();
                            return 1;
                        }
                    }else {
                        // Si falla al guardar la transacción en el historial, deshacer la transacción
                        $this->conn->rollBack();
                        return 2;
                    }
            } else {
                // Si no se encuentra la cuenta, deshace la transacción
                $this->conn->rollBack();
                return 3; // O lanzar una excepción
            }
        } catch (PDOException $e) {
            // En caso de error, deshace la transacción
            $this->conn->rollBack();
            throw $e; // Lanza la excepción para manejo posterior
        }
    }
    public function modTransaccion($id, $monto,$s_anterior,$s_posterior,$tipo) {
        // Inserta un registro de la transacción en el historial
        $query = "INSERT INTO " . $this->table_transacciones . 
        " (cuenta_id, monto,saldo_anterior,saldo_posterior, tipo, fecha) 
        VALUES (:id, :monto, :saldo_anterior, :saldo_posterior,:tipo, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':saldo_anterior', $s_anterior);
        $stmt->bindParam(':saldo_posterior', $s_posterior);
        $stmt->bindParam(':tipo', $tipo);
    
        // Ejecuta el query para insertar en el historial de transacciones
        return $stmt->execute(); // Retorna true si la inserción fue exitosa, de lo contrario false
    }
    public function modTransferir($id, $monto) {
        // Obtén el saldo actual de la cuenta
        $tipo = "depósito";
        $query = "SELECT id, saldo FROM " . $this->table_cuentas . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cuenta) {
            $nuevoSaldo = $cuenta['saldo'] + $monto;
            // Inserta un registro de la transacción en el historial
            $query = "UPDATE " . $this->table_cuentas . " SET saldo = :nuevoSaldo WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nuevoSaldo', $nuevoSaldo);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            if ($this->modTransaccion($id, $monto,$cuenta['saldo'],$nuevoSaldo, $tipo)) {
                // Confirma la transacción solo si se guarda correctamente el historial              
                return true;
            } else {
                // Si falla al guardar la transacción en el historial, deshacer la transacción           
                return false;
            }
        }else {
            // Si no se encuentra la cuenta, deshace la transacción
            $this->conn->rollBack();
            return false;
        }
    }
    
}
