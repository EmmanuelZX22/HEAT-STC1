<?php
session_start();
require_once '../modelo/conexionPDO.php'; // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../loginusuario.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$actividades = $_POST['actividades'];
$fecha_reserva = date('Y-m-d'); // Fecha de reserva
$total_gasto = 0;

foreach ($actividades as $actividad => $detalles) {
    // Verificar si la actividad fue seleccionada (checkbox y cantidad > 0)
    if (isset($detalles['cantidad']) && $detalles['cantidad'] > 0 && isset($detalles['seleccionada'])) {
        $horario = $detalles['horario'];
        $cantidad = $detalles['cantidad'];
        $precio = 0;

        // Definir precios por actividad
        switch ($actividad) {
            case 'Cabalgata':
                $precio = 550;
                break;
            case 'Masaje':
                $precio = 250;
                break;
            case 'Temazcal':
                $precio = 600;
                break;
        }

        // Calcular costo total para esta actividad
        $total = $precio * $cantidad;
        $total_gasto += $total; // Sumar al gasto total

        // Insertar en la tabla actividades_reservadas
        $query = "INSERT INTO actividades_reservadas (huesped_id, actividad, horario, cantidad, precio, fecha_reserva)
                  VALUES (:huesped_id, :actividad, :horario, :cantidad, :precio, :fecha_reserva)";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':huesped_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':actividad', $actividad, PDO::PARAM_STR);
        $stmt->bindValue(':horario', $horario, PDO::PARAM_STR);
        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':precio', $precio, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_reserva', $fecha_reserva, PDO::PARAM_STR);
        $stmt->execute();
    }
}

// Actualizar el gasto total en la tabla de huespedes
$updateQuery = "UPDATE huespedes SET gasto_total = gasto_total + :total WHERE id = :id";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bindValue(':total', $total_gasto, PDO::PARAM_STR);
$updateStmt->bindValue(':id', $usuario_id, PDO::PARAM_INT);
$updateStmt->execute();

header("Location: ../actividades.php?success=1");
exit();
