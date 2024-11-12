<?php

include('../modelo/conexionPDO.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen_actual = $_POST['imagen_actual'];

    
    $imagen = $_FILES['imagen'];
    $nombreImagen = $imagen['name'];
    $rutaDestino = '../ruta/destino/';
    $rutaImagen = $rutaDestino . $nombreImagen;

    
    if (!file_exists($rutaDestino)) {
        mkdir($rutaDestino, 0777, true);
    }

    
    if (!empty($nombreImagen)) {
        move_uploaded_file($imagen['tmp_name'], $rutaImagen);
    } else {
        
        $rutaImagen = $imagen_actual;
    }

    
    $sql = 'UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, imagen = :imagen WHERE id = :id';

 
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':imagen', $rutaImagen);

    if ($stmt->execute()) {
        header('Location: inventario1.php');
    } else {
        echo 'Error al actualizar el producto: ' . $stmt->errorInfo()[2];
    }
} else {
   
    header('Location: admin.php');
    exit();
}


$conn = null;
?>
