<?php
class LoginStatusQuery extends Action{
    public function action(array $request_data = null){
        if($this->check_login() === true){
            $this->response->init($this->response::CODE_OK, "Have logged in.");
            $data = array("login"=>true,"username" => $_SESSION["username"]);
            $this->response->respond($data);
        }else{
            $this->response->init($this->response::CODE_OK, "Not logged in yet.");
            $data = array("login"=>false,"username" => null);
            $this->response->respond($data);
        }
    }
}
?>