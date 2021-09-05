<?php
class Posts extends Action{
    const SORTBY_OPTIONS = array("user"=>"user.username", "time"=>"post.createdtime");
    const ORDER_OPTIONS = array("asc"=>"ASC", "des"=>"DESC");

    /// sql example for selecting
    // sprintf("SELECT user.username, post.picture, post.desc_, post.createdtime
    // FROM user INNER JOIN post ON user.userid=post.userid
    // WHERE user.username='%s' ORDER BY post.createdtime ASC
    // LIMIT %d, %d",  
    // $_GET["user"],
    // $start, 
    // $limit);


    public function action(array $request_data = null){
        if($this->check_login() === false){
            $this->response->init($this->response::CODE_UNAUTHORIZED, "Please login first!");
            $this->response->respond();
        }
        require "config.php";
        $sql = "SELECT user.username, post.picture, post.desc_, post.createdtime
            FROM user INNER JOIN post ON user.userid=post.userid";
        
        // query the posts by userid
        if(isset($_GET["user"])){
            // get userid by user name first
            $sql = $sql . sprintf(" WHERE user.username LIKE '%%%s%%'", $_GET["user"]);
        }

        if(isset($_GET["sortby"])){
            if(!array_key_exists($_GET["sortby"], self::SORTBY_OPTIONS)){
                $this->response->init($this->response::CODE_INVALID_PARAMETERS, "Parameter 'sortby' is invalid!");
                $this->response->respond();
            }else{
                $sortby = $_GET["sortby"];
            }

            if(isset($_GET["order"])){
                if(!array_key_exists($_GET["order"], self::ORDER_OPTIONS)){
                    $this->response->init($this->response::CODE_INVALID_PARAMETERS, "Parameter 'order' is invalid!");
                    $this->response->respond();
                }else{
                    $order = $_GET["order"];
                }
            }else{
                $order = "asc";
            }

            $sql = $sql . sprintf(" ORDER BY %s %s", self::SORTBY_OPTIONS[$sortby], self::ORDER_OPTIONS[$order]);
        }

        // process the postion of the posts to be loaded
        if(isset($_GET["start"])){
            if(!is_numeric($_GET["start"])){
                $this->response->init($this->response::CODE_INVALID_PARAMETERS, "Parameter 'start' should be the no-less-than-0 integer!");
                $this->response->respond();
            }else if(intval($_GET["start"]) != $_GET["start"] || intval($_GET["start"]) < 0){
                $this->response->init($this->response::CODE_INVALID_PARAMETERS, "Parameter 'start' should be the no-less-than-0 integer!");
                $this->response->respond();
            }
            $start = intval($_GET["start"]);
        }else{
            $start = 0;
        }
        if(isset($_GET["limit"])){
            if(!is_numeric($_GET["limit"])){
                $this->response->init($this->response::CODE_INVALID_PARAMETERS, "Parameter 'limit' should be the positive integer!");
                $this->response->respond();
            }else if(intval($_GET["limit"]) != $_GET["limit"] || intval($_GET["limit"]) <= 0){
                $this->response->init($this->response::CODE_INVALID_PARAMETERS, "Parameter 'limit' should be the positive integer!");
                $this->response->respond();
            }
            $limit = intval($_GET["limit"]);
        }else{            
            $limit = $num_of_posts_for_each_request_default;
        }
        $sql = $sql . sprintf(" LIMIT %d, %d", $start, $limit);

        // execute the $sql
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            $data = array();
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $row_array = array(
                    "username" => $row["username"],
                    "timestamp" => $row["createdtime"],
                    // "url" => $_SERVER["HTTP_HOST"] . "/" . $dir_for_picture . $row["picture"],
                    "url" => $dir_for_picture_url . $row["picture"],
                    "description" => $row["desc_"]
                );
                array_push($data, $row_array);
            }
            
            // feedback the response
            $this->response->init($this->response::CODE_OK, "Query successfully");
            $this->response->respond($data);
        }else{
            $this->response->init($this->response::CODE_OK, "Query successfully");
            $this->response->respond(array());
        }
    }
}
?>