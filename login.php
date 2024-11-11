<?php
require_once 'conexion.php';
require_once 'jwt.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["user"]) && isset($_GET["pass"])) {
        $c = conexion();
        
        // Consulta solo por el nombre de usuario
        $s = $c->prepare("SELECT * FROM users WHERE user_app = :user");
        $s->bindValue(":user", $_GET["user"]);
        $s->execute();
        $s->setFetchMode(PDO::FETCH_ASSOC);
        $r = $s->fetch();
        
        if ($r) {
            // Verifica la contraseña usando password_verify
            if (password_verify($_GET["pass"], $r['user_password'])) {
                $t = JWT::create(["user_app" => $_GET["user"]], Config::SECRET);
                $result = ["login" => "y", "token" => $t, "sub" => $r['user_suscription']];
            } else {
                $result = ["login" => "n", "token" => "Error", "message" => "Contraseña incorrecta"];
            }
        } else {
            $result = ["login" => "n", "token" => "Error", "message" => "Usuario no encontrado"];
        }

        header("HTTP/1.1 200 OK");
        echo json_encode($result);
    } else {
        header("HTTP/1.1 400 Bad Request"); // Para errores de solicitud
        echo json_encode(["login" => "n", "token" => "Error", "message" => "Parámetros inválidos"]);
    }
}
?>