<?php

class Signup extends Action{
    public function action(array $request_data = null){
        // check the existence of the request data
        if((!isset($request_data["username"])) || (!isset($request_data["password"]))){
            $this->response->init($this->response::CODE_INVALID_DATA, "Request format error!");
            $this->response->respond();
        }
        // search table user for this username
        $sql = sprintf("SELECT userid, username FROM user WHERE username='%s'", $request_data["username"]);
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $this->response->init($this->response::CODE_INVALID_DATA, "The username has been used, try another username!");
            $this->response->respond();
        }
        $sql = sprintf("INSERT INTO user (username, password) VALUES ('%s', '%s')",$request_data["username"], sha1($request_data["password"]));
        $result = $this->db->query($sql);
        if($result === true){
            // auto logout
            // remove all session variables
            session_unset();
            // destroy the session
            session_destroy();
            
            $this->response->init($this->response::CODE_OK, "Sign up successfully!");
            $this->response->respond();
        }else{
            $this->response->init($this->response::CODE_SERVER_ERROR, "Failed to write into the database!");
            $this->response->respond();
        }
    }
}

?>