<?php

abstract class Action{
    protected $db;
    protected $code;
    protected $msg;
    
    protected $response;

    public function __construct()
    {
        require_once "src/database.php";
        $this->db = new Database();
        require_once "src/response.php";
        $this->response = new Response();
    }
    
    // The $_SESSION is applied to check the login status of users
    protected function check_login(): bool{
        // session_start();
        if(isset($_SESSION["username"]) && isset($_SESSION["userid"])){
            return true;
        }
        return false;
    }
    
    abstract protected function action(array $request_data = null);
}
?>