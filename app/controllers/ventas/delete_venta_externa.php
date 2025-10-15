<?php
include ('../../config.php');
require_once __DIR__ . '/../almacen2/api_client.php';

// Params
$id = isset($_GET['id_producto']) ? (int)$_GET['id_producto'] : 0; // id PK en tb_ventas_externas
if ($id <= 0) {
    http_response_code(400);
    echo 'ID inválido';
    exit;
}

// Obtener fila
$stmt = $pdo->prepare("SELECT * FROM tb_ventas_externas WHERE id_producto = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo 'Venta externa no encontrada';
    exit;
}

$externalId = (int)($row['external_id'] ?? 0);
$cantidad = (float)($row['stock_total'] ?? 0);
$ubicaciones = (string)($row['ubicaciones'] ?? '');
$nombre = (string)($row['nombre'] ?? '');
$usuario = 'Sistema';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!empty($_SESSION['sesion_email'])) { $usuario = $_SESSION['sesion_email']; }

// Parsear id_ubicacion del campo ubicaciones (formato '1:80, 2:10')
$idUbicacion = null;
if ($ubicaciones !== '') {
    $first = explode(',', $ubicaciones)[0];
    if ($first !== null && $first !== '') {
        $parts = explode(':', trim($first));
        if (count($parts) >= 1) {
            $idUbicacion = (int)preg_replace('/[^0-9]/', '', $parts[0]);
        }
    }
}

// Si no tenemos external_id (ventas antiguas), intentar resolverlo por nombre contra la API
if ($externalId <= 0 && $nombre !== '') {
    try {
        $cfg = require __DIR__ . '/../almacen2/config_api.php';
        $resp = api_request('GET', $cfg['available_products_path']);
        if ($resp['ok'] && isset($resp['json']) && is_array($resp['json'])) {
            foreach ($resp['json'] as $prod) {
                if (isset($prod['nombre']) && strcasecmp(trim($prod['nombre']), trim($nombre)) === 0) {
                    $externalId = (int)($prod['id_producto'] ?? 0);
                    break;
                }
            }
        }
    } catch (Exception $e) {
        @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN resolve external_id by name failed: ".$e->getMessage()."\n", FILE_APPEND);
    }
}

// Primero ajustar stock remoto: suma positiva de la cantidad
try {
    $cfg = require __DIR__ . '/../almacen2/config_api.php';
    if (!empty($cfg['adjust_stock_path']) && $externalId > 0 && $cantidad > 0) {
        $payload = [
            'id_producto' => $externalId,
            'id_ubicacion' => $idUbicacion,
            'cantidad' => $cantidad, // sumar de vuelta
            'motivo' => 'Reversión venta externa id '.$id,
            'usuario' => $usuario,
        ];
        $res = api_request('POST', $cfg['adjust_stock_path'], [], $payload);
        if (!$res['ok'] || (isset($res['status']) && $res['status'] >= 400)) {
            @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN reverse adjust id=".$id." product=".$externalId." resp=".json_encode($res)."\n", FILE_APPEND);
        }
    }
} catch (Exception $e) {
    @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN reverse adjust exception: ".$e->getMessage()."\n", FILE_APPEND);
}

// Luego eliminar fila local
$del = $pdo->prepare("DELETE FROM tb_ventas_externas WHERE id_producto = :id");
$del->execute([':id' => $id]);

// Responder y redirigir
$_SESSION['mensaje1'] = 'Venta externa eliminada';
echo '<script>location.href = "'.$URL.'/ventas";</script>';
exit;
