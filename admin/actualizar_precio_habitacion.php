<?php
include '../modelo/conexionPDO.php';

$data = json_decode(file_get_contents('php://input'), true);
error_log(print_r($data, true)); // Ver qué datos se están recibiendo

$huespedId = $data['huespedId'] ?? null;
$nuevoPrecio = $data['nuevoPrecio'] ?? null;

if ($huespedId && $nuevoPrecio !== null) {
    try {
        $stmt = $conn->prepare("UPDATE huespedes SET precio_h = :nuevoPrecio WHERE id = :huespedId");
        $stmt->execute([':nuevoPrecio' => $nuevoPrecio, ':huespedId' => $huespedId]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Agregar más información sobre el error
        error_log("Error en la consulta: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
}
?>