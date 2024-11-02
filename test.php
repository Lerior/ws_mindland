<?php
require_once 'conexion.php';
$c = conexion();
if ($c) {
    echo "SIII.";
}else{
    echo "NOOOO.";
}
?>