<?php

include ('../../config.php');

// endpoint to register external products into a separate cart table
$nro_venta = $_GET['nro_venta'] ?? '';
$external_id = $_GET['id_producto'] ?? '';
$cantidad = $_GET['cantidad'] ?? 0;
$ubicacion = $_GET['ubicacion'] ?? '';
$nombre = $_GET['nombre'] ?? '';

try {
    // create table if not exists (no FK constraints)
    $sqlCreate = "CREATE TABLE IF NOT EXISTS tb_carrito_externo (
        id_carrito_externo INT AUTO_INCREMENT PRIMARY KEY,
        nro_venta VARCHAR(100) DEFAULT '',
        external_id VARCHAR(255) DEFAULT '',
        nombre VARCHAR(255) DEFAULT '',
        cantidad DECIMAL(10,2) DEFAULT 0,
        ubicacion TEXT,
        fyh_creacion DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sqlCreate);

    $sent = $pdo->prepare("INSERT INTO tb_carrito_externo (nro_venta, external_id, nombre, cantidad, ubicacion, fyh_creacion) VALUES (:nro_venta, :external_id, :nombre, :cantidad, :ubicacion, :fyh_creacion)");
    $fechaHora = date('Y-m-d H:i:s');
    $sent->bindParam(':nro_venta', $nro_venta);
    $sent->bindParam(':external_id', $external_id);
    $sent->bindParam(':nombre', $nombre);
    $sent->bindParam(':cantidad', $cantidad);
    $sent->bindParam(':ubicacion', $ubicacion);
    $sent->bindParam(':fyh_creacion', $fechaHora);

    if($sent->execute()){
        $lastId = $pdo->lastInsertId();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $lastId, 'message' => 'Producto agregado al carrito externo.']);
    }else{
        header('Content-Type: application/json', true, 500);
        echo json_encode(['success' => false, 'message' => 'No se pudo agregar el producto al carrito externo.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json', true, 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

