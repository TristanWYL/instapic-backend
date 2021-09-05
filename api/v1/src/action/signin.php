<?php

class Signin extends Action{
    public function action(array $request_data = null){
        // check the existence of the request data
        if((!isset($request_data["username"])) || (!isset($request_data["password"]))){
            $this->response->init($this->response::CODE_INVALID_DATA, "Request format error!");
            $this->response->respond();
        }
        $sql = sprintf("SELECT * FROM user WHERE username=CONVERT('%s' USING utf8mb4) COLLATE utf8mb4_bin AND password='%s'",
            $request_data["username"],
            sha1($request_data["password"])
        );
        $result = $this->db->query($sql);
        if(mysqli_num_rows($result) > 0){
            // record the status with $_SESSION
            $row = mysqli_fetch_assoc($result);
            $_SESSION["username"] = $row["username"];
            $_SESSION["userid"] = $row["userid"];
            $this->response->init($this->response::CODE_OK, "Sign in successfully!");
            $this->response->respond();
        }else{
            $this->response->init($this->response::CODE_INVALID_DATA, "Incorrect username or password!");
            $this->response->respond();
        }
    }
}
?>