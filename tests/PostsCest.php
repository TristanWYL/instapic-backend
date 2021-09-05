<?php
require "config.php";
class PostsCest
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

    public function fetchPostsFailedDueToLogout(ApiTester $I){
        $this->_logout($I);
        $I->sendGet('posts?api_key=your_api_key');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Please login first!");
    }

    public function fetchPostsFailedDueToInappropriateParameters(ApiTester $I){
        // sortby
        $I->sendGet('posts?api_key=your_api_key&sortby=xx');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'sortby' is invalid!");

        // order
        $I->sendGet('posts?api_key=your_api_key&sortby=time&order=xx');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'order' is invalid!");

        // start
        $I->sendGet('posts?api_key=your_api_key&start='.rand(1, 9) / 10);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'start' should be the no-less-than-0 integer!");

        $I->sendGet('posts?api_key=your_api_key&start=-' . rand(1,2000));
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'start' should be the no-less-than-0 integer!");

        $I->sendGet('posts?api_key=your_api_key&start=abc');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'start' should be the no-less-than-0 integer!");

        // limit 
        $I->sendGet('posts?api_key=your_api_key&limit='.rand(1, 9) / 10);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'limit' should be the positive integer!");

        $I->sendGet('posts?api_key=your_api_key&limit=-' . rand());
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'limit' should be the positive integer!");

        $I->sendGet('posts?api_key=your_api_key&limit=abc');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Parameter 'limit' should be the positive integer!");
    }

    private function _postRecordMatchJsonType(ApiTester $I){
        $postRecords = json_decode($I->grabResponse(), true)["data"];
        foreach($postRecords as $index=>$record){
            $I->seeResponseMatchesJsonType([
                "username" => "string",
                "timestamp" => "string",
                "url" => "string",
                "description" => "string"
            ], '$.data['.$index.']');
        }
    }

    public function fetchPostsSuccessfully(ApiTester $I){
        // content check
        $I->sendGet('posts?api_key=your_api_key');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $this->_postRecordMatchJsonType($I);
    }

    public function fetchPostsSuccessfullyByUsername(ApiTester $I){
        require "config.php";
        // upload more than one
        $image_file_path = "tests/_data/size_reasonable.jpg";
        
        // first uploading as $username1
        $desc = "A random number is attached to this description: ". rand(0,1000000);
        $this->_logout($I);
        $this->_login($I, $username1, $password1);
        $I->deleteHeader('content-type');
        $data = ['description' => $desc];
        $files = ["image" => $image_file_path];
        $I->sendPost('post?api_key=your_api_key', $data, $files);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains("successfully");

        // second uploading as $username2
        $desc = "A random number is attached to this description: ". rand(0,1000000);
        $this->_logout($I);
        $this->_login($I, $username2, $password2);
        $I->deleteHeader('content-type');
        $data = ['description' => $desc];
        $files = ["image" => $image_file_path];
        $I->sendPost('post?api_key=your_api_key', $data, $files);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains("successfully");

        // check posts of the first username
        $username = $username1;
        $I->sendGet('posts?api_key=your_api_key&user='.$username);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $this->_postRecordMatchJsonType($I);

        // check username
        $response_body = json_decode($I->grabResponse(), true);
        $upload_records = $response_body["data"];
        $count = 0;
        foreach($upload_records as $record){
            $count++;
            $I->assertEquals($username, $record["username"], "Uploader of this post should be " . $username);
        }
        $I->assertGreaterThan(0, $count, "The number of records of ".$username." should be greater than 0.");

        // check posts of the second username
        $username = $username2;
        $I->sendGet('posts?api_key=your_api_key&user='.$username);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $this->_postRecordMatchJsonType($I);

        // check username
        $response_body = json_decode($I->grabResponse(), true);
        $upload_records = $response_body["data"];
        $count = 0;
        foreach($upload_records as $record){
            $count++;
            $I->assertEquals($username, $record["username"], "Uploader of this post should be " . $username);
        }
        $I->assertGreaterThan(0, $count, "The number of records of ".$username." should be greater than 0.");
    }

    public function fetchPostsSuccessfullyOrderByTime(ApiTester $I){
        // the following items should be checked
        $count_to_fetch = 5;
        $order = null;

        // default to the ascending order
        $I->sendGet('posts?api_key=your_api_key&sortby=time&limit='.$count_to_fetch);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $this->_postRecordMatchJsonType($I);

        // check the order and number of posts fetched
        $time = 0;
        $count = 0;
        $response_body = json_decode($I->grabResponse(), true);
        $upload_records = $response_body["data"];
        $count = 0;
        foreach($upload_records as $record){
            $count++;
            $time_uploaded = strtotime($record["timestamp"]);
            $I->assertGreaterOrEquals($time, $time_uploaded, "The uploaded time is ascending.");
            $time = $time_uploaded;
        }
        $I->assertLessOrEquals($count_to_fetch, $count, sprintf("The actually fetched posts count %d should be less than or equal to %d", $count, $count_to_fetch));
        
        // check the other order
        $order = "des";
        $I->sendGet('posts?api_key=your_api_key&sortby=time&order='.$order.'&limit='.$count_to_fetch);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $this->_postRecordMatchJsonType($I);

        // check the order and number of posts fetched
        $time = time() + 8 * 3600;
        $count = 0;
        $response_body = json_decode($I->grabResponse(), true);
        $upload_records = $response_body["data"];
        $count = 0;
        foreach($upload_records as $record){
            $count++;
            $time_uploaded = strtotime($record["timestamp"]);
            $I->assertLessOrEquals($time, $time_uploaded, "The uploaded time is descending.");
            $time = $time_uploaded;
        }
        $I->assertLessOrEquals($count_to_fetch, $count, sprintf("The actually fetched posts count %d should be less than or equal to %d", $count, $count_to_fetch));
    }

    public function fetchPostsSuccessfullyWithOffest(ApiTester $I){
        // the following items should be checked
        $count_to_fetch = 6;
        $offset = rand(1,$count_to_fetch-1);

        // default offset is 0
        $I->sendGet('posts?api_key=your_api_key&sortby=time&limit='.$count_to_fetch);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $this->_postRecordMatchJsonType($I);

        $response_body = json_decode($I->grabResponse(), true);
        $upload_records_no_offset = array_slice($response_body["data"], $offset);

        // fetch posts by an offset of [$offset]
        $I->sendGet('posts?api_key=your_api_key&sortby=time&limit='.$count_to_fetch.'&start='.$offset);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Query successfully");
        $this->_postRecordMatchJsonType($I);

        $response_body = json_decode($I->grabResponse(), true);
        $upload_records_with_offset = $response_body["data"];
        
        // check whether the offset works normally
        foreach($upload_records_no_offset as $index=>$record){
            $I->assertEquals($record["username"], $upload_records_with_offset[$index]["username"]);
            $I->assertEquals($record["timestamp"], $upload_records_with_offset[$index]["timestamp"]);
            $I->assertEquals($record["url"], $upload_records_with_offset[$index]["url"]);
            $I->assertEquals($record["description"], $upload_records_with_offset[$index]["description"]);
        }
    }
}
