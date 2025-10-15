<?php
// Configuración para el cliente de la API remota (almacen externo)
// Ajusta estos valores según la API que te pasaron
return [
    // Base URL sin trailing slash (dominio/host). NO incluir paths ni query params aquí.
    'base_url' => 'https://api-inventario.up.railway.app',

    // Endpoint para obtener productos con ubicaciones (relativo a base_url)
    'available_products_path' => '/api/commerce/products/available/with-locations',

    // Configurar endpoint para ajustar stock remoto si existe. Si la API remota no expone ajuste, dejar null.
    // Ejemplo: '/api/commerce/products/adjust-stock' (depende de la API real)
    'adjust_stock_path' => '/api/inventory/adjust',

    // Si la API requiere api key o Bearer token, define aquí (opcional)
    // Usa el token completo tal cual lo pones en Postman, por ejemplo: 'Bearer eyJhbGciOi...'
    'api_key' => null, // ejemplo: 'Bearer eyJhbGciOi...'

    // Timeout en segundos
    'timeout' => 10,
];
