<?php
class Post extends Action{
    public function action(array $request_data = null){
        if($this->check_login() === false){
            $this->response->init($this->response::CODE_UNAUTHORIZED, "Please login before posting pictures!");
            $this->response->respond();
        }

        // check existence of 'description' of the picture
        if(!isset($_POST["description"])){
            $this->response->init($this->response::CODE_INVALID_DATA, "Please provide a short description for the image!");
            $this->response->respond();
        }

        // process the received files
        // check whether an image file is received
        if(!isset($_FILES["image"])){
            $this->response->init($this->response::CODE_INVALID_DATA, "Failed to find uploaded image file!");
            $this->response->respond();
        }

        require "config.php";
        if($_FILES["image"]["tmp_name"] === "" && $_FILES["image"]["error"] === 1 && $_FILES["image"]["size"] === 0){
            $this->response->init($this->response::CODE_INVALID_DATA, sprintf("Uploaded file should be of a size less than %.3fMB!", MAX_PICTURE_SIZE_BYTE/1000/1000));
            $this->response->respond();
        }

        $image_check = getimagesize($_FILES["image"]["tmp_name"]);
        if($image_check === false){
            $this->response->init($this->response::CODE_INVALID_DATA, "Uploaded file is not an image!");
            $this->response->respond();
        }
        
        // limit the file size
        if ($_FILES["image"]["size"] > MAX_PICTURE_SIZE_BYTE){
            $this->response->init($this->response::CODE_INVALID_DATA, sprintf("Uploaded file should be of a size less than %.3fMB!", MAX_PICTURE_SIZE_BYTE/1000/1000));
            $this->response->respond();
        }

        // limit file type to jpg, jpeg, png, gif
        $image_file_ext = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
        if($image_file_ext != "jpg" && $image_file_ext != "png" && $image_file_ext != "jpeg"
            && $image_file_ext != "gif" ) {
            $this->response->init($this->response::CODE_INVALID_DATA, "Only JPG, JPEG, PNG & GIF files are allowed!");
            $this->response->respond();
        }
        
        // generate a file name for the image
        while(true){
            $image_new_name = md5($_SESSION["username"] . microtime(true)) . "." . $image_file_ext;
            if(file_exists($dir_for_picture . $image_new_name) === false){
                break;
            }
        }
        
        // store the image
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $dir_for_picture . $image_new_name) === false){
            $this->response->init($this->response::CODE_SERVER_ERROR, "Internal Server Error! Please try to upload the file again.");
            $this->response->respond();
        }
        
        // write a record into the database
        $sql = sprintf("INSERT INTO post (userid, picture, desc_) VALUES ('%s', '%s', '%s')", 
            $_SESSION["userid"], 
            $image_new_name, 
            $_POST["description"]);
        $result = $this->db->query($sql);
        if($result === true){
            $this->response->init($this->response::CODE_OK, "Post successfully.");
            $this->response->respond();
        }else{
            $this->response->init($this->response::CODE_SERVER_ERROR, "Failed to post a picture.");
            $this->response->respond();
        }
    }
}

?>