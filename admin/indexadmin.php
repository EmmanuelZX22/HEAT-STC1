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
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: -18px;
        }

        h1 {
            color: #007bff;
        }

        .center {
            display: flex;
            justify-content: center;
        }

        #contenedor {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }

        #contenedor>div {
            width: 50%;
        }

        .sidebar-footer a {
            color: #ffffff;
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
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <div class="main p-3">
            <div class="container text-center">
                <h1>Bienvenido al Soporte Técnico de STC</h1>
                <br>
                <div style="text-align: justify;">
                    <p>El sistema está diseñado para gestionar de manera eficiente tanto el inventario como la administración de los huéspedes, brindando control total sobre los recursos del hotel. Además, permite la creación y seguimiento de tickets relacionados con fallas o problemas en cualquier sistema informático del hotel. Esto incluye desde la detección temprana de incidentes hasta el monitoreo y resolución de los mismos, proporcionando una herramienta integral que facilita la administración operativa y técnica del hotel, asegurando que todos los sistemas funcionen de manera óptima para mejorar la experiencia de los huéspedes y la eficiencia del personal.</p>
                </div>
                <br><br>
                <div class="center">
                    <img src="../img/1.png" width="350" height="350" alt="Imagen de Soporte Técnico">
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../js/script1.js"></script>
</body>

</html>
