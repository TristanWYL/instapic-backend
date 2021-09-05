<?php

class UploadPostCest
{
    public function _before(ApiTester $I)
    {
        // log in
        require "config.php";
        $this->_login($I, $username1, $password1);
    }

    public function _login(\ApiTester $I, String $username, String $password){
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost('signin?api_key=your_api_key', [
          'username' => $username,
          'password' => $password
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains("successfully");
    }

    public function _logout(\ApiTester $I)
    {
        $I->sendGet('signout?api_key=your_api_key');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains('successfully');
    }

    public function _upload(\ApiTester $I, String $filename="tests/_data/size_reasonable.jpg"){
        // $I->haveHttpHeader('Content-Type', 'multipart/form-data');
        require "config.php";
        $I->deleteHeader('content-type');
        $data = ['description' => 'An image uploaded by ' . $username1];
        $files = ["image" => $filename];
        $I->sendPost('post?api_key=your_api_key', $data, $files);
    }

    public function uploadFailedDueToLogout(ApiTester $I){
        $this->_logout($I);
        $this->_upload($I);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Please login before posting pictures");
    }

    public function uploadFailedDueToNoDescription(ApiTester $I)
    {
        require "config.php";
        $I->deleteHeader('content-type');
        $data = ['description1' => 'An image uploaded by ' . $username1];
        $files = ["image" => "tests/_data/size_reasonable.jpg"];
        $I->sendPost('post?api_key=your_api_key', $data, $files);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Please provide a short description for the image!");
    }

    public function uploadFailedDueToNoImage(ApiTester $I)
    {
        require "config.php";
        $I->deleteHeader('content-type');
        $data = ['description' => 'An image uploaded by ' . $username1];
        $files = ["image1" => "tests/_data/size_reasonable.jpg"];
        $I->sendPost('post?api_key=your_api_key', $data, $files);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Failed to find uploaded image file!");
    }

    public function uploadFailedDueToOverSizeImage(ApiTester $I)
    {
        $this->_upload($I, "tests/_data/oversize.jpg");
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Uploaded file should be of a size less than");
    }

    public function uploadFailedDueToFileIsNotImage(ApiTester $I)
    {
        $this->_upload($I, "tests/_data/instapic_test.sql");
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Uploaded file is not an image!");
    }

    public function uploadSuccessfully(ApiTester $I)
    {
        require "config.php";
        $domain = "http://localhost/";
        // data needs to be checked
        $image_file_path = "tests/_data/size_reasonable.jpg";
        $desc = "A random number is attached to this description: ". rand(0,1000000);
        $username = $username1;
        $password = $password1;

        // logout and login to ensure the username is what we need to check
        $this->_logout($I);
        $this->_login($I, $username, $password);

        // upload
        $I->deleteHeader('content-type');
        $data = ['description' => $desc];
        $files = ["image" => $image_file_path];
        $I->sendPost('post?api_key=your_api_key', $data, $files);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains("successfully");

        // check file integrity
        // get the latest uploading record
        $I->sendGet('posts?api_key=your_api_key&start=0&limit=1&sortby=time&order=des');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $response_body = json_decode($I->grabResponse(), true);
        $latest_upload_record = $response_body["data"][0];
        $I->seeResponseMatchesJsonType([
            "username" => "string",
            "timestamp" => "string",
            "url" => "string",
            "description" => "string"
        ], '$.data[0]');
        $I->assertEquals(md5_file($image_file_path), md5_file($domain . $latest_upload_record["url"]));

        // check other items
        $I->assertEquals($desc, $latest_upload_record["description"]);
        $I->assertEquals($username, $latest_upload_record["username"]);
        date_default_timezone_set("UTC");
        $time_elapsed_seconds = time() - strtotime($latest_upload_record["timestamp"]) + 8*3600;
        $I->assertLessThan(2, $time_elapsed_seconds);

        // see the latest record in database
        $picutre_url_items = explode("/", $latest_upload_record["url"]);
        $I->seeInDatabase('post', 
            [
                "picture" => end($picutre_url_items),
                "desc_" => $latest_upload_record["description"]
            ]
        );
    }
}
