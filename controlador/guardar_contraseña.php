<?php
// Incluir la conexión a la base de datos
include('../modelo/conexionPDO.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el correo y la contraseña desde el formulario
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];

    // Verificar que el correo exista en la base de datos
    $query = "SELECT * FROM huespedes WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Encriptar la contraseña antes de guardarla
        $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $updateQuery = "UPDATE huespedes SET password = :password WHERE email = :email";
        $updateStmt = $conn->prepare($updateQuery);
        
        // Asignar los valores de los parámetros
        $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        if ($updateStmt->execute()) {
            header('Location: ../loginusuario.php');
        } else {
            echo "Hubo un error al guardar la contraseña. Inténtalo de nuevo.";
        }
    } else {
        echo "El correo ingresado no está registrado.";
    }
}
?>
