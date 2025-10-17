<?php

include ('../../config.php');
require_once __DIR__ . '/../almacen2/api_client.php';

// Obtener usuario de sesión para mandarlo al ajuste remoto
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$usuarioSesion = isset($_SESSION['sesion_email']) ? $_SESSION['sesion_email'] : 'Sistema';

$nro_venta = isset($_GET['nro_venta']) ? $_GET['nro_venta'] : '';
$id_cliente = isset($_GET['id_cliente']) ? (int)$_GET['id_cliente'] : null;
if ($nro_venta === '') {
    echo '<script>alert("Falta nro_venta"); location.href = "'.$URL.'/ventas2/create.php";</script>';
    exit;
}

// Asegurar existencia de carrito externo
$pdo->exec("CREATE TABLE IF NOT EXISTS tb_carrito_externo (
    id_carrito_externo INT AUTO_INCREMENT PRIMARY KEY,
    nro_venta VARCHAR(100) DEFAULT '',
    external_id VARCHAR(255) DEFAULT '',
    nombre VARCHAR(255) DEFAULT '',
    cantidad DECIMAL(10,2) DEFAULT 0,
    ubicacion TEXT,
    fyh_creacion DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Asegurar existencia de tabla de ventas externas (según estructura brindada)
$pdo->exec("CREATE TABLE IF NOT EXISTS tb_ventas_externas (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nro_venta VARCHAR(100) DEFAULT '',
    id_cliente INT NULL,
    nombre VARCHAR(255) NOT NULL,
    stock_total VARCHAR(50) NOT NULL,
    ubicaciones VARCHAR(255) NOT NULL,
    precio DECIMAL(18,2) DEFAULT 0,
    external_id INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Intentar agregar columna external_id si la tabla ya existía sin ella (ignorar errores)
try { $pdo->exec("ALTER TABLE tb_ventas_externas ADD COLUMN IF NOT EXISTS external_id INT DEFAULT 0"); } catch (Exception $e) { /* noop */ }
// Nuevas columnas para agrupar por venta y asociar cliente
try { $pdo->exec("ALTER TABLE tb_ventas_externas ADD COLUMN IF NOT EXISTS nro_venta VARCHAR(100) DEFAULT ''"); } catch (Exception $e) { /* noop */ }
try { $pdo->exec("ALTER TABLE tb_ventas_externas ADD COLUMN IF NOT EXISTS id_cliente INT NULL"); } catch (Exception $e) { /* noop */ }

// Obtener ítems del carrito externo
$stmt = $pdo->prepare("SELECT * FROM tb_carrito_externo WHERE nro_venta = :nv ORDER BY id_carrito_externo ASC");
$stmt->bindParam(':nv', $nro_venta);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items || count($items) === 0) {
    echo '<script>alert("No hay productos en el carrito externo"); location.href = "'.$URL.'/ventas2/create.php";</script>';
    exit;
}

$monto_total = isset($_GET['monto_total']) ? $_GET['monto_total'] : 0;
$ins = $pdo->prepare("INSERT INTO tb_ventas_externas (nro_venta, id_cliente, nombre, stock_total, ubicaciones, precio, external_id) VALUES (:nro_venta, :id_cliente, :nombre, :stock_total, :ubicaciones, :precio, :external_id)");

// Best-effort: ajustar inventario remoto si está configurado
$cfg = require __DIR__ . '/../almacen2/config_api.php';

foreach ($items as $it) {
    $nombre = isset($it['nombre']) ? $it['nombre'] : '';
    $cant = (string)$it['cantidad'];
    $ubi = isset($it['ubicacion']) ? $it['ubicacion'] : '';

    $ins->execute([
        ':nro_venta' => $nro_venta,
        ':id_cliente' => $id_cliente,
        ':nombre' => $nombre,
        ':stock_total' => $cant,
        ':ubicaciones' => $ubi,
        ':precio' => $monto_total,
        ':external_id' => (int)($it['external_id'] ?? 0),
    ]);

    // Ajuste remoto (no bloqueante si falla)
    try {
        if (!empty($cfg['adjust_stock_path'])) {
            $idProd = (int)($it['external_id'] ?? 0);
            $qty = (float)$it['cantidad'];
            // Parsear el primer id_ubicacion del string "id:stock, id2:stock2"
            $ubiStr = (string)$ubi;
            $idUbi = null;
            if ($ubiStr !== '') {
                // tomar primer par separado por coma
                $first = explode(',', $ubiStr)[0];
                if ($first !== null && $first !== '') {
                    $parts = explode(':', trim($first));
                    if (count($parts) >= 1) {
                        $idUbi = (int)preg_replace('/[^0-9]/', '', $parts[0]);
                    }
                }
            }

            if ($idProd > 0 && $qty > 0) {
                $payload = [
                    'id_producto' => $idProd,
                    'id_ubicacion' => $idUbi,
                    'cantidad' => -$qty,
                    'motivo' => 'Venta realizada nro '.$nro_venta,
                    'usuario' => $usuarioSesion,
                ];
                $res = api_request('POST', $cfg['adjust_stock_path'], [], $payload);
                if (!$res['ok'] || (isset($res['status']) && $res['status'] >= 400)) {
                    @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN adjust ventas_externas nro=".$nro_venta." product=".$idProd." resp=".json_encode($res)."\n", FILE_APPEND);
                }
            }
        }
    } catch (Exception $e) {
        @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN adjust exception ventas_externas: ".$e->getMessage()."\n", FILE_APPEND);
    }
}

// Limpiar carrito externo de ese nro_venta
$del = $pdo->prepare("DELETE FROM tb_carrito_externo WHERE nro_venta = :nv");
$del->bindParam(':nv', $nro_venta);
$del->execute();

session_start();
$_SESSION['mensaje1'] = 'Venta externa registrada';
echo '<script>location.href = "'.$URL.'/ventas2/create.php";</script>';
exit;
