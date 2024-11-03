<?php
class Config {
    public static $SECRET = "mindlandKEY";
    public static $HOST = null;
    public static $PORT = null;
    public static $BD = null;
    public static $USER = null;
    public static $PASS = null;
    
    public static function init() {
        self::$HOST = getenv('HOST');
        self::$PORT = getenv('PORT');
        self::$BD = getenv('DB');
        self::$USER = getenv('USER');
        self::$PASS = getenv('PASS');
    }
}

Config::init();
