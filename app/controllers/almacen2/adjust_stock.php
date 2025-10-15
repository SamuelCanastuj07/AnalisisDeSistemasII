<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

// Allow POST with JSON body
$raw = file_get_contents('php://input');
$body = [];
if ($raw) {
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $body = $decoded;
    }
}

// fallback to POST params
$id_producto = isset($body['id_producto']) ? (int)$body['id_producto'] : (isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : null);
$delta = isset($body['delta']) ? (int)$body['delta'] : (isset($_POST['delta']) ? (int)$_POST['delta'] : null);
$reason = isset($body['reason']) ? $body['reason'] : (isset($_POST['reason']) ? $_POST['reason'] : '');

if (!$id_producto || !is_int($delta)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos. Se requiere id_producto y delta (int).']);
    exit;
}

$cfg = require __DIR__ . '/config_api.php';
if (empty($cfg['adjust_stock_path'])) {
    http_response_code(501);
    echo json_encode(['success' => false, 'error' => 'Ajuste remoto no configurado', 'note' => 'Set adjust_stock_path en app/controllers/almacen2/config_api.php si la API remota soporta ajuste de stock']);
    exit;
}

// Proxy request to remote adjust endpoint
$payload = ['id_producto' => $id_producto, 'delta' => $delta, 'reason' => $reason];
$res = api_request('POST', $cfg['adjust_stock_path'], [], $payload);

if (!$res['ok']) {
    http_response_code(502);
    echo json_encode(['success' => false, 'error' => 'Error contactando API remota', 'details' => $res['error']]);
    exit;
}

// Forward remote response
if (isset($res['json'])) {
    http_response_code($res['status'] ?? 200);
    echo json_encode($res['json']);
    exit;
}

http_response_code($res['status'] ?? 200);
echo json_encode(['success' => true, 'raw' => $res['raw'] ?? null]);
exit;
