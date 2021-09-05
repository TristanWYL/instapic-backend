<?php
foreach (glob("src/action/*.php") as $filename)
{
    require_once $filename;
}

class Process{
    public function work($request){
        // refer to: https://stackoverflow.com/a/26866773/11659389
        $post_data = json_decode(file_get_contents('php://input'), true);

        // process request
        $action = new $request();
        $action->action($post_data);
    }
}