API ligera para `almacen2`

Rutas (archivos PHP):

- `app/controllers/almacen2/listado_de_productos.php`  -> GET -> Lista todos los productos (JSON)
- `app/controllers/almacen2/mostrar_producto.php?id={id}` -> GET -> Muestra un producto por id (JSON)
- `app/controllers/almacen2/adjust_stock.php` -> POST -> Ajusta stock. Acepta JSON o form-data: { id_producto, delta, reason }

Ejemplos curl (Windows PowerShell):

# Listar productos
curl 'http://localhost/proyecto%20analisis%20C-503/app/controllers/almacen2/listado_de_productos.php'

# Mostrar producto
curl 'http://localhost/proyecto%20analisis%20C-503/app/controllers/almacen2/mostrar_producto.php?id=25'

# Ajustar stock (sumar 5)
curl -Method POST -Uri 'http://localhost/proyecto%20analisis%20C-503/app/controllers/almacen2/adjust_stock.php' -Body (ConvertTo-Json @{ id_producto=1; delta=5; reason='compra' }) -ContentType 'application/json'

# Ajustar stock (restar 3) usando curl clásico
curl -X POST -H "Content-Type: application/json" -d "{\"id_producto\":1,\"delta\":-3,\"reason\":\"venta\"}" "http://localhost/proyecto%20analisis%20C-503/app/controllers/almacen2/adjust_stock.php"

Notas:
- `adjust_stock.php` usa transacciones y `SELECT ... FOR UPDATE` para evitar condiciones de carrera. No permitirá que el stock sea negativo (responde 409).
- Si existe una tabla `tb_movimientos` se intentará insertar un registro de auditoría; si no existe, la operación continúa sin falla.

Endpoint remoto usado por defecto:

- https://api-inventario.up.railway.app/api/commerce/products/available/with-locations?con_stock=1&orden=stock_asc

Forma de los objetos recibidos desde la API remota (esperado):

- id_producto
- nombre
- stock_total
- ubicaciones: array de objetos con campos:
	- id_ubicacion
	- nombre
	- stock

Configuración:

1) Si la API remota es accesible tal como está, no necesitas cambiar nada. `listado_de_productos.php` y `mostrar_producto.php` llamarán al endpoint configurado en `app/controllers/almacen2/config_api.php`.

2) Si la API remota ofrece un endpoint para ajustar stock, establece `adjust_stock_path` en `app/controllers/almacen2/config_api.php` con la ruta relativa (por ejemplo `/api/commerce/products/adjust-stock`). Si no existe, `adjust_stock.php` devolverá 501 (Not Implemented).

3) Si la API remota requiere autenticación, rellena `api_key` en `config_api.php` con el valor apropiado. Pon el valor exactamente como debe ir en el header `Authorization`, por ejemplo:

```
'Bearer eyJhbGciOiJI...'
```

El cliente `api_client.php` añadirá ese valor al header `Authorization` en cada petición.

Ejemplo: obtener lista (filtrada por lugares con stock)
curl "https://api-inventario.up.railway.app/api/commerce/products/available/with-locations?con_stock=1&orden=stock_asc"

