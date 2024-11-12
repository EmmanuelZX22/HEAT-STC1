<?php
include '../modelo/conexionPDO.php';
require '../fpdf/fpdf.php';

// Obtener el ID del huésped de la URL
$huespedId = $_GET['huespedId'] ?? null;

if ($huespedId) {
    // Obtener información del huésped
    $stmt = $conn->prepare("SELECT h.nombre, h.apellido, h.email, h.fecha_entrada, h.fecha_salida, r.id AS habitacion_id, r.tipo AS habitacion, h.cantidad_disponible, h.gasto_total
                            FROM huespedes h
                            JOIN habitaciones r ON h.habitacion_id = r.id
                            WHERE h.id = :id");
    $stmt->execute([':id' => $huespedId]);
    $huesped = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($huesped) {
        // Obtener el historial de actividades del huésped
        $stmtActividades = $conn->prepare("SELECT actividad, horario, cantidad, precio, (cantidad * precio) AS precio_total, fecha_reserva 
                                            FROM actividades_reservadas 
                                            WHERE huesped_id = :huesped_id 
                                            ORDER BY fecha_reserva DESC");
        $stmtActividades->bindParam(':huesped_id', $huespedId, PDO::PARAM_INT);
        $stmtActividades->execute();
        $actividades = $stmtActividades->fetchAll(PDO::FETCH_ASSOC);

        // Crear el PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);

        // Título del documento
        $pdf->Cell(0, 10, 'Ticket de reserva', 0, 1, 'C');
        $pdf->Ln(5);

        // Información del huésped
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 10, 'Nombre: ' . $huesped['nombre'] . ' ' . $huesped['apellido']);
        $pdf->Ln(6);
        $pdf->Cell(50, 10, 'Email: ' . $huesped['email']);
        $pdf->Ln(6);
        $pdf->Cell(50, 10, 'Fecha de Entrada: ' . $huesped['fecha_entrada']);
        $pdf->Ln(6);
        $pdf->Cell(50, 10, 'Fecha de Salida: ' . $huesped['fecha_salida']);
        $pdf->Ln(6);
        $pdf->Cell(50, 10, 'Habitacion ID: ' . $huesped['habitacion_id']);
        $pdf->Ln(6);
        $pdf->Cell(50, 10, 'Cantidad Disponible: ' . $huesped['cantidad_disponible']);
        $pdf->Ln(6);
        $pdf->Cell(50, 10, 'Gasto Total: $' . number_format($huesped['gasto_total'], 2));
        $pdf->Ln(10);

        // Título para la sección de actividades
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Actividades reservadas', 0, 1);
        $pdf->Ln(5);

        // Tabla de actividades
        $pdf->SetFont('Arial', '', 10);
        foreach ($actividades as $actividad) {
            $pdf->Cell(50, 10, 'Actividad: ' . $actividad['actividad']);
            $pdf->Ln(6);
            $pdf->Cell(50, 10, 'Horario: ' . $actividad['horario']);
            $pdf->Ln(6);
            $pdf->Cell(50, 10, 'Cantidad: ' . $actividad['cantidad']);
            $pdf->Ln(6);
            $pdf->Cell(50, 10, 'Precio Unitario: $' . number_format($actividad['precio'], 2));
            $pdf->Ln(6);
            $pdf->Cell(50, 10, 'Precio Total: $' . number_format($actividad['precio_total'], 2));
            $pdf->Ln(6);
            $pdf->Cell(50, 10, 'Fecha de Reserva: ' . $actividad['fecha_reserva']);
            $pdf->Ln(10);
        }

        // Salida del PDF
        $pdf->Output('I', 'Reporte_Huesped_' . $huespedId . '.pdf');
    } else {
        echo "Huésped no encontrado.";
    }
} else {
    echo "ID de huésped no proporcionado.";
}
?>
