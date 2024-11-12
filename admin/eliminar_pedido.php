<?php

require_once('../modelo/conexionPDO.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

   
    $sql = "DELETE FROM datos_compra WHERE id = :id";

    try {
        
        $stmt = $conn->prepare($sql);

        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        
        $stmt->execute();

       
        $conn = null;

        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    
    echo json_encode(['error' => 'ID no proporcionado']);
}
?>
