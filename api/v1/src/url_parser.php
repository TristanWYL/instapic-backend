<?php
class UrlParser{
    public static function parse(){
        $url = $_SERVER["REQUEST_URI"];
        $pos = strpos($url, "?");
        if($pos !== false){
            $url = substr($url, 0, $pos);
        }
        $url_items = explode("/", $url);
        require_once "./config.php";
        $request = $url_items[3];
        if(array_key_exists($request, SupportedApiClassPair) === false){
            require_once "./src/response.php";
            $response = new Response();
            $response->init($response::CODE_NOT_FOUND, "Requested endpoint '$request' does not exist!");
            $response->respond();
        }
        return SupportedApiClassPair[$request];
    }
}
?>