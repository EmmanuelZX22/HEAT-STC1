<?php
include('../modelo/conexionPDO.php');

if (isset($_GET['eliminar']) && isset($_GET['id'])) {
    $idActividad = $_GET['id'];

    $sqlEliminar = "UPDATE actividades_reservadas SET estado = 'cancelado' WHERE id = :id";
    $stmt = $conn->prepare($sqlEliminar);
    $stmt->bindParam(':id', $idActividad, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ./Admin_actividades.php");
    exit();
}

$actividades = [];

try {
    $sql = 'SELECT ar.*, h.nombre AS nombre_huesped, h.apellido AS apellido_huesped 
            FROM actividades_reservadas ar
            JOIN huespedes h ON ar.huesped_id = h.id
            WHERE ar.estado != "cancelado"
            ORDER BY ar.id';
    $resultado = $conn->query($sql);

    if ($resultado) {
        $actividades = $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo 'Error al obtener actividades reservadas: ' . $conn->errorInfo()[2];
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo Heat.ico">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h2 {
            color: #007bff;
        }

        .table th, .table td {
            text-align: center;
        }

        .btn-delete {
            color: #ffffff;
            background-color: #dc3545;
            border: none;
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
                            <a href="#">MENU PRINCIPAL</a>
                        </div>
                    </div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-item">
                            <a href="./caja1.php" class="sidebar-link">
                                <i class="lni lni-agenda"></i>
                                <span>Caja</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="./huespedest.php" class="sidebar-link">
                                <i class="lni lni-protection"></i>
                                <span>Huespedes</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="./actividadest.php" class="sidebar-link">
                                <i class="lni lni-protection"></i>
                                <span>Actividades</span>
                            </a>
                        </li>
                    </ul>
                    <div class="sidebar-footer">
                        <a href="../login.php" class="sidebar-link">
                            <i class="lni lni-exit"></i>
                            <span>Cerrar sesión</span>
                        </a>
                    </div>
                </aside>

        <div class="main p-3">
            <div class="container">
                <h2 class="text-center">Administrar Actividades Reservadas</h2>
                <br>
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Huésped</th>
                            <th>Actividad</th>
                            <th>Horario</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Fecha de Reserva</th>
                            <th class="actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actividades as $actividad): ?>
                            <tr>
                                <td><?php echo $actividad['id']; ?></td>
                                <td><?php echo $actividad['nombre_huesped'] . ' ' . $actividad['apellido_huesped']; ?></td>
                                <td><?php echo $actividad['actividad']; ?></td>
                                <td><?php echo $actividad['horario']; ?></td>
                                <td><?php echo $actividad['cantidad']; ?></td>
                                <td><?php echo $actividad['precio']; ?></td>
                                <td><?php echo $actividad['fecha_reserva']; ?></td>
                                <td class="actions">
                                    <a href="javascript:void(0);" onclick="showInfoModal(<?php echo htmlspecialchars(json_encode($actividad)); ?>)" class="btn btn-info">Información</a>
                                    <a href="?eliminar=1&id=<?php echo $actividad['id']; ?>" class="btn btn-delete">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Detalles de la Actividad Reservada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Huésped:</strong> <span id="infoHuesped"></span></p>
                    <p><strong>Actividad:</strong> <span id="infoActividad"></span></p>
                    <p><strong>Horario:</strong> <span id="infoHorario"></span></p>
                    <p><strong>Cantidad:</strong> <span id="infoCantidad"></span></p>
                    <p><strong>Precio:</strong> $<span id="infoPrecio"></span></p>
                    <p><strong>Fecha de Reserva:</strong> <span id="infoFechaReserva"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showInfoModal(actividad) {
            document.getElementById('infoHuesped').innerText = actividad.nombre_huesped + ' ' + actividad.apellido_huesped;
            document.getElementById('infoActividad').innerText = actividad.actividad;
            document.getElementById('infoHorario').innerText = actividad.horario;
            document.getElementById('infoCantidad').innerText = actividad.cantidad;
            document.getElementById('infoPrecio').innerText = actividad.precio;
            document.getElementById('infoFechaReserva').innerText = actividad.fecha_reserva;

            new bootstrap.Modal(document.getElementById('infoModal')).show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script1.js"></script>
</body>

</html>
