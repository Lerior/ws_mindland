<?php
require_once 'conexion.php';
require_once 'jwt.php';
$user=$_POST['nombre'];
$rol=$_POST['rol'];
$suscription=$_POST['suscripcion'];
$phone=$_POST['numero'];
$mail=$_POST['correo'];
$birth=$_POST['nacimiento'];
$password=$_POST['contrasena'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$userA=$_POST['user'];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['nombre']) && isset($_POST['rol']) && isset($_POST['suscripcion']) && isset($_POST['numero']) 
    && isset($_POST['correo']) && isset($_POST['nacimiento']) && isset($_POST['contrasena']) && isset($_POST['user'])){
        $c = conexion();
        $s = $c->prepare("INSERT INTO users (user_name, user_rol, user_suscription, user_phone, user_mail, user_birth, user_password, user_app) 
        VALUES (:uname, :rol, :sus, :phone, :mail, :birth, :pass, :user)");
        $s->bindValue(":uname", $user);
        $s->bindValue(":rol", $rol);
        $s->bindValue(":sus", $suscription);
        $s->bindValue(":phone", $phone);
        $s->bindValue(":mail", $mail);
        $s->bindValue(":birth", $birth);
        $s->bindValue(":pass", $password_hash);
        $s->bindValue(":user", $userA);
        $s->execute();
        if($s->rowCount()>0){
            header("HTTP/1.1 201 created");
            echo json_encode(array("add" => "y", "user_id" => $c->lastInsertId()));
        }else{
            header("HTTP/1.1 400 bad request");
            echo json_encode(array("add" => "n"));
        }
    }else{
        header("HTTP/1.1 400 Bad Request");
        echo "Faltan datos";
    }
}

        
    /*if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($usuario) && isset($pass)){
            $c = conexion();
            $s = $c->prepare("INSERT INTO users (user, pass) VALUES (:user, :pass)");
            $s->bindValue(":user", $usuario);
            $s->bindValue(":pass", $pass);
            $s->execute();
            $s->setFetchMode(PDO::FETCH_ASSOC);
            $r = $s->fetch();
            
            header("http/1.1 200 ok");
            echo json_encode($s);
        }
    }*/
?>