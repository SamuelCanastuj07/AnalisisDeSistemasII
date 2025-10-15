<?php
// Populate $datos_productos for the almacen2 view by calling the remote API
require_once __DIR__ . '/api_client.php';

$cfg = require __DIR__ . '/config_api.php';
$path = $cfg['available_products_path'];

$res = api_request('GET', $path, ['con_stock' => 1, 'orden' => 'stock_asc']);

$datos_productos = [];
if ($res['ok'] && isset($res['json'])) {
    $j = $res['json'];
    // try to extract list from common wrappers
    $items = [];
    if (isset($j['data']) && is_array($j['data'])) {
        if (isset($j['data']['productos'])) $items = $j['data']['productos'];
        else $items = $j['data'];
    } elseif (isset($j['productos'])) {
        $items = $j['productos'];
    } elseif (is_array($j)) {
        $items = $j;
    }

    foreach ($items as $p) {
        if (!is_array($p)) continue;
        $id = $p['id_producto'] ?? ($p['id'] ?? null);
        $nombre = $p['nombre'] ?? '';
    $stock_total = $p['stock_total'] ?? ($p['stock'] ?? 0);
    $precio = $p['precio'] ?? ($p['precio_venta'] ?? 0);
        // prepare ubicaciones JSON string for possible use
        $ubicaciones = $p['ubicaciones'] ?? [];

        $datos_productos[] = [
            'id_producto' => $id,
            'codigo' => $id,
            'id_categoria' => null,
            'nombre_categoria' => 'Externo',
            'nombre' => $nombre,
            'descripcion' => '',
            'stock' => $stock_total,
            'precio_venta' => $precio,
            'imagen' => '',
            'ubicaciones' => $ubicaciones,
        ];
    }
}
