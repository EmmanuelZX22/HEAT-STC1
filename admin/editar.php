<?php
// editar.php

include('../modelo/conexionPDO.php');


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    
    $sql = 'SELECT * FROM productos WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    
    $conn = null;

    if (!$producto) {
        echo 'Producto no encontrado.';
        exit();
    }
} else {
    echo 'ID de producto no válido.';
    exit();
}
?>

<!-- agregar.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Panel de Administrador</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style1.css">
    <link rel="icon" type="image/x-icon" href="../img/logo3.ico">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007bff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        .btn-submit {
            color: #ffffff;
            background-color: #007bff;
            border: none;
        }
    </style>
</head>

<br><br><br><br><br>

<body>
    <header class="navar1">
        <div class="logo">
            <img src="../img/logo3.png" alt="logotipo">
        </div>
        <nav>
            <ul class="link">
                <li><a href="administrador.php">PRODUCTOS</a></li>
                <li><a href="adminUsuarios.php">USUARIOS</a></li>
                <li><a href="pedidos.php">PEDIDOS</a></li>
                <li><a href="../login.php">CERRAR SESION</a></li>
            </ul>
        </nav>
    </header>

     <div class="container mt-4">
        <h2>Editar Producto</h2>

        
        <form action="procesar_editar.php" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <div class="form-group">
                <label for="nombre">Nombre del Producto</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción del Producto</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo $producto['descripcion']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio del Producto</label>
                <input type="number" class="form-control" id="precio" name="precio" value="<?php echo $producto['precio']; ?>" required>
            </div>
            <div class="form-group">
                <label for="imagen">Nueva Imagen del Producto</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            </div>
            <input type="hidden" name="imagen_actual" value="<?php echo $producto['imagen']; ?>">
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
        </form>
    </div>

</body>
</html>
