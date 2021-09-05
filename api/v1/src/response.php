<?php

class Response{
    // status code
    // The request is processed normally
    const CODE_OK = 200;

    // API_KEY is missed
    const CODE_API_KEY_MISSED = 400;

    // user should log in first
    const CODE_UNAUTHORIZED = 401;

    // the request parameters are invlid
    const CODE_INVALID_PARAMETERS = 402;

    // the data from the POST request  are invlid
    const CODE_INVALID_DATA = 403;

    // The request resource is not found
    const CODE_NOT_FOUND = 404;

    // server internal error
    const CODE_SERVER_ERROR = 500;

    
    private $response = null;
    public function init(int $code, string $msg){
        $this->response = array("code"=>$code, "msg"=>$msg);
    }

    public function respond(array $data=null){
        // https://stackoverflow.com/a/1353867/11659389
        header("Content-Type: application/json");
        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods: GET, POST");
        // header("ccess-Control-Allow-Headers: *");
        if($data !== null){
            $this->response["data"] = $data;
        }
        
        echo json_encode($this->response);
        http_response_code($this->response["code"]);
        exit;
    }
}
?>