<?php
require("../modelo/conexionPDO.php");

try {
    
    if (isset($_POST['clave'])) {
        
        $correo = htmlentities(addslashes($_POST['correo']));
        $clave = $_POST['clave'];

        $sql = "SELECT * FROM t_usuarios WHERE correo = :correo";

        
        $resultado = $conn->prepare($sql);

        
        $resultado->execute(array(":correo" => $correo));

        $login = $resultado->fetch(PDO::FETCH_ASSOC);

        if ($login && password_verify($clave, $login['clave'])) {
            
            session_start();

            
            $_SESSION['usuario_id'] = $login['id'];
            $_SESSION['usuario_nombre'] = $login['nombreUsuario'];
            $_SESSION['usuario_correo'] = $login['correo'];
            

            echo '<script>
                Swal.fire({
                icon: "success",
                title: "Usuario aceptado",
                text: "Inicio de sesión correcto",
                showConfirmButton: true,
                confirmButtonText: "Aceptar"
            }) </script>';

            
            if ($login['es_administrador'] == 1) {
                
                header("Location: ../admin/indexadmin.php");
                exit();
            } else {
               
                header("Location: ../admin/indext.php");
                exit();
            }
        } else {
            echo '<script>
                Swal.fire({
                icon: "error",
                title: "Credenciales no válidas",
                text: "Inicio de sesión incorrecto",
                showConfirmButton: true,
                confirmButtonText: "Aceptar"
            }) </script>';
            header("Location: ../../index.php?error=si");
            exit();
        }
    } else {
        echo "Error: Contraseña no proporcionada.";
        
    }

} catch (Exception $e) {
    die($e->getMessage());
}
?>
