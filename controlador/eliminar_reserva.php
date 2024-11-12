<?php
session_start();
require_once '../modelo/conexionPDO.php'; // Asegúrate de que esta ruta sea correcta y que el archivo exista

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, redirigir al login
    header("Location: ../loginusuario.php");
    exit();
}

// Verificar si se ha enviado el ID de la reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserva_id'])) {
    $reserva_id = $_POST['reserva_id'];

    try {
        // Preparar la consulta para eliminar la reserva
        $stmt = $conn->prepare("DELETE FROM actividades_reservadas WHERE id = :reserva_id AND huesped_id = :usuario_id");
        $stmt->bindParam(':reserva_id', $reserva_id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Redirigir de vuelta al historial de actividades con un mensaje de éxito
        header("Location: ../historialU.php?mensaje=Reserva eliminada exitosamente.");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Redirigir si no se envió un ID de reserva
    header("Location: historiaU.php?error=No se pudo eliminar la reserva.");
    exit();
}
?>