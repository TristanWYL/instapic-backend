<?php
class UserCest
{
    public function _before(ApiTester $I)
    {
      
    }

    public function signupFailedDueToWrongFormProvided(\ApiTester $I)
    {
        // wrong username key
        require "config.php";
        $url = "signup";
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'username1' => 'xxxxxxxx',
          'password' => $password1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Request format error");

        // missing username
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'password' => $password1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Request format error");

        // missing password
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'username' => $username1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Request format error");

        // inappropriate username
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'username' => $username1,
          'password' => 'xxxxxxxx'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN); 
        $I->seeResponseIsJson();
        $I->seeResponseContains("The username has been used, try another username!");
    }

    public function signupSuccessfully(\ApiTester $I)
    {
        $new_user_info = [
          'username' => 'test_user_name',
          'password' => 'test_password'
        ];
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost('signup?api_key=your_api_key', $new_user_info);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains('successfully');
        $new_user_info["password"] = sha1($new_user_info["password"]);
        $I->seeInDatabase('user', $new_user_info);
    }

    public function signinFailedDueToWrongFormProvided(\ApiTester $I)
    {
        require "config.php";
        // wrong username key
        $url = "signin";
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'username1' => $username1,
          'password' => $password1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Request format error");

        // missing username
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'password' => $password1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Request format error");

        // missing password
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'username' => $username1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Request format error");

        // incorrect username and/or password
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost($url.'?api_key=your_api_key', [
          'username' => 'xxxx',
          'password' => $password1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN); 
        $I->seeResponseIsJson();
        $I->seeResponseContains("Incorrect username or password!");
    }

    public function signinSuccessfully(\ApiTester $I)
    {
        require "config.php";
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost('signin?api_key=your_api_key', [
          'username' => $username1,
          'password' => $password1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains("successfully");
    }

    public function logout(\ApiTester $I)
    {
        $I->sendPost('signout?api_key=your_api_key');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains('successfully');
    }

    public function loginStatusQuery(\ApiTester $I){
      $this->logout($I);
      // query login status
      $I->sendGet('signin_query?api_key=your_api_key');
      $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
      $I->seeResponseIsJson();
      $I->seeResponseContains('Not logged in');

      $this->signinSuccessfully($I);
      $I->sendGet('signin_query?api_key=your_api_key');
      $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
      $I->seeResponseIsJson();
      $I->seeResponseContains('Have logged in');
    }
}
