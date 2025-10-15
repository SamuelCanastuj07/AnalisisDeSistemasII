<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

$id = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
}

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Falta id del producto']);
    exit;
}

$cfg = require __DIR__ . '/config_api.php';
$path = $cfg['available_products_path'];

// Forward any incoming query params (so tests using orden/con_stock match Postman)
$incoming = $_GET;
if (empty($incoming)) {
    // sensible defaults similar to your Postman query
    $incoming = ['con_stock' => 1];
}

// call remote API and filter by id on the PHP side if remote doesn't support single-item fetch
$res = api_request('GET', $path, $incoming);
if (!$res['ok']) {
    http_response_code(502);
    echo json_encode(['success' => false, 'error' => 'Error contactando API remota', 'details' => $res['error']]);
    exit;
}

// Log remote response and incoming params for debugging
$logPath = __DIR__ . '/debug_remote_response.log';
$logEntry = '[' . date('Y-m-d H:i:s') . '] REQUEST ' . json_encode($incoming) . PHP_EOL . var_export($res, true) . PHP_EOL . "----\n";
@file_put_contents($logPath, $logEntry, FILE_APPEND);


$data = [];
if (isset($res['json'])) {
    $j = $res['json'];
    if (isset($j['data']) && is_array($j['data'])) {
        $data = $j['data'];
    } elseif (is_array($j)) {
        $data = $j;
    }
}

$found = null;
foreach ($data as $p) {
    $pid = $p['id_producto'] ?? ($p['id'] ?? null);
    if ($pid == $id) {
        $found = $p;
        break;
    }
}

if (!$found) {
    // build debug info to help determine why Postman finds the product but this script doesn't
    $count = is_array($data) ? count($data) : 0;
    $sample_ids = [];
    if ($count > 0) {
        $max = min(10, $count);
        for ($i = 0; $i < $max; $i++) {
            $pp = $data[$i];
            $sample_ids[] = $pp['id_producto'] ?? ($pp['id'] ?? null);
        }
    }

    $debug = [
        'success' => false,
        'error' => 'Producto no encontrado en API remota',
        'remote_status' => $res['status'] ?? null,
        'received_count' => $count,
        'sample_ids' => $sample_ids,
    ];

    // include a small raw snippet if available
    if (isset($res['raw'])) {
        $debug['raw_snippet'] = substr($res['raw'], 0, 2000);
    } elseif (isset($res['json'])) {
        // include first item sample
        $debug['first_item'] = $data[0] ?? null;
    }

    http_response_code(404);
    echo json_encode($debug);
    exit;
}

$mapped = [
    'id_producto' => $found['id_producto'] ?? ($found['id'] ?? null),
    'nombre' => $found['nombre'] ?? '',
    'stock_total' => $found['stock_total'] ?? ($found['stock'] ?? 0),
    'ubicaciones' => $found['ubicaciones'] ?? [],
];

echo json_encode(['success' => true, 'data' => $mapped]);
exit;
