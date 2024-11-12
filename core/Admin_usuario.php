<?php
    class Usuario{
        //Astributos

        //Metodos
        public function AutentificarUsuario($correo,$password){
            include '../conexion.php';
            $conectar=new Conexion();
            $consulta=$conectar->prepare("SELECT * FROM usuarios WHERE CorreoElectronico = :correo && Password = :password");
            $consulta->bindParam(":correo",$correo,PDO::PARAM_STR);
            $consulta->bindParam(":password",$password,PDO::PARAM_STR);
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            return $consulta->fetchAll();
        }
        public function InsertarUsuario($nombre,$correo,$password,$tipo){
            include '../conexion.php';
            $conectar=new Conexion();
            $consulta=$conectar->prepare("SELECT INTO usuarios(NombreCompleto,CorreoElectronico,Password,Tipo,FechaRegistro)VALUES(:nombre,:correo,:password,:tipo,NOW())");
            $consulta->bindParam(":nombre",$nombre,PDO::PARAM_STR);
            $consulta->bindParam(":correo",$correo,PDO::PARAM_STR);
            $consulta->bindParam(":password",$password,PDO::PARAM_STR);
            $consulta->bindParam(":tipo",$tipo,PDO::PARAM_INT);
            $consulta->execute();
            return true;
        }
        public function ModifcarUsuario($id,$nombre,$correo){
            include '../conexion.php';
            $conectar=new Conexion();
            $consulta=$conectar->prepare("UPDATE usuarios SET 
            NombreCompleto=:nombre,CorreoElectronico=:correo WHERE Id=:id");
            $consulta->bindParam(":nombre",$nombre,PDO::PARAM_STR);
            $consulta->bindParam(":correo",$correo,PDO::PARAM_STR);
            $consulta->bindParam(":id",$id,PDO::PARAM_INT);
            $consulta->execute();
            return true;
        }
        public function EliminarUsuario($id){
            try{
                include '../conexion.php';
            $conectar=new Conexion();
            $consulta=$conectar->prepare("DELETE FROM usuarios WHERE Id=:id");
            $consulta->bindParam(":nombre",$nombre,PDO::PARAM_STR);
            $consulta->bindParam(":correo",$correo,PDO::PARAM_STR);
            $consulta->bindParam(":id",$id,PDO::PARAM_INT);
            $consulta->execute();
            return true;
            } catch (Exception $e){
                return false;
            }
        }
        public function ObtenerUsuario(){
            include '../conexion.php';
            $conectar=new Conexion();
            $consulta=$conectar->prepare("SELECT * FROM usuarios");
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            return $consulta->fetchAll();
        }
        public function ObtenerUsuarioPorNombre($nombre){
            include '../conexion.php';
            $conectar=new Conexion();
            $consulta=$conectar->prepare("SELECT * FROM usuarios WHERE NombreCompleto LIKE :nombre");
            $consulta->bindParam(":nombre","%$nombre%",PDO::PARAM_STR);
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            return $consulta->fetchAll();
        }
    }
?>