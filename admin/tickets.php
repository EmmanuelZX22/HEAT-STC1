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

#contenedor > div {
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
            <div class="text-center">
                <h1>
                    SISTEMA DE TICKETS
                </h1>
                <br>
                <div class="text-align: justify;">
                    <a>Ingresa al sistema y reporta la falla que presentas actualmente, sera atendida lo antes posible</a>
                </div>
                <br><br>
                <div class="center">
                    <div>
                        <img style="text-align: center;" src="../img/2.png" width="350" height="350">
                    </div>
                </div>
                <br><br>
                <div class="center">
                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="window.open('../osTicket/upload', '_blank')">Sistema de tickets</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="../js/script1.js"></script>
</body>

</html>