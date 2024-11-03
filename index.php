<?php
    $host = $_ENV['MYSQLHOST'];
    $port = $_ENV['MYSQLPORT'];
    $dbname = $_ENV['MYSQL_DATABASE'];
    $username = $_ENV['MYSQLUSER'];
    $password = $_ENV['MYSQLPASSWORD'];

    $con = mysqli_connect($host, $username, $password, $dbname, $port);

    
    if ($con) {
        echo "SIII.";
    }else{
        echo "NOOOO.";
    }