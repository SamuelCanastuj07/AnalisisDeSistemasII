<?php
/**
 * Simple HTTP client for the remote inventory API used by almacen2.
 */
$cfg = require __DIR__ . '/config_api.php';

function api_request($method, $path, $query = [], $body = null) {
    global $cfg;

    $base = rtrim($cfg['base_url'], '/');
    $url = $base . $path;
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $cfg['timeout']);

    $headers = ['Accept: application/json'];
    if (!empty($cfg['api_key'])) {
        // if api_key stored as full header value (e.g. 'Bearer x..'), send it as Authorization
        $headers[] = 'Authorization: ' . $cfg['api_key'];
    }

    if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH' || $method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($body !== null) {
            $payload = is_string($body) ? $body : json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers[] = 'Content-Type: application/json';
        }
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $errno = curl_errno($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($errno) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok' => false, 'error' => $err];
    }

    curl_close($ch);

    $decoded = json_decode($resp, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // return raw response if not JSON
        return ['ok' => true, 'status' => $status, 'raw' => $resp];
    }

    return ['ok' => true, 'status' => $status, 'json' => $decoded];
}
