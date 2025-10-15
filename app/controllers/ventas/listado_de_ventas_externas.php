<?php

// Lista agrupada por nro_venta desde tb_ventas_externas + cantidad total
// Nota: tb_ventas_externas no tiene nro_venta según la captura; asumimos que se agregará luego si se quiere agrupar.
// Como la estructura actual no incluye nro_venta, listaremos tal cual (todas las filas)

$query = $pdo->prepare("SELECT * FROM tb_ventas_externas ORDER BY id_producto DESC");
$query->execute();
$datos_ventas_externas = $query->fetchAll(PDO::FETCH_ASSOC);
