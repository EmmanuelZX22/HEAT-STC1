<?php


include('../modelo/conexionPDO.php');

if (isset($_GET['eliminar']) && isset($_GET['id'])) {
    $idUsuario = $_GET['id'];
    
    include('../modelo/conexionPDO.php');

    $sqlBorrar = "DELETE FROM t_usuarios WHERE id = :id";
    $stmt = $conn->prepare($sqlBorrar);
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    

    $conn = null;

    header("Location: adminUsuarios.php"); 
    exit();
}


$sql = 'SELECT * FROM t_usuarios ORDER BY id';
$resultado = $conn->query($sql);


if ($resultado) {
    
    $usuarios = $resultado->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo 'Error al obtener usuarios: ' . $conn->errorInfo()[2];
}


$conn = null;
?>

<!-- adminUsuarios.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Usuarios</title>
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
            margin-top: 50px; /* Ajusta el margen superior según sea necesario */
        }

        h2 {
            color: #007bff;
        }

        .table th, .table td {
            text-align: center;
        }

        .table th.actions, .table td.actions {
            width: 150px;
        }

        .btn-delete {
            color: #ffffff;
            background-color: #dc3545;
            border: none;
        }
    </style>
</head>
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

    <div class="container">
        <br><br><br>
        <h2>Administrar Usuarios</h2>

      
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Correo</th>
                    <th>Nombre Usuario</th>
                    <th>Apellido Materno</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th class="actions">Acciones</th>
                </tr>
            </thead>
            <tbody>
                
                <?php
                $contadorId = 1; 
                foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $contadorId; ?></td>
                        <td><?php echo $usuario['correo']; ?></td>
                        <td><?php echo $usuario['nombreUsuario']; ?></td>
                        <td><?php echo $usuario['aMaterno']; ?></td>
                        <td><?php echo $usuario['direccion']; ?></td>
                        <td><?php echo $usuario['telefono']; ?></td>
                        <td class="actions">
                            <a href="?eliminar=true&id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-delete">Eliminar</a>
                        </td>
                    </tr>
                <?php
                    $contadorId++; 
                endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="js/bootstrap.min.js"></script>
</body>
</html>
