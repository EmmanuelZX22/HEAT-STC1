<?php
include('../modelo/conexionPDO.php');

$id = $_POST['id'];
$cantidad = $_POST['cantidad'];

$sql = "UPDATE productos SET cantidad = :cantidad WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':cantidad', $cantidad);

if ($stmt->execute()) {
    header('Location: ./inventario1.php');
} else {
    echo "Error al actualizar cantidad: " . $stmt->errorInfo()[2];
}
?>