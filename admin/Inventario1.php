<?php

include('../modelo/conexionPDO.php');

$sql = 'SELECT * FROM productos ORDER BY id';
$resultado = $conn->query($sql);

if ($resultado) {

    $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo 'Error al obtener productos: ' . $conn->errorInfo()[2];
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

        .btn-edit {
            color: #ffffff;
            background-color: #28a745;
            border: none;
        }

        .btn-delete {
            color: #ffffff;
            background-color: #dc3545;
            border: none;
        }
        /* Estilo para filas con cantidad igual a 1 */
        .low-stock {
            background-color: #f8d7da;
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
            <div class="container mt-4">
                <div class="text-center">
                    <h2>Administrar Productos</h2>
                </div>
            
                <br>
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Imagen</th>
                            <th class="actions">Acciones</th>
                            <th>Provedor</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                        <?php
                        $fila = 1; 
                        foreach ($productos as $producto): 
                            $rowClass = $producto['cantidad'] == 1 ? 'low-stock' : '';
                        ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo $fila++; ?></td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td><?php echo $producto['descripcion']; ?></td>
                                <td>$<?php echo $producto['precio']; ?></td>
                                <td><?php ['cantidad']; ?>
                                <form action="actualizar_cantidad.php" method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                    <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" min="0" style="width: 80px;">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </form>
                                </td>
                                <td><img src="<?php echo $producto['imagen']; ?>" alt="Imagen del Producto" style="max-width: 100px;"></td>
                                <td class="actions">
                                    <a href="editar1.php?id=<?php echo $producto['id']; ?>" class="btn btn-success btn-edit">Editar</a>
                                    <a href="eliminar.php?id=<?php echo $producto['id']; ?>" class="btn btn-danger btn-delete">Eliminar</a>
                                </td>
                                <td>
                                <a href="https://www.sams.com.mx/?utm_source=google&utm_medium=cpc&utm_advertiser=sams_brand&utm_campaign=sams_lf_sem_ecomm_brand_exacta&utm_term=sams_e&utm_content=&gad_source=1&gclid=Cj0KCQiA_qG5BhDTARIsAA0UHSLm9imgAt2hF9JHucD3bysv04nJ4qjWeMtuiDKZiSOvI0aXT2DIlEUaAjyiEALw_wcB" target="_blank">
                                    <button type="button" class="btn btn-primary">SAMS</button>
                                </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


                <a href="agregar.php" class="btn btn-success">Agregar Producto</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="../js/script1.js"></script>
</body>

</html>