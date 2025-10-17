<?php
include ('../../config.php');
require_once __DIR__ . '/../almacen2/api_client.php';

// Elimina una venta externa completa (todas las filas con el mismo nro_venta) y revierte stock remoto
$nro = isset($_GET['nro_venta']) ? $_GET['nro_venta'] : '';
if ($nro === '') {
  http_response_code(400);
  echo 'Falta nro_venta';
  exit;
}

// Obtener filas de esa venta
$st = $pdo->prepare("SELECT * FROM tb_ventas_externas WHERE nro_venta = :nv");
$st->execute([':nv' => $nro]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
  $_SESSION['mensaje6'] = 'No se encontró la venta externa';
  echo '<script>location.href = "'.$URL.'/ventas";</script>';
  exit;
}

// Usuario de sesión
$usuario = 'Sistema';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!empty($_SESSION['sesion_email'])) { $usuario = $_SESSION['sesion_email']; }

// Intentar revertir stock remoto por cada ítem
try {
  $cfg = require __DIR__ . '/../almacen2/config_api.php';
  foreach ($rows as $row) {
    $externalId = (int)($row['external_id'] ?? 0);
    $cantidad = (float)($row['stock_total'] ?? 0);
    $ubicaciones = (string)($row['ubicaciones'] ?? '');

    // parse first id_ubicacion
    $idUbicacion = null;
    if ($ubicaciones !== '') {
      $first = explode(',', $ubicaciones)[0];
      if ($first !== null && $first !== '') {
        $parts = explode(':', trim($first));
        if (count($parts) >= 1) { $idUbicacion = (int)preg_replace('/[^0-9]/', '', $parts[0]); }
      }
    }

    if (!empty($cfg['adjust_stock_path']) && $externalId > 0 && $cantidad > 0) {
      $payload = [
        'id_producto' => $externalId,
        'id_ubicacion' => $idUbicacion,
        'cantidad' => $cantidad,
        'motivo' => 'Reversión venta externa nro '.$nro,
        'usuario' => $usuario,
      ];
      $res = api_request('POST', $cfg['adjust_stock_path'], [], $payload);
      if (!$res['ok'] || (isset($res['status']) && $res['status'] >= 400)) {
        @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN reverse group nro=".$nro." resp=".json_encode($res)."\n", FILE_APPEND);
      }
    }
  }
} catch (Exception $e) {
  @file_put_contents(__DIR__.'/../almacen2/debug_remote_response.log', date('c')." WARN reverse group exception: ".$e->getMessage()."\n", FILE_APPEND);
}

// Eliminar filas locales
$del = $pdo->prepare("DELETE FROM tb_ventas_externas WHERE nro_venta = :nv");
$del->execute([':nv' => $nro]);

$_SESSION['mensaje1'] = 'Venta externa eliminada';
echo '<script>location.href = "'.$URL.'/ventas";</script>';
exit;
