<?php
class Config{
    const SECRET = "mindlandKEY";
    
    /*
    const HOST = "localhost";
    const BD = "mindland";
    const USER = "root";
    const PASS = "";
    */
    const HOST = getenv('HOST');
    const PORT = getenv('PORT');
    const BD = getenv('DB');
    const USER = getenv('USER');
    const PASS = getenv('PASS');
}