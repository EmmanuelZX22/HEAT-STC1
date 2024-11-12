<?php
require_once '../modelo/conexionPDO.php';
require '../fpdf/fpdf.php'; // Asegúrate de que la biblioteca FPDF esté incluida

// Validar que se recibe el ID del huésped
if (!isset($_GET['huesped_id'])) {
    die("ID de huésped no especificado.");
}
$huesped_id = intval($_GET['huesped_id']);

// Consulta para obtener la información del huésped
$stmtHuesped = $conn->prepare("SELECT nombre, apellido, email, fecha_entrada, fecha_salida, habitacion_id, cantidad_disponible, gasto_total FROM huespedes WHERE id = :huesped_id");
$stmtHuesped->bindParam(':huesped_id', $huesped_id, PDO::PARAM_INT);
$stmtHuesped->execute();
$huesped = $stmtHuesped->fetch(PDO::FETCH_ASSOC);

if (!$huesped) {
    die("Huesped no encontrado.");
}

// Consulta para obtener el historial de actividades del huésped
$stmtActividades = $conn->prepare("SELECT actividad, horario, cantidad, precio, (cantidad * precio) AS precio_total, fecha_reserva FROM actividades_reservadas WHERE huesped_id = :huesped_id ORDER BY fecha_reserva DESC");
$stmtActividades->bindParam(':huesped_id', $huesped_id, PDO::PARAM_INT);
$stmtActividades->execute();
$actividades = $stmtActividades->fetchAll(PDO::FETCH_ASSOC);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Agregar imagen en la parte superior izquierda
$pdf->Image('../img/logoM.png', 10, 10, 30); // X=10, Y=10, ancho=30

// Agregar imagen en la parte superior derecha
$pdf->Image('../img/2.png', 170, 5, 20); // X=170, Y=10, ancho=30

// Espacio para las imágenes antes de iniciar el contenido
$pdf->Ln(5);

// Título del documento
$pdf->Cell(0, 10, 'Reporte Completo del Huesped', 0, 1, 'C');
$pdf->Ln(10);

// Información del huésped
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Nombre: ' . $huesped['nombre'] . ' ' . $huesped['apellido']);
$pdf->Ln(8);
$pdf->Cell(50, 10, 'Email: ' . $huesped['email']);
$pdf->Ln(8);
$pdf->Cell(50, 10, 'Fecha de Entrada: ' . $huesped['fecha_entrada']);
$pdf->Ln(8);
$pdf->Cell(50, 10, 'Fecha de Salida: ' . $huesped['fecha_salida']);
$pdf->Ln(8);
$pdf->Cell(50, 10, 'Habitacion ID: ' . $huesped['habitacion_id']);
$pdf->Ln(8);
$pdf->Cell(50, 10, 'Cantidad Disponible: ' . $huesped['cantidad_disponible']);
$pdf->Ln(8);
$pdf->Cell(50, 10, 'Gasto Total: $' . number_format($huesped['gasto_total'], 2));
$pdf->Ln(12);

// Título para la sección de actividades
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Historial de Actividades', 0, 1);
$pdf->Ln(6);

// Encabezados de la tabla de actividades
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 10, 'Actividad', 1);
$pdf->Cell(30, 10, 'Horario', 1);
$pdf->Cell(20, 10, 'Cantidad', 1);
$pdf->Cell(30, 10, 'Precio Unitario', 1);
$pdf->Cell(30, 10, 'Precio Total', 1);
$pdf->Cell(40, 10, 'Fecha de Reserva', 1);
$pdf->Ln();

// Datos de las actividades
$pdf->SetFont('Arial', '', 10);
foreach ($actividades as $actividad) {
    $pdf->Cell(40, 10, $actividad['actividad'], 1);
    $pdf->Cell(30, 10, $actividad['horario'], 1);
    $pdf->Cell(20, 10, $actividad['cantidad'], 1);
    $pdf->Cell(30, 10, '$' . number_format($actividad['precio'], 2), 1);
    $pdf->Cell(30, 10, '$' . number_format($actividad['precio_total'], 2), 1);
    $pdf->Cell(40, 10, $actividad['fecha_reserva'], 1);
    $pdf->Ln();
}

// Salida del PDF
$pdf->Output('I', 'Reporte_Huesped_' . $huesped_id . '.pdf');

?>