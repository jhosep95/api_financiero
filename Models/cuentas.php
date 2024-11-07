<?php
// /api-financiera/routes/cuentas.php

require_once '../controllers/CuentaController.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$controller = new CuentaController();

// Obtiene la ruta
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', $_SERVER['REQUEST_URI']);

// Verifica si es un depósito
if ($requestMethod === 'POST' && isset($requestUri[3]) && $requestUri[3] === 'cuentas' && isset($requestUri[4]) && $requestUri[5] === 'depositar'){
    $id = $requestUri[4]; // Obtiene el ID de la cuenta de la URI
    $controller->depositar($id); // Llama al método depositar
} elseif ($requestMethod === 'GET' && isset($requestUri[3]) && $requestUri[3] === 'cuentas' && !isset($requestUri[4])) {
    // Ruta: /cuentas -> Listar todas las cuentas
    $controller->listarCuentas(); // Llama al método listar cuentas
}elseif ($requestMethod === 'POST' && isset($requestUri[3]) && $requestUri[3] === 'cuentas' && isset($requestUri[4]) && $requestUri[5] === 'retirar'){
    $id = $requestUri[4]; // Obtiene el ID de la cuenta de la URI
    $controller->retirar(id:$id); // Llama al método retirar
}elseif ($requestMethod === 'POST' && isset($requestUri[3]) && $requestUri[3] === 'cuentas' && isset($requestUri[4]) && $requestUri[5] === 'transferir'){
    $id = $requestUri[4]; // Obtiene el ID de la cuenta de la URI
    $controller->transferir(id:$id); // Llama al método transferir
}elseif ($requestMethod === 'GET' && isset($requestUri[3]) && $requestUri[3] === 'cuentas' && isset($requestUri[4]) && is_numeric($requestUri[4])) {
    // Ruta: /cuentas/{id} -> Listar detalle de una cuenta por ID
    $id = $requestUri[4];
    $controller->listarDetalleCuentas($id); // Llama al método listar detalle de una cuenta específica
}else {
    http_response_code(404);
    echo json_encode(["message" => "Ruta no encontrada."]);
}