<?php

include('../modelo/conexionPDO.php');

if (isset($_GET['eliminar']) && isset($_GET['id'])) {
    $idUsuario = $_GET['id'];
    
    $sqlBorrar = "DELETE FROM t_usuarios WHERE id = :id";
    $stmt = $conn->prepare($sqlBorrar);
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    
    $conn = null;

    header("Location: ./huespedes1.php"); 
    exit();
}

if (isset($_GET['convertir']) && isset($_GET['id'])) {
    $idUsuario = $_GET['id'];
    
    $sqlConvertir = "UPDATE t_usuarios SET es_administrador = 1 WHERE id = :id";
    $stmt = $conn->prepare($sqlConvertir);
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    $conn = null;

    header("Location: ./huespedes1.php");
    exit();
}

if (isset($_GET['quitar']) && isset($_GET['id'])) {
    $idUsuario = $_GET['id'];
    
    $sqlQuitar = "UPDATE t_usuarios SET es_administrador = 0 WHERE id = :id";
    $stmt = $conn->prepare($sqlQuitar);
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    $conn = null;

    header("Location: ./huespedes1.php");
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEAT - STC</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo Heat.ico">
    <style>
        #contenedor {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }

        #contenedor > div {
            width: 50%;
        }
        
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
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

        .btn-delete, .btn-admin, .btn-remove-admin {
            color: #ffffff;
            border: none;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-admin {
            background-color: #007bff;
        }

        .btn-remove-admin {
            background-color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">MENU PRINCIAL</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="./indexadmin.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="./inventario1.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Inventarios</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="./huespedes.php" class="sidebar-link">
                        <i class="lni lni-protection"></i>
                        <span>Huespedes</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="./Admin_actividades.php" class="sidebar-link">
                        <i class="lni lni-protection"></i>
                        <span>Actividades</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="./huespedes1.php" class="sidebar-link">
                        <i class="lni lni-protection"></i>
                        <span>Usuarios del sistema</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="./tickets.php" class="sidebar-link">
                        <i class="lni lni-layout"></i>
                        <span>Tickets</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="../login.php" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Cerrar sesion</span>
                </a>
            </div>
        </aside>
        <div class="main p-3">
            <div class="container">
                <h2 class="text-center">Administrar usuarios del sistema</h2>
                <br>
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Correo</th>
                            <th>Nombre Usuario</th>
                            <th>Apellido Materno</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Administrador</th>
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
                                <td><?php echo $usuario['es_administrador']; ?></td>
                                <td class="actions">
                                    <a href="?eliminar=true&id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-delete">Eliminar</a>
                                    <a href="?convertir=true&id=<?php echo $usuario['id']; ?>" class="btn btn-primary btn-admin">Admin</a>
                                    <a href="?quitar=true&id=<?php echo $usuario['id']; ?>" class="btn btn-secondary btn-remove-admin">Quitar</a>
                                </td>
                            </tr>
                        <?php
                            $contadorId++;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="../js/script1.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>
