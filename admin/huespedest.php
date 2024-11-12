<?php
include('../modelo/conexionPDO.php');

if (isset($_GET['eliminar']) && isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    $sqlBorrar = "DELETE FROM huespedes WHERE id = :id"; // Cambiar "t_huespedes" a "huespedes"
    $stmt = $conn->prepare($sqlBorrar);
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    $conn = null;

    header("Location: huespedes.php");
    exit();
}

$sql = 'SELECT * FROM huespedes ORDER BY id'; // Cambiar "t_huespedes" a "huespedes"
$resultado = $conn->query($sql);

if ($resultado) {
    $huespedes = $resultado->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo 'Error al obtener huéspedes: ' . $conn->errorInfo()[2];
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
        <h2 class="text-center">Administrar Huéspedes</h2>
                <br>
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Fecha Entrada</th>
                            <th>Fecha Salida</th>
                            <th>Cantidad Disponible</th>
                            <th>Habitación ID</th>
                            <th>Gasto Total</th>
                            <th class="actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $contadorId = 1;
                        foreach ($huespedes as $huesped): ?>
                            <tr>
                                <td><?php echo $huesped['id']; ?></td>
                                <td><?php echo $huesped['nombre']; ?></td>
                                <td><?php echo $huesped['apellido']; ?></td>
                                <td><?php echo $huesped['email']; ?></td>
                                <td><?php echo $huesped['fecha_entrada']; ?></td>
                                <td><?php echo $huesped['fecha_salida']; ?></td>
                                <td><?php echo $huesped['cantidad_disponible']; ?></td>
                                <td><?php echo $huesped['habitacion_id']; ?></td>
                                <td><?php echo $huesped['gasto_total']; ?></td>
                                <td class="actions">
                                    <a href="javascript:void(0);" onclick="showInfoModal(<?php echo htmlspecialchars(json_encode($huesped)); ?>)" class="btn btn-info">Información</a>                                </td>
                            </tr>
                        <?php
                    $contadorId++; 
                endforeach; ?>
            </tbody>
        </table>
    </div>
        </div>
    </div>

    <!-- Modal de Información -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Detalles del Huésped</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="infoNombre"></span></p>
                    <p><strong>Apellido:</strong> <span id="infoApellido"></span></p>
                    <p><strong>Email:</strong> <span id="infoEmail"></span></p>
                    <p><strong>Fecha de Entrada:</strong> <span id="infoFechaEntrada"></span></p>
                    <p><strong>Fecha de Salida:</strong> <span id="infoFechaSalida"></span></p>
                    <p><strong>Habitación ID:</strong> <span id="infoHabitacionId"></span></p>
                    <p><strong>Cantidad Disponible:</strong> <span id="infoCantidadDisponible"></span></p>
                    <p><strong>Gasto Total:</strong> $<span id="infoGastoTotal"></span></p>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="generarPDF(<?php echo $huesped['id']; ?>)">Generar reporte completo</button>
                </div>
            </div>
        </div>
    </div>

<script>
    // Función para mostrar el modal de Información con los datos del huésped
function showInfoModal(huesped) {
    document.getElementById('infoNombre').innerText = huesped.nombre;
    document.getElementById('infoApellido').innerText = huesped.apellido;
    document.getElementById('infoEmail').innerText = huesped.email;
    document.getElementById('infoFechaEntrada').innerText = huesped.fecha_entrada;
    document.getElementById('infoFechaSalida').innerText = huesped.fecha_salida;
    document.getElementById('infoHabitacionId').innerText = huesped.habitacion_id;
    document.getElementById('infoCantidadDisponible').innerText = huesped.cantidad_disponible;
    document.getElementById('infoGastoTotal').innerText = huesped.gasto_total;

    // Mostrar el modal de Información
    new bootstrap.Modal(document.getElementById('infoModal')).show();
}
</script>
<script>
    function generarPDF(huespedId) {
        window.open('./generar_reporte.php?huesped_id=' + huespedId, '_blank');
    }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="../js/script1.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>