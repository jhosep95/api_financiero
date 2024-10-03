<?php
require_once '../Models/Cuenta.php';
require_once '../Database/Database.php'; // Asegúrate de incluir la clase Database

use App\Models\Cuenta;

class CuentaController {
    private $cuenta;
    private $historial;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cuenta = new Cuenta($this->db);
    }

    public function listarCuentas() {
        $stmt = $this->cuenta->listarCuentas();
        $cuentas = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cuentas[] = $row;
        }
    
        header('Content-Type: application/json');
        echo json_encode($cuentas);
    }
    
    public function listarDetalleCuentas($id) {
        $cuenta = $this->cuenta->listarDetalleCuentas($id);
        if ($cuenta) {
            header('Content-Type: application/json');
            echo json_encode($cuenta);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Cuenta no encontrada."]);
        }
    }    
    public function depositar($id) {
        // Obtener los datos de la solicitud
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->monto) && is_numeric($data->monto) && $data->monto > 0) {
            $monto = $data->monto;

            // Intentar procesar el depósito
            if ($this->cuenta->depositar($id, $monto)) {
                http_response_code(200);
                echo json_encode(["message" => "Depósito realizado con éxito."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Cuenta no encontrada."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Monto inválido."]);
        }
    }
    public function retirar($id) {
        // Obtener los datos de la solicitud
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->monto) && is_numeric($data->monto) && $data->monto > 0) {
            $monto = $data->monto;
            $mensaje = $this->cuenta->retirar($id, $monto);
            // Intentar procesar el retiro
            if($mensaje==1) {
                http_response_code(response_code: 200);
                echo json_encode(["message" => "Retiro realizado con éxito."]);
            } else if($mensaje==2){
                http_response_code(404);
                echo json_encode(["message" => "Saldo insuficiente"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Cuenta no encontrada."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Monto inválido"]);
        }
    }
    public function transferir($id) {
         // Obtener los datos de la solicitud
         $data = json_decode(file_get_contents("php://input"));

         if (isset($data->monto) && isset($data->cuentaDestinoId)  && is_numeric($data->cuentaDestinoId)
            && is_numeric($data->monto) && $data->monto > 0) {
             $monto = $data->monto; 
             $cuentaDestinoId=$data->cuentaDestinoId;
             $mensaje = $this->cuenta->transferir($id, $monto,$cuentaDestinoId);
             // Intentar procesar el depósito
             if($mensaje==1) {
                http_response_code(response_code: 200);
                echo json_encode(["message" => "Transferencia realizada con éxito."]);
            } else if($mensaje==2){
                http_response_code(404);
                echo json_encode(["message" => "Transferencia rechazada"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Cuenta no encontrada."]);
            }
         } else {
             http_response_code(400);
             echo json_encode(["message" => "Monto inválido."]);
         }
    }
}