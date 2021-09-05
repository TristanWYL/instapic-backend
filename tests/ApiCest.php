<?php
class ApiCest 
{    
    public function apiKeyMissing(ApiTester $I)
    {
        $I->sendGet('url_any');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('api_key NOT FOUND');
    }

    public function apiKeyWrong(ApiTester $I)
    {
        $I->sendGet('url_any?api_key=xxxxxxxx');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::PAYMENT_REQUIRED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('api_key NOT RECOGNIZED');
    }

    public function endpointNotExist(ApiTester $I){
        $random_endpoint = "xxxx";
        $I->sendGet($random_endpoint.'?api_key=your_api_key');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Requested endpoint '$random_endpoint' does not exist");
    }
}