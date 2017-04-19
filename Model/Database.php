<?php

class Database extends MySQLi {

    private static $instance = null ;

    public function __construct($host, $user, $password, $database){
        parent::__construct($host, $user, $password, $database);
    }

    public static function getInstance(){
        if(!defined("HOST")){
            define('HOST','localhost');
        }
        if(!defined("DBSA")){
            define('DBSA','garage');
        }
        if(!defined("USER")){
            define('USER','root');
        }
        if(!defined("PASS")){
            define('PASS','');
        }

        if (self::$instance == null){
            self::$instance = new self(HOST, USER, PASS, DBSA);
        }
        return self::$instance ;
    }
}

