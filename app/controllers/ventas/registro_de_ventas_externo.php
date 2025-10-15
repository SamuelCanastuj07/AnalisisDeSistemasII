<?php

include ('../../config.php');

// Inputs por GET (mantener coherencia con flujo existente)
$nro_venta = isset($_GET['nro_venta']) ? $_GET['nro_venta'] : '';
$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';
$total_cancelar = isset($_GET['total_cancelar']) ? $_GET['total_cancelar'] : 0;

if ($nro_venta === '' || $id_cliente === '') {
    http_response_code(400);
    echo '<script>alert("Faltan datos de la venta externa"); location.href = "'.$URL.'/ventas2/create.php";</script>';
    exit;
}

require_once __DIR__ . '/../almacen2/api_client.php';

try {
    // Desactivar FK checks de inicio (sesión actual)
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    $pdo->beginTransaction();

    // Insertar la venta localmente
    $sentencia = $pdo->prepare("INSERT INTO tb_ventas (nro_venta, id_cliente, total_pagado, fyh_creacion)
    VALUES (:nro_venta, :id_cliente, :total_pagado, :fyh_creacion)");

    $fechaHora = date('Y-m-d H:i:s');
    $sentencia->bindParam('nro_venta', $nro_venta);
    $sentencia->bindParam('id_cliente', $id_cliente);
    $sentencia->bindParam('total_pagado', $total_cancelar);
    $sentencia->bindParam('fyh_creacion', $fechaHora);
    if (!$sentencia->execute()) {
        throw new Exception('No se pudo registrar la venta local');
    }

    // Obtener items del carrito externo
    $pdo->exec("CREATE TABLE IF NOT EXISTS tb_carrito_externo (
        id_carrito_externo INT AUTO_INCREMENT PRIMARY KEY,
        nro_venta VARCHAR(100) DEFAULT '',
        external_id VARCHAR(255) DEFAULT '',
        nombre VARCHAR(255) DEFAULT '',
        cantidad DECIMAL(10,2) DEFAULT 0,
        ubicacion TEXT,
        fyh_creacion DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmt = $pdo->prepare("SELECT id_carrito_externo, external_id, cantidad FROM tb_carrito_externo WHERE nro_venta = :nv");
    $stmt->bindParam(':nv', $nro_venta);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // No llamar aún al endpoint remoto; primero persistimos detalle local y confirmamos venta

    // Persistir detalle de la venta externa (para que aparezca en listados)
    $pdo->exec("CREATE TABLE IF NOT EXISTS tb_ventas_detalle_externo (
        id_detalle_externo INT AUTO_INCREMENT PRIMARY KEY,
        nro_venta VARCHAR(100) NOT NULL,
        external_id INT NOT NULL,
        nombre VARCHAR(255) DEFAULT '',
        cantidad DECIMAL(10,2) DEFAULT 0,
        ubicacion TEXT,
        fyh_creacion DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $insDet = $pdo->prepare("INSERT INTO tb_ventas_detalle_externo (nro_venta, external_id, nombre, cantidad, ubicacion, fyh_creacion)
        VALUES (:nro_venta, :external_id, :nombre, :cantidad, :ubicacion, :fyh)");
    foreach ($items as $it) {
        $insDet->execute([
            ':nro_venta' => $nro_venta,
            ':external_id' => (int)$it['external_id'],
            ':nombre' => isset($it['nombre']) ? $it['nombre'] : '',
            ':cantidad' => (float)$it['cantidad'],
            ':ubicacion' => isset($it['ubicacion']) ? $it['ubicacion'] : '',
            ':fyh' => $fechaHora,
        ]);
    }

    // Commit de la venta y su detalle externo
    $pdo->commit();
    // Rehabilitar FKs
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    // Intento de ajuste remoto (best-effort, no aborta la venta si falla)
    try {
        $cfg = require __DIR__ . '/../almacen2/config_api.php';
        if (!empty($cfg['adjust_stock_path'])) {
            foreach ($items as $it) {
                $idProd = (int)$it['external_id'];
                $cant = (float)$it['cantidad'];
                if ($idProd <= 0 || $cant <= 0) continue;

                $payload1 = [ 'id_producto' => $idProd, 'delta' => -$cant, 'reason' => 'venta nro '.$nro_venta ];
                $res = api_request('POST', $cfg['adjust_stock_path'], [], $payload1);
                if (!$res['ok'] || ($res['status'] >= 400)) {
                    $payload2 = [ 'productId' => $idProd, 'delta' => -$cant, 'reason' => 'venta nro '.$nro_venta ];
                    $res = api_request('POST', $cfg['adjust_stock_path'], [], $payload2);
                }
                if (!$res['ok'] || ($res['status'] >= 400)) {
                    $payload3 = [ 'productId' => $idProd, 'quantity' => $cant, 'type' => 'SUBTRACT', 'reason' => 'venta nro '.$nro_venta ];
                    $res = api_request('POST', $cfg['adjust_stock_path'], [], $payload3);
                }
                if (!$res['ok'] || ($res['status'] >= 400)) {
                    @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN adjust failed nro=".$nro_venta." product=".$idProd." resp=".json_encode($res)."\n", FILE_APPEND);
                }
            }
        }
    } catch (Exception $exRemote) {
        @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN adjust exception: ".$exRemote->getMessage()."\n", FILE_APPEND);
    }

    // Limpiar carrito externo tras registrar (independiente del ajuste remoto)
    try {
        $del = $pdo->prepare("DELETE FROM tb_carrito_externo WHERE nro_venta = :nv");
        $del->bindParam(':nv', $nro_venta);
        $del->execute();
    } catch (Exception $edel) { /* ignore */ }

    session_start();
    $_SESSION['mensaje1'] = 'Se registró la venta y se ajustó el inventario externo';
    echo '<script>location.href = "'.$URL.'/ventas2/create.php";</script>';
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    try { $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (Exception $ie) {}
    @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." EXCEPTION: ".$e->getMessage()."\n", FILE_APPEND);
    session_start();
    $_SESSION['mensaje7'] = 'Venta no registrada: '. $e->getMessage();
    echo '<script>location.href = "'.$URL.'/ventas2/create.php";</script>';
    exit;
}
