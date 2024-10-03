
# Proyecto Crear API con php

Requisitos previos

Asegúrate de tener instalados los siguientes programas en tu computadora antes de comenzar:

    XAMPP (incluye Apache, PHP, y MySQL)
    phpMyAdmin
    Navegador web (Chrome, Firefox, etc.)
    Cliente para realizar solicitudes HTTP (Postman o cURL)

Instrucciones para la instalación local
1. Clonar el repositorio
Primero, clona este repositorio en tu máquina local. Abre una terminal y ejecuta el siguiente comando:

    git clone https://github.com/jhosep95/api_financiero

2. Configuración del servidor local
Abre XAMPP y asegúrate de iniciar los módulos de Apache y MySQL.
Coloca los archivos del proyecto en la carpeta htdocs de XAMPP. La ruta debería ser algo como:

    C:/xampp/htdocs/proyecto-api-financiera

3. Configuración de la base de datos

Abre phpMyAdmin en tu navegador accediendo a 

    http://localhost/phpmyadmin/.

Crea una base de datos con el nombre 

    api_financiera

Ve a la pestaña Bases de datos y crea una nueva base de datos llamada api_financiera.
Importa el archivo SQL para generar las tablas necesarias:
En la base de datos api_financiera, selecciona la pestaña Importar.
Elige el archivo api__transacciones.sql que se encuentra en la carpeta sql/ del proyecto.
Haz clic en Continuar para importar las tablas.

4. Configuración de la conexión a la base de datos
Ve al archivo 

    config/Database.php 

y asegúrate de configurar las credenciales de la base de datos:

    private $host = 'localhost';
    private $db_name = 'api_financiera';
    private $username = 'root';
    private $password = '';
    private $conn;

5. Ejecutar el proyecto
Abre una terminal y navega hasta la carpeta 

    public/ 
del proyecto, donde se encuentran los archivos accesibles públicamente:

    cd /ruta/a/proyecto-api-financiera/public

Ejecuta el siguiente comando para iniciar el servidor embebido de PHP en el puerto 8000:

    php -S localhost:8000

Abre tu navegador web y accede al proyecto en 
    
    http://localhost:8000/api-financiera/public/cuentas

6. Rutas de la API

A continuación se detallan las rutas principales de la API:

a) Listar Cuentas: Metodo: GET

    http://localhost:8000/api-financiera/public/cuentas

respuesta:

    [
        {
            "id": 1,
            "saldo": 5000,
            "titularCuenta": "John Doe"
        },
        {
            "id": 2,
            "saldo": 200,
            "titularCuenta": "Jane Smith"
        }
    ]

b)  Procesar depositos: POST

    http://localhost:8000/api-financiera/public/cuentas/{id}/depositar

estructura de consulta:

    { 
    "monto": 1000 
    }

respuesta:

    {
	"message": "Depósito realizado con éxito."
    }

c) Procesar retiro: Metodo: POST

    http://localhost:8000/api-financiera/public/cuentas/{id}/retirar

estructura de consulta:

    { 
    "monto": 1000 
    }

respuesta:

    {
    "message": "Retiro realizado con éxito."    
    }


d) Procesar Transferencia: Metodo: POST

    http://localhost:8000/api-financiera/public/cuentas/{id}/transferir

estructura de consulta:

    {
	"cuentaDestinoId": 2, 
	"monto": 500
    }

respuesta:

    {
	"message": "Transferencia realizada con éxito."
    }

e) Ver detalle de cuenta: Metodo: GET

    http://localhost:8000/api-financiera/public/cuentas/{id}

respuesta:

    {
	"id": 1,
	"saldo": "3693.95",
	"tipoCuenta": "CuentaEstandar",
	"historialTransacciones": [
            {
                "id": 60,
                "cuenta_id": 1,
                "tipo": "depósito",
                "monto": "100.00",
                "saldo_anterior": "5120.00",
                "saldo_posterior": "5220.00",
                "fecha": "2024-10-03 02:16:11"
            },
            {
                "id": 63,
                "cuenta_id": 1,
                "tipo": "Retiro",
                "monto": "1000.00",
                "saldo_anterior": "5220.00",
                "saldo_posterior": "4200.00",
                "fecha": "2024-10-03 02:17:37"
            }
	    ]
    }
