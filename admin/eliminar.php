<?php

include('../modelo/conexionPDO.php');


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

 
    $sql = 'DELETE FROM productos WHERE id = :id';

    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header('Location: inventario1.php');
    } else {
        echo 'Error al eliminar el producto: ' . $stmt->errorInfo()[2];
    }
} else {
    echo 'ID de producto no válido.';
}


$conn = null;
?>
