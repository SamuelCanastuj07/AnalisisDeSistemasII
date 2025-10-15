<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

// Default query params exactly like Postman screenshot
$defaults = ['con_stock' => 1, 'orden' => 'stock_asc'];

// Merge incoming GET params (they override defaults)
$incoming = array_merge($defaults, $_GET ?: []);

$cfg = require __DIR__ . '/config_api.php';
$path = $cfg['available_products_path'];

$res = api_request('GET', $path, $incoming);
if (!$res['ok']) {
    http_response_code(502);
    echo json_encode(['success' => false, 'error' => 'Error contactando API remota', 'details' => $res['error']]);
    exit;
}

// If remote returned JSON, forward it exactly as-is to match Postman
if (isset($res['json'])) {
    // send same HTTP status code returned by remote (or 200)
    http_response_code($res['status'] ?? 200);
    echo json_encode($res['json']);
    exit;
}

// Otherwise return raw body
http_response_code($res['status'] ?? 200);
echo $res['raw'] ?? '';
exit;

