<?php
class Signout extends Action{
    public function action(array $request_data = null){
        // remove all session variables
        session_unset();
        // destroy the session
        session_destroy();

        $this->response->init($this->response::CODE_OK, "Log out successfully.");
        $this->response->respond();
    }
}
?>