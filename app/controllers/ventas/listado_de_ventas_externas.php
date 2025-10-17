<?php

// Intentar agrupar ventas externas por nro_venta e id_cliente
try {
	// Verificar si existen las columnas esperadas; si no existen, caemos a listado simple
	$cols = $pdo->query("SHOW COLUMNS FROM tb_ventas_externas")->fetchAll(PDO::FETCH_COLUMN, 0);
	$haveNro = in_array('nro_venta', $cols);
	$haveCli = in_array('id_cliente', $cols);

	if ($haveNro && $haveCli) {
	$sql = "SELECT ve.nro_venta, ve.id_cliente, 
		       SUM(CAST(ve.stock_total AS DECIMAL(18,2))) AS cantidad_total,
		       MAX(ve.precio) AS monto_total,
					   COUNT(*) AS items
				FROM tb_ventas_externas ve
				GROUP BY ve.nro_venta, ve.id_cliente
				ORDER BY MIN(ve.id_producto) DESC";
		$st = $pdo->prepare($sql);
		$st->execute();
		$ventas_ext_grupo = $st->fetchAll(PDO::FETCH_ASSOC);

		// Enriquecer con nombre de cliente
		$mapClientes = [];
		if (!empty($ventas_ext_grupo)) {
			$ids = array_unique(array_filter(array_map(function($r){ return (int)$r['id_cliente']; }, $ventas_ext_grupo)));
			if ($ids) {
				$in = implode(',', array_map('intval', $ids));
				$qC = $pdo->query("SELECT id_cliente, nombre_cliente FROM tb_clientes WHERE id_cliente IN ($in)");
				foreach ($qC->fetchAll(PDO::FETCH_ASSOC) as $c) {
					$mapClientes[(int)$c['id_cliente']] = $c['nombre_cliente'];
				}
			}
		}

		$datos_ventas_externas = [
			'modo' => 'agrupado',
			'grupos' => $ventas_ext_grupo,
			'clientes' => $mapClientes,
		];
	} else {
		// Fallback: listado simple existente
		$query = $pdo->prepare("SELECT * FROM tb_ventas_externas ORDER BY id_producto DESC");
		$query->execute();
		$datos = $query->fetchAll(PDO::FETCH_ASSOC);
		$datos_ventas_externas = [
			'modo' => 'simple',
			'filas' => $datos,
		];
	}
} catch (Exception $e) {
	// Último recurso: listado simple
	$query = $pdo->prepare("SELECT * FROM tb_ventas_externas ORDER BY id_producto DESC");
	$query->execute();
	$datos_ventas_externas = [
		'modo' => 'simple',
		'filas' => $query->fetchAll(PDO::FETCH_ASSOC),
	];
}
