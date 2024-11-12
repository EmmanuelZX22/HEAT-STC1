<?php


include('../modelo/conexionPDO.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad']; 

    
    $imagen = $_FILES['imagen'];
    $nombreImagen = $imagen['name'];
    $rutaDestino = '../ruta/destino/';
    $rutaImagen = $rutaDestino . $nombreImagen;

    
    if (!file_exists($rutaDestino)) {
        mkdir($rutaDestino, 0777, true);
    }

    
    if (move_uploaded_file($imagen['tmp_name'], $rutaImagen)) {
        
        $sql = 'INSERT INTO productos (nombre, descripcion, precio, imagen, cantidad) VALUES (:nombre, :descripcion, :precio, :imagen, :cantidad)';

        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':imagen', $rutaImagen);
        $stmt->bindParam(':cantidad', $cantidad);

        if ($stmt->execute()) {
            header('Location: inventario1.php');
        } else {
            echo 'Error al agregar el producto: ' . $stmt->errorInfo()[2];
        }
    } else {
        echo 'Error al mover la imagen al directorio de destino.';
    }
} else {
    
    header('Location: agregar.php');
    exit();
}


$conn = null;
?>
