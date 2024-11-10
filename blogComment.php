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

$user = JWT::get_data($jwt, Config::SECRET)['user_app'];
error_log("Usuario obtenido del token: " . $user);
if (!$user) {
    header("HTTP/1.1 400 Bad Request");
    echo "No se pudo obtener el usuario del token.";
    exit;
}

$c = conexion();
$s = $c->prepare("SELECT user_id FROM users WHERE user_app = :user");
$s->bindValue(":user",$user);
$s->execute();
$idUser = $s->fetchColumn();
/*
//Bloque buscar id para encontrar al usuario
$idUserComment = $_GET['userC'];
$c = conexion();
$s = $c->prepare("SELECT user_id FROM users WHERE user_app = :user");
$s->bindValue(":user",$idUserComment);
$s->execute();
$idUser = $s->fetchColumn();
*/
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
            if(isset($_GET['idTopic'])){
                $s = $c->prepare("SELECT * FROM comments WHERE topic_id = :idTopic");
                $s->bindValue(":idTopic", $_GET['idTopic']);
            }else{
                $s = $c->prepare("SELECT * FROM comments");
            }
            $s->execute();
            $s->setFetchMode(PDO::FETCH_ASSOC);
            $r = $s->fetchAll();
            header("http/1.1 200 ok");
            echo json_encode($r);
        break;
    case 'POST':
        if(isset($_POST['comentario']) && isset($_POST['idTopic'])){
            $c = conexion();
            $s = $c->prepare("INSERT INTO comments (topic_id, comment, user_id) VALUES (:idTopic, :comentario, :usuario)");
            $s->bindValue(":idTopic", $_POST['idTopic']);
            $s->bindValue(":comentario", $_POST['comentario']);
            $s->bindValue(":usuario", $idUser);
            $s->execute();
            if($s->rowCount()>0){
                header("http/1.1 201 created");
                echo json_encode(array("add" => "y", "comment_id" => $c->lastInsertId()));
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
        if(isset($_GET['idComment']) ){
            $sql = "UPDATE comments SET ";
            (isset($_GET['comentario'])) ? $sql .= "comment = :comentario, " : null;
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE comment_id = :comment_id";
            $c = conexion();
            $s = $c->prepare($sql);
            (isset($_GET['comentario'])) ? $s->bindValue(":comentario", $_GET['comentario']) : null;
            $s->bindValue(":comment_id", $_GET['idComment']);
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
        if(isset($_GET['idComment'])){
            $c = conexion();
            $s = $c->prepare("DELETE FROM comments WHERE comment_id = :comment_id");
            $s->bindValue(":comment_id", $_GET['idComment']);
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