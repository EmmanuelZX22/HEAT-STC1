<?php
session_start();
require("../modelo/conexionPDO.php");

$pass_nocifrada = $_POST['clave'];
$pass_cifrada = password_hash($pass_nocifrada, PASSWORD_DEFAULT, array("cost"=>10));

if ($conn == true) {
    try {
        // Verificar si el correo ya está registrado
        $stmt = $conn->prepare("SELECT COUNT(*) FROM t_usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $_POST['correo']);
        $stmt->execute();
        $existeCorreo = $stmt->fetchColumn();

        if ($existeCorreo > 0) {
            // Si el correo ya existe, mostrar un mensaje y detener el proceso
            echo "Este correo ya ha sido registrado previamente.";
        } else {
            // Insertar nuevo usuario si el correo no está registrado
            $inserta = $conn->prepare("INSERT INTO t_usuarios(correo, clave, nombreUsuario, aPaterno, aMaterno, direccion, telefono) 
                                       VALUES (:correo, :clave, :nombre, :apaterno, :amaterno, :direccion, :telefono)");
            $inserta->bindParam(':correo', $_POST['correo']);
            $inserta->bindParam(':clave', $pass_cifrada);
            $inserta->bindParam(':nombre', strtoupper($_POST['nombre']));
            $inserta->bindParam(':apaterno', strtoupper($_POST['apaterno']));
            $inserta->bindParam(':amaterno', strtoupper($_POST['amaterno']));
            $inserta->bindParam(':direccion', strtoupper($_POST['direccion']));
            $inserta->bindParam(':telefono', $_POST['telefono']);

            $inserta->execute();

            $conn = null;
            header('Location: ../login.php');
        }
    } catch (PDOException $e) {
        echo "Error al procesar recurso: " . $e->getMessage();
    }
} else {
    echo "Error al procesar recurso";
}
?>
