<?php

require_once('../modelo/conexionPDO.php');


$sql = 'SELECT * FROM datos_compra ORDER BY id';

try {
    $resultado = $conn->query($sql);

    
    if ($resultado) {
        
        $compras = $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo 'Error al obtener datos de compra: ' . $conn->errorInfo()[2];
    }
} catch (Exception $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
    exit();
}


$conn = null;
?>

<!-- admin.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
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

        .table th, .table td {
            text-align: center;
        }

        .table th.actions, .table td.actions {
            width: 200px;
        }

        .btn-delete, .btn-echo {
            color: #ffffff;
            border: none;
            margin: 5px;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-echo {
            background-color: #007bff;
        }
    </style>
</head>
<br><br><br><br><br><br>
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
        <h2>Administrar Compras</h2>


<table class="table table-bordered table-hover">
    <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>Dirección de Entrega</th>
            <th>Nombre</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Productos</th>
            <th>Total del Carrito</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        $fila = 1; 
        foreach ($compras as $compra): ?>
            <tr>
                <td><?php echo $fila++; ?></td>
                <td><?php echo $compra['direccion_entrega']; ?></td>
                <td><?php echo $compra['nombre']; ?></td>
                <td><?php echo $compra['apellido_paterno']; ?></td>
                <td><?php echo $compra['apellido_materno']; ?></td>
                <td>
                    <?php
                        
                        $productos = json_decode($compra['productos_json'], true);
                        
                        
                        foreach ($productos as $producto) {
                            echo '<div>';
                            echo '<img src="' . $producto['imagen'] . '" alt="Imagen del Producto" style="max-width: 100px; margin-right: 5px;">';
                            echo 'Nombre: ' . $producto['nombre'] . '<br>';
                            
                            
                            if (isset($producto['cantidad'])) {
                                echo 'Cantidad: ' . $producto['cantidad'] . '<br>';
                            } else {
                                echo 'Cantidad: No especificada<br>';
                            }
                            
                            echo '</div>';
                        }
                    ?>
                </td>
                <td>$<?php echo $compra['total_carrito']; ?></td>
                <td class="actions">
                    <button class="btn btn-danger btn-delete" onclick="eliminarCompra(<?php echo $compra['id']; ?>)">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    </div>

    <script src="js/bootstrap.min.js"></script>
    <script>
    
    function eliminarCompra(id) {
        
        if (confirm('¿Está seguro de que desea eliminar este pedido?')) {
            
            fetch('eliminar_pedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    
                    alert('Pedido eliminado correctamente');
                    location.reload(); 
                } else {
                    
                    alert('Error al eliminar el pedido: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }
</script>
</body>
</html>
