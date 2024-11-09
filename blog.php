<?php
require_once 'conexion.php';
require_once 'jwt.php';

/********BLOQUE DE ACCESO DE SEGURIDAD */
$headers = apache_request_headers();
$tmp = isset($headers['Authorization']) ? $headers['Authorization'] : '';
$jwt = str_replace("Bearer ", "", $tmp);
if(JWT::verify($jwt, Config::SECRET) > 0){
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

$user = JWT::get_data($jwt, Config::SECRET)['user'] ?? null;
error_log("Usuario obtenido del token: " . $user);
if (!$user) {
    header("HTTP/1.1 400 Bad Request");
    echo "No se pudo obtener el usuario del token."+$user;
    exit;
}
$c = conexion();
$s = $c->prepare("SELECT user_id FROM users WHERE user_app = :user");
$s->bindValue(":user",$user);
$s->execute();
$idUser = $s->fetchColumn();

if (!$idUser) {
    header("HTTP/1.1 400 Bad Request");
    echo "Usuario no encontrado.";
    exit;
}
/*** BLOQUE WEB SERVICE REST */
$metodo = $_SERVER["REQUEST_METHOD"];
switch($metodo){
    case 'GET':
            $c = conexion();
            if(isset($_GET['id_alert'])){
                $s = $c->prepare("SELECT * FROM alert WHERE id_alert = :id_alert");
                $s->bindValue(":id_alert", $_GET['id_alert']);
            }else{
                $s = $c->prepare("SELECT * FROM alert");
            }
            $s->execute();
            $s->setFetchMode(PDO::FETCH_ASSOC);
            $r = $s->fetchAll();
            header("http/1.1 200 ok");
            echo json_encode($r);
        break;
    case 'POST':
        if(isset($_POST['titulo']) && isset($_POST['descripcion'])){
            $c = conexion();
            $s = $c->prepare("INSERT INTO topics (tittle, description, user_id) VALUES (:titulo, :descripcion, :usuario)");
            $s->bindValue(":titulo", $_POST['titulo']);
            $s->bindValue(":descripcion", $_POST['descripcion']);
            $s->bindValue(":usuario", $idUser);
            $s->execute();
            if($s->rowCount()>0){
                header("http/1.1 201 created");
                echo json_encode(array("add" => "y", "topic_id" => $c->lastInsertId()));
            }else{
                header("http/1.1 400 bad request");
                echo json_encode(array("add" => "n"));
            }
        }else{
            header("HTTP/1.1 400 Bad Request");
            echo "Faltan datos";
        }
        break;
    case 'PUT':
        if(isset($_GET['id_alert']) ){
            $sql = "UPDATE alert SET ";
            (isset($_GET['user'])) ? $sql .= "user = :u, " : null;
            (isset($_GET['n_student'])) ? $sql .= "n_student = :ns, " : null;
            (isset($_GET['ouser'])) ? $sql .= "ouser = :o, " : null;
            (isset($_GET['hrfecha'])) ? $sql .= "hrfecha = :h, " : null;
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE id_alert = :id_alert";
            $c = conexion();
            $s = $c->prepare($sql);
            (isset($_GET['user'])) ? $s->bindValue(":u", $_GET['user']) : null;
            (isset($_GET['n_student'])) ? $s->bindValue(":ns", $_GET['n_student']) : null;
            (isset($_GET['ouser'])) ? $s->bindValue(":o", $_GET['ouser']) : null;
            (isset($_GET['hrfecha'])) ? $s->bindValue(":h", $_GET['hrfecha']) : null;

            $s->bindValue(":id_alert", $_GET['id_alert']);
            $s->execute();
            if($s->rowCount()>0){
                header("http/1.1 200 ok");
                echo json_encode(array("update" => "y"));
            }else{
                header("http/1.1 400 bad request");
                echo json_encode(array("update" => "n"));
            }
        }else{
            header("HTTP/1.1 400 Bad Request");
            echo "Faltan datos";
        }
        break;
    case 'DELETE':
        if(isset($_GET['id_alert'])){
            $c = conexion();
            $s = $c->prepare("DELETE FROM alert WHERE id_alert = :id_alert");
            $s->bindValue(":id_alert", $_GET['id_alert']);
            $s->execute();
            if($s->rowCount()>0){
                header("http/1.1 200 ok");
                echo json_encode(array("delete" => "y"));
            }else{
                header("http/1.1 400 bad request");
                echo json_encode(array("delete" => "n"));
            }
        }else{
            header("HTTP/1.1 400 Bad Request");
            echo "Faltan datos";
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
}