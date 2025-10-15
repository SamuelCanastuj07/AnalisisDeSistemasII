<?php

include ('../../config.php');

// Admite tanto AJAX (JSON) como envío por formulario con redirect
$id = isset($_POST['id_carrito_externo']) ? $_POST['id_carrito_externo'] : (isset($_GET['id_carrito_externo']) ? $_GET['id_carrito_externo'] : null);

if ($id === null || $id === '') {
    // Respuesta de error
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json', true, 400);
        echo json_encode(['success' => false, 'message' => 'Falta id_carrito_externo']);
        exit;
    } else {
        ?>
        <script>
            alert('Falta id del carrito externo');
            location.href = "<?php echo $URL;?>/ventas2/create.php";
        </script>
        <?php
        exit;
    }
}

try {
    $stmt = $pdo->prepare("DELETE FROM tb_carrito_externo WHERE id_carrito_externo = :id");
    $stmt->bindParam(':id', $id);
    $ok = $stmt->execute();

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
        exit;
    } else {
        ?>
        <script>
            location.href = "<?php echo $URL;?>/ventas2/create.php";
        </script>
        <?php
        exit;
    }
} catch (PDOException $e) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json', true, 500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    } else {
        ?>
        <script>
            alert('No se pudo eliminar: <?php echo addslashes($e->getMessage()); ?>');
            location.href = "<?php echo $URL;?>/ventas2/create.php";
        </script>
        <?php
        exit;
    }
}
