<?php
//Optional (if not set the default c3 output dir will be used)
define('C3_CODECOVERAGE_ERROR_LOG_FILE', '../../c3_error.log'); 
include '../../c3.php';
define('MY_APP_STARTED', true);

// chdir(dirname(getcwd()));
error_reporting(E_WARNING);
// error_reporting(E_ALL);
session_start();

require_once "./config.php";
require_once "./src/response.php";

if(!isset($_GET["api_key"])){
    $response = new Response();
    $response->init($response::CODE_API_KEY_MISSED, "api_key NOT FOUND");
    $response->respond();
}
if(in_array($_GET["api_key"], API_KEYS)===false){
    $response = new Response();
    $response->init($response::CODE_INVALID_PARAMETERS, "api_key NOT RECOGNIZED");
    $response->respond();
}

// $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

require_once "./src/url_parser.php";
$request = UrlParser::parse();
require_once "./src/process.php";
$process = new Process();
$process->work($request);

$response = new Response();
$response->init($response::CODE_SERVER_ERROR, "Unexpected Server error");
$response->respond();