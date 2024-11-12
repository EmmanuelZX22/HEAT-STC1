<?php
// Incluir conexión a la base de datos
include '../modelo/conexionPDO.php';

// Obtener las habitaciones disponibles
$stmt = $conn->query("SELECT id, tipo, precio, cantidad_disponible FROM habitaciones WHERE cantidad_disponible > 0");
$habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si se envió el formulario para agregar huésped
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nombre = $_POST['name'];
    $apellido = $_POST['lastName'];
    $email = $_POST['email'];
    $fechaEntrada = $_POST['checkInDate'];
    $fechaSalida = $_POST['checkOutDate'];

    // Validación de claves en el arreglo $_POST
    $habitacionId = $_POST['habitacion'] ?? null;
    $cantidadHabitaciones = $_POST['cantidadHabitaciones'] ?? null;

    try {
        // Verificar si el correo ya está registrado
        $stmt = $conn->prepare("SELECT COUNT(*) FROM huespedes WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $existeCorreo = $stmt->fetchColumn();

        if ($existeCorreo > 0) {
            $mensaje = 'Este correo ya ha sido registrado previamente.';
        } else {
            // Verificar disponibilidad de la habitación y obtener su precio
            $stmt = $conn->prepare("SELECT precio, cantidad_disponible FROM habitaciones WHERE id = :habitacion_id");
            $stmt->execute([':habitacion_id' => $habitacionId]);
            $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($habitacion && $habitacion['cantidad_disponible'] >= $cantidadHabitaciones) {
                // Calcular el número de noches
                $fechaEntradaDate = new DateTime($fechaEntrada);
                $fechaSalidaDate = new DateTime($fechaSalida);
                $diferencia = $fechaEntradaDate->diff($fechaSalidaDate);
                $numeroNoches = $diferencia->days; // Días de diferencia

                // Calcular el precio total multiplicando el precio de la habitación por el número de noches y la cantidad de habitaciones
                $precioTotal = $habitacion['precio'] * $numeroNoches * $cantidadHabitaciones;

                // Insertar la reserva con el precio total
                $sql = "INSERT INTO huespedes (nombre, apellido, email, fecha_entrada, fecha_salida, habitacion_id, cantidad_disponible, precio_h) 
                        VALUES (:nombre, :apellido, :email, :fecha_entrada, :fecha_salida, :habitacion_id, :cantidad_disponible, :precio_total)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':email' => $email,
                    ':fecha_entrada' => $fechaEntrada,
                    ':fecha_salida' => $fechaSalida,
                    ':habitacion_id' => $habitacionId,
                    ':cantidad_disponible' => $cantidadHabitaciones,
                    ':precio_total' => $precioTotal
                ]);

                // Actualizar la cantidad disponible en la tabla de habitaciones
                $stmt = $conn->prepare("UPDATE habitaciones SET cantidad_disponible = cantidad_disponible - :cantidad 
                                        WHERE id = :habitacion_id");
                $stmt->execute([
                    ':cantidad' => $cantidadHabitaciones,
                    ':habitacion_id' => $habitacionId
                ]);

                $mensaje = 'Reserva registrada exitosamente. Total a pagar: $' . $precioTotal;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $mensaje = 'No hay suficientes habitaciones disponibles.';
            }
        }
    } catch (PDOException $e) {
        $mensaje = 'Error al registrar la reserva: ' . $e->getMessage();
    }
}


// Verificar si se envió el formulario para eliminar huésped (Check-Out)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $huespedId = $_POST['huespedId'];

    try {
        // Obtener la cantidad de habitaciones del huésped
        $stmt = $conn->prepare("SELECT cantidad_disponible, habitacion_id FROM huespedes WHERE id = :huespedId");
        $stmt->execute([':huespedId' => $huespedId]);
        $huesped = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($huesped) {
            // Actualizar la disponibilidad de habitaciones en la tabla habitaciones
            $stmt = $conn->prepare("UPDATE habitaciones SET cantidad_disponible = cantidad_disponible + :cantidad 
                                    WHERE id = :habitacion_id");
            $stmt->execute([
                ':cantidad' => $huesped['cantidad_disponible'],
                ':habitacion_id' => $huesped['habitacion_id']
            ]);

            // Eliminar al huésped
            $stmt = $conn->prepare("DELETE FROM huespedes WHERE id = :huespedId");
            $stmt->execute([':huespedId' => $huespedId]);

            $mensaje = 'Check-Out realizado exitosamente.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $mensaje = 'Huésped no encontrado.';
        }
    } catch (PDOException $e) {
        $mensaje = 'Error al procesar el Check-Out: ' . $e->getMessage();
    }
}

// Código adicional para obtener datos del huésped para el modal de pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'getHuespedData') {
    $huespedId = $_POST['huespedId'];
    try {
        // Obtener los datos del huésped, incluyendo Precio_H
        $stmt = $conn->prepare("SELECT h.id, h.nombre, h.apellido, h.email, h.cantidad_disponible, h.precio_h AS precio_h, hab.tipo 
                                FROM huespedes h
                                JOIN habitaciones hab ON h.habitacion_id = hab.id
                                WHERE h.id = :huespedId");
        $stmt->execute([':huespedId' => $huespedId]);
        $huesped = $stmt->fetch(PDO::FETCH_ASSOC);

        // Devolver los datos como JSON
        echo json_encode($huesped);

        // Verificar si el campo precio_h existe y tiene valor
        if ($huesped && isset($huesped['precio_h'])) {
            echo json_encode($huesped); // Si existe, envía el JSON normalmente
        } else {
            // Mensaje de error en caso de que no se obtenga precio_h
            echo json_encode(['error' => 'precio_h no encontrado en los datos del huésped.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al obtener los datos del huésped: ' . $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
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
    background-color: #e0f7fa;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.container {
    max-width: 1200px; /* Ancho máximo del contenedor */
    margin: auto; /* Centrar el contenedor */
    padding: 20px; /* Espaciado interno */
}

.header {
    background-color: #b2dfdb;
    padding: 10px;
    border-radius: 5px;
    text-align: center; /* Centrar el texto */
}

.table-container {
    max-height: 400px; /* Altura máxima del contenedor de la tabla */
    overflow-y: auto; /* Permitir desplazamiento vertical */
    border: 1px solid #ddd; /* Borde alrededor del contenedor */
    border-radius: 5px; /* Bordes redondeados */
    margin-top: 20px; /* Espaciado superior */
}

.table {
    width: 100%; /* Asegurarse de que la tabla ocupe todo el ancho */
    border-collapse: collapse; /* Colapsar bordes */
}

.table th, .table td {
    padding: 10px; /* Espaciado interno de celdas */
    text-align: left; /* Alinear texto a la izquierda */
    border: 1px solid #ddd; /* Bordes de las celdas */
}

.table th {
    background-color: #f2f2f2; /* Color de fondo para encabezados */
}
.btn {
    width: 100%; /* Botones ocupan todo el ancho */
}

.modal-body {
    overflow-y: auto; /* Permitir desplazamiento vertical en el modal si es necesario */
}

@media (max-width: 768px) {
    .header {
        font-size: 1.2em; /* Ajustar tamaño de fuente en pantallas pequeñas */
    }

    .table th, .table td {
        font-size: 0.9em; /* Ajustar tamaño de fuente en pantallas pequeñas */
    }

    .btn {
        font-size: 0.9em; /* Ajustar tamaño de fuente de botones en pantallas pequeñas */
    }
}
.low-stock {
            background-color: #f8d7da;
        }
    </style>
</head>

<!-- Modal de Check-Out -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">Detalles de Check-Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="checkoutForm" method="POST">
                    <input type="hidden" name="action" value="checkout">
                    <input type="hidden" id="checkoutHuespedId" name="huespedId">
                    
                    <p><strong>Nombre:</strong> <span id="checkoutNombre"></span></p>
                    <p><strong>Apellido:</strong> <span id="checkoutApellido"></span></p>
                    <p><strong>Email:</strong> <span id="checkoutEmail"></span></p>
                    <p><strong>Fecha de Entrada:</strong> <span id="checkoutFechaEntrada"></span></p>
                    <p><strong>Fecha de Salida:</strong> <span id="checkoutFechaSalida"></span></p>
                    <p><strong>Habitación:</strong> <span id="checkoutHabitacion"></span></p>
                    <p><strong>Cantidad de Habitaciones:</strong> <span id="checkoutCantidad"></span></p>
                    <p><strong>Total Gasto en Actividades:</strong> $<span id="checkoutGastoTotal"></span></p>

                    <div class="mb-3">
                        <label for="pagoCliente" class="form-label">Cantidad Pagada por el Cliente</label>
                        <input type="number" class="form-control" id="pagoCliente" name="pagoCliente" required>
                    </div>
                    <p><strong>Cambio:</strong> $<span id="checkoutCambio"></span></p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="calcularCambio">Calcular Cambio</button>
                <button type="button" class="btn btn-info" id="generarTicket">Ticket</button>
                <button type="button" class="btn btn-success" id="confirmarCheckout">Confirmar Check-Out</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pagar Habitación -->
<div class="modal fade" id="pagarHabitacionModal" tabindex="-1" aria-labelledby="pagarHabitacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagarHabitacionModalLabel">Detalles de Pago de Habitación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pagarHabitacionForm">
                    <p><strong>Nombre:</strong> <span id="pagarNombre"></span></p>
                    <p><strong>Apellido:</strong> <span id="pagarApellido"></span></p>
                    <p><strong>Email:</strong> <span id="pagarEmail"></span></p>
                    <p><strong>Habitación:</strong> <span id="pagarHabitacionTipo"></span></p>
                    <p><strong>Cantidad de Habitaciones:</strong> <span id="pagarCantidad"></span></p>
                    <p><strong>Precio Total de la Habitación:</strong> $<span id="pagarPrecioTotal"></span></p>
                    <div class="mb-3">
                        <label for="pagoHabitacionCliente" class="form-label">Cantidad Pagada por el Cliente</label>
                        <input type="number" class="form-control" id="pagoHabitacionCliente" required>
                    </div>
                    <p><strong>Cambio:</strong> $<span id="pagarCambio"></span></p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="calcularCambioPago">Calcular Cambio</button>
                <button type="button" class="btn btn-success" id="confirmarPagoHabitacion">Pagar Habitación</button>
                <button type="button" class="btn btn-warning" id="confirmarPagoParcial">Confirmar Pago Parcial</button>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar el modal y el cálculo de cambio -->
<script>
    let selectedHuesped = {};

    // Mostrar el modal y cargar los datos del huésped
    function showCheckoutModal(huesped) {
        selectedHuesped = huesped;

        document.getElementById('checkoutHuespedId').value = huesped.id;
        document.getElementById('checkoutNombre').innerText = huesped.nombre;
        document.getElementById('checkoutApellido').innerText = huesped.apellido;
        document.getElementById('checkoutEmail').innerText = huesped.email;
        document.getElementById('checkoutFechaEntrada').innerText = huesped.fecha_entrada;
        document.getElementById('checkoutFechaSalida').innerText = huesped.fecha_salida;
        document.getElementById('checkoutHabitacion').innerText = huesped.tipo;
        document.getElementById('checkoutCantidad').innerText = huesped.cantidad_disponible;
        document.getElementById('checkoutGastoTotal').innerText = huesped.gasto_total;

        // Limpiar el campo de cambio
        document.getElementById('checkoutCambio').innerText = '';
        document.getElementById('pagoCliente').value = '';

        // Mostrar el modal
        new bootstrap.Modal(document.getElementById('checkoutModal')).show();
    }

    document.getElementById('generarTicket').addEventListener('click', function () {
    if (selectedHuesped.id) {
        // Redirigir a la página para generar el ticket con el ID del huésped
        window.open(`./generar_ticket.php?huespedId=${selectedHuesped.id}`, '_blank');
    }
    });

    // Calcular el cambio
    document.getElementById('calcularCambio').addEventListener('click', function () {
        const pagoCliente = parseFloat(document.getElementById('pagoCliente').value);
        const gastoTotal = parseFloat(selectedHuesped.gasto_total);

        if (!isNaN(pagoCliente) && pagoCliente >= gastoTotal) {
            const cambio = pagoCliente - gastoTotal;
            document.getElementById('checkoutCambio').innerText = cambio.toFixed(2);
        } else {
            alert("La cantidad pagada no es suficiente o no es válida.");
        }
    });

    // Confirmar el Check-Out y enviar el formulario
    document.getElementById('confirmarCheckout').addEventListener('click', function () {
        if (document.getElementById('checkoutCambio').innerText !== '') {
            document.getElementById('checkoutForm').submit();
        } else {
            alert("Por favor, calcula el cambio antes de confirmar el Check-Out.");
        }
    });
</script>

<script>
// FUNCIONALIDAD DEL MODAL DE PAGO DE HABITACION 

// Mostrar el modal de pago de habitación
function showPagarHabitacionModal(huesped) {

selectedHuesped = huesped;

console.log("Huésped seleccionado:", selectedHuesped); // Verificar que el ID esté presente

    if (huesped.error) {
        alert(huesped.error);
        return;
    }

    // Asegúrate de que todos estos campos se establezcan correctamente
    selectedHuesped = huesped; // Esto debería estar bien

    document.getElementById('pagarNombre').innerText = huesped.nombre;
    document.getElementById('pagarApellido').innerText = huesped.apellido;
    document.getElementById('pagarEmail').innerText = huesped.email;
    document.getElementById('pagarHabitacionTipo').innerText = huesped.tipo;
    document.getElementById('pagarCantidad').innerText = huesped.cantidad_disponible;

    const precioTotal = parseFloat(huesped.precio_h || 0);
    if (isNaN(precioTotal)) {
        console.error("Precio total no válido:", huesped.precio_h);
        alert("Error al obtener el precio total de la habitación.");
        return;
    }

    document.getElementById('pagarPrecioTotal').innerText = precioTotal.toFixed(2);

    document.getElementById('pagarCambio').innerText = '';
    document.getElementById('pagoHabitacionCliente').value = '';

    new bootstrap.Modal(document.getElementById('pagarHabitacionModal')).show();
}

// Calcular el cambio para el pago de la habitación
document.getElementById('calcularCambioPago').addEventListener('click', function () {
    const pagoCliente = parseFloat(document.getElementById('pagoHabitacionCliente').value);
    const precioTotal = parseFloat(document.getElementById('pagarPrecioTotal').innerText);

    if (!isNaN(pagoCliente) && pagoCliente >= precioTotal) {
        const cambio = pagoCliente - precioTotal;
        document.getElementById('pagarCambio').innerText = cambio.toFixed(2);
    } else {
        alert("La cantidad pagada no es suficiente o no es válida.");
    }
});

// Confirmar el pago de la habitación (pago completo)
document.getElementById('confirmarPagoHabitacion').addEventListener('click', function () {
    const cambioTexto = document.getElementById('pagarCambio').innerText;
    if (cambioTexto !== '') {
        const precioTotal = parseFloat(document.getElementById('pagarPrecioTotal').innerText);

        // Actualizar el precio a 0 en la base de datos para reflejar el pago completo
        actualizarPrecioHabitacionEnBaseDeDatos(selectedHuesped.id, 0); // Aquí se usa selectedHuesped.id

        alert("Pago realizado exitosamente.");
        new bootstrap.Modal(document.getElementById('pagarHabitacionModal')).hide();
    } else {
        alert("Por favor, calcula el cambio antes de confirmar el pago.");
    }
});

// Confirmar el pago parcial
document.getElementById('confirmarPagoParcial').addEventListener('click', function () {

const precioTotalElemento = document.getElementById('pagarPrecioTotal');

const precioTotalActual = parseFloat(precioTotalElemento.innerText);


if (!isNaN(precioTotalActual) && precioTotalActual > 0) {

    // Calcular la mitad del precio total y actualizar el total pendiente

    const pagoParcial = precioTotalActual / 2;

    const nuevoTotal = precioTotalActual - pagoParcial;


    // Actualizar el total pendiente en la interfaz

    precioTotalElemento.innerText = nuevoTotal.toFixed(2);

    document.getElementById('pagarCambio').innerText = ''; // Limpiar campo de cambio


    // Actualizar el nuevo total en la base de datos

    actualizarPrecioHabitacionEnBaseDeDatos(selectedHuesped.id, nuevoTotal);


    alert("Pago parcial realizado exitosamente. Nuevo total pendiente: $" + nuevoTotal.toFixed(2));

} else {

    alert("Error al calcular el pago parcial.");

}

});

// Función para actualizar el campo Precio_Habitaciones en la base de datos
function actualizarPrecioHabitacionEnBaseDeDatos(huespedId, nuevoPrecio) {
    console.log("Enviando datos:", { huespedId, nuevoPrecio }); // Agregar esta línea para depurar
    fetch('./actualizar_precio_habitacion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ huespedId: huespedId, nuevoPrecio: nuevoPrecio })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Precio actualizado en la base de datos.");
        } else {
            console.error("Error al actualizar el precio en la base de datos:", data.error);
            alert("Error al actualizar el precio: " + (data.error || "Error desconocido."));
        }
    })
    .catch(error => {
        console.error("Error en la solicitud de actualización:", error);
    });
}
</script>

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

        <div class="container">
            <div class="header text-center">
                <h1>Sistema de Check-In / Check-Out</h1>
                <p>Fecha: <span id="currentDate"></span> | Hora: <span id="currentTime"></span></p>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-info text-center" role="alert">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <form id="reservationForm" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="checkInDate" class="form-label">Fecha de Entrada</label>
                            <input type="date" class="form-control" id="checkInDate" name="checkInDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="checkOutDate" class="form-label">Fecha de Salida</label>
                            <input type="date" class="form-control" id="checkOutDate" name="checkOutDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="habitacion" class="form-label">Selecciona Habitación</label>
                            <select class="form-control" id="habitacion" name="habitacion" required>
                                <?php foreach ($habitaciones as $habitacion): ?>
                                    <option value="<?php echo $habitacion['id']; ?>">
                                        <?php echo $habitacion['tipo'] . " - $" . $habitacion['precio'] . " - Disponibles: " . $habitacion['cantidad_disponible']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cantidadHabitaciones" class="form-label">Cantidad de Habitaciones</label>
                            <input type="number" class="form-control" id="cantidadHabitaciones" name="cantidadHabitaciones" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar Huésped</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h2 class="text-center">Huéspedes Registrados</h2>
                    <div class="table-responsive"> <!-- Contenedor para la tabla -->
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Email</th>
                                    <th>Fecha Entrada</th>
                                    <th>Fecha Salida</th>
                                    <th>Habitación</th>
                                    <th>Cant. Habitaciones</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conn->query("SELECT h.id, h.nombre, h.apellido, h.email, h.fecha_entrada, h.fecha_salida, r.tipo, h.cantidad_disponible, h.gasto_total,  h.precio_h  
                                                    FROM huespedes h 
                                                    JOIN habitaciones r ON h.habitacion_id = r.id");
                                $huespedes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($huespedes as $huesped): ?>
                                    <tr>
                                        <td><?php echo $huesped['id']; ?></td>
                                        <td><?php echo $huesped['nombre']; ?></td>
                                        <td><?php echo $huesped['apellido']; ?></td>
                                        <td><?php echo $huesped['email']; ?></td>
                                        <td><?php echo $huesped['fecha_entrada']; ?></td>
                                        <td><?php echo $huesped['fecha_salida']; ?></td>
                                        <td><?php echo $huesped['tipo']; ?></td>
                                        <td><?php echo $huesped['cantidad_disponible']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="showCheckoutModal({
                                                    id: <?php echo $huesped['id']; ?>,
                                                    nombre: '<?php echo addslashes($huesped['nombre']); ?>',
                                                    apellido: '<?php echo addslashes($huesped['apellido']); ?>',
                                                    email: '<?php echo addslashes($huesped['email']); ?>',
                                                    fecha_entrada: '<?php echo $huesped['fecha_entrada']; ?>',
                                                    fecha_salida: '<?php echo $huesped['fecha_salida']; ?>',
                                                    tipo: '<?php echo addslashes($huesped['tipo']); ?>',
                                                    cantidad_disponible: <?php echo $huesped['cantidad_disponible']; ?>,
                                                    gasto_total: <?php echo $huesped['gasto_total'] ?? 0.00; ?>
                                                });">
                                                Check-Out
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm"
                                                onclick="showPagarHabitacionModal({
                                                    id: <?php echo $huesped['id']; ?>,
                                                    nombre: '<?php echo addslashes($huesped['nombre']); ?>',
                                                    apellido: '<?php echo addslashes($huesped['apellido']); ?>',
                                                    email: '<?php echo addslashes($huesped['email']); ?>',
                                                    tipo: '<?php echo addslashes($huesped['tipo']); ?>',
                                                    cantidad_disponible: <?php echo $huesped['cantidad_disponible']; ?>,
                                                    precio_h: <?php echo $huesped['precio_h']; ?>
                                                });">
                                                Pagar Habitación
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> <!-- Fin del contenedor de la tabla -->
            </div>
        </div>
    </div>

<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('currentDate').innerText = now.toLocaleDateString();
        document.getElementById('currentTime').innerText = now.toLocaleTimeString();
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../js/script1.js"></script>
</body>
</html>
