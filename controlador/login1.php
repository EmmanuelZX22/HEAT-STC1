<?php
session_start();
require_once '../modelo/conexionPDO.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = trim($_POST['password']); // Elimina espacios en blanco adicionales en la contraseña

    try {
        // Consulta para verificar el usuario en la base de datos
        $sql = "SELECT * FROM huespedes WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Imprime los datos obtenidos de la base de datos para depuración
        echo "<pre>";
        echo "Datos de usuario en la base de datos: ";
        print_r($user);
        echo "Contraseña ingresada: " . $password . "<br>";
        echo "Contraseña en base de datos: " . $user['password'] . "<br>";
        echo "</pre>";

        // Verifica la contraseña usando password_verify para comparar con el hash
        if ($user && password_verify($password, $user['password'])) {
            // Si la contraseña es correcta, iniciar la sesión y redirigir
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];

            // Redireccionar al usuario a actividades.php
            header("Location: ../actividades.php");
            exit();
        } else {
            echo "Error: Credenciales inválidas.<br>";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
