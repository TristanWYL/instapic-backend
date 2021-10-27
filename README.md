- [1. Overview](#1-overview)
  - [1.1. Technology Stack](#11-technology-stack)
- [2. How to deploy](#2-how-to-deploy)
    - [2.0.1. Note](#201-note)
- [3. RESTful API](#3-restful-api)
    - [3.0.1. Note](#301-note)
    - [3.0.2. API's](#302-apis)
    - [3.0.3. Response of the request](#303-response-of-the-request)
    - [3.0.4. Status Code](#304-status-code)
- [4. How to extend the functionality of this api system](#4-how-to-extend-the-functionality-of-this-api-system)
- [5. Test](#5-test)
  - [5.1. How to run the test](#51-how-to-run-the-test)
  - [5.2. Potential issue for generating the coverage report](#52-potential-issue-for-generating-the-coverage-report)
  - [5.3. Note](#53-note)
- [6. Note](#6-note)
- [7. License](#7-license)

# 1. Overview
This repository is a restful api system developed for [the technical assessment](challenge_for_interview.md) of a job interview, which is the backend of a pcture sharing website. One instance of this api system has been deployed at https://instapic.club/.

One client frontend of this api has been developed, which could be found at [another repository](https://github.com/TristanWYL/instapic-frontend.git).

## 1.1. Technology Stack
    Apache + php + MariaDB

# 2. How to deploy

**STEP 1:** Install [XAMPP](https://www.apachefriends.org/index.html) and clone this repository into the document root of the apache server

**STEP 2:** Create a MySQL database/schema and a user for managing the database (using a MySQL client). You could instead execute the following codes:

```sql
CREATE DATABASE IF NOT EXISTS `instapic`;
CREATE USER 'instapic_user'@'localhost' IDENTIFIED BY 'password_for_instapic_user';
GRANT CREATE, SELECT, INSERT, UPDATE, DELETE, DROP, ALTER ON `instapic`.* TO 'instapic_user'@'localhost';
```

Or conduct these operations with a MySQL GUI client.

**STEP 3:** Clone this repository into the webroot directory.

**STEP 4:** Modify *./api/v1/config.php* to suit your case.

**STEP 5:** Run *./api/v1/init.php* with php.exe for initliazing the project

    > php api/v1/init.php

Now, the api is ready to run

### 2.0.1. Note

The directory *$dir_for_picture* specified in *./api/v1/config.php* has to be created before the image files could be uploaded;

# 3. RESTful API

### 3.0.1. Note

Before accessing the api, you have to configure an api key in *./api/v1/config.php*, and for accessing each endpoint, it is required to append *api_key=yourkey* to the url, so that the api system can recognize the client app.

### 3.0.2. API's
POST `/api/v1/signup`
```json
Example call:
    /api/v1/signup?api_key=yourkey
Function: 
    Sign up
Request format:
    Content-Type: application/json
    json example:
        {"username":xx, "password":**}
Response example: 
    {
        "code":200, 
        "msg":"Sign up successfully"
    }
```

POST `/api/v1/signin`
```json
Example call:
    /api/v1/signin?api_key=yourkey
Function: 
    Sign in
Request format:
    Content-Type: application/json
    json example:
        {"username":xx, "password":**}
Response example: 
    {
        "code":200, 
        "msg":"Login successfully"
    }
```

GET `/api/v1/signin_query`
```json
Example call:
    /api/v1/signin_query?api_key=yourkey
Function: 
    Enquire the login status
Response example: 
    {
        "code":200, 
        "msg":"Have logged in.",
        "data":{
            "login":true,
            "username":{current_login_username}
        }
    }
    or
    {
        "code":200, 
        "msg":"Not logged in yet.",
        "data":{
            "login":false,
            "username":null
        }
    }
```

GET `/api/v1/logout`
```json
Example call:
    /api/v1/signout?api_key=yourkey
Function: 
    Log out
Response example: 
    {
        "code":200, 
        "msg":"Sign out successfully"
    }
```

POST `/api/v1/post`
```json
Example call:
    /api/v1/post?api_key=yourkey
Function:
    Post an image to the website
Request format:
    Content-Type: multipart/form-data
    Form example:
        {
            "image":<Choose File>,
            "description":"A short description for the image"
        }
Response example:
    {
        "code":200, 
        "msg":"Post successfully"
    }
```

GET `/api/v1/posts?start={index_of_start_of_posts}&limit={number_of_posts}&user=username`
```json
Example call:
    /api/v1/posts?api_key=yourkey&start=0&limit=5&user=username&sortby=time&order=asc
Function:
    Get a list of uploaded posts
Arguments:
    start:
        [Optinal]
        Function: 
            Search posts starting from [start]
            Should be integer and no less than 0
        Default: 0
    limit:
        [Optional]
        Function:
            Specify the number of posts for each request
            Should be integer and greater than or equal to 1
        Default: 10
    user:
        [Optional]
        Function:
            Filter the posts of the specific user
        Default: 
            All users
    sortby:
        [Optional]
        Function:
            Query posts sorted by 'sortby'
        Available Optioins:
            'user': sort by user name
            'time': sort by uploaded time
        Default:
            Does not sort at all
    order:
        [Optional]
        Function:
            When querying posts, sort the findings by 'sortby' in a lowering or raising order
        Available Optioins:
            'asc': in ascending order
            'des': in descending order
        Default:
            'asc'

Response example:
    {
        "code":200, 
        "msg":"Query successfully",
        "data":[
            {
                "username":"username1",
                "timestamp":"2021-07-17 15:00:00",
                "url":"path/to/the/picture1",
                "description":"short description of the picture1"
            },
            {
                "username":"username2",
                "timestamp":"2021-07-17 16:00:00",
                "url":"path/to/the/picture2",
                "description":"short description of the picture2"
            },
            ...
        ]
    }
    or if no data found from the database
    {
        "code":200, 
        "msg":"Query successfully",
        "data":[]
    }

```

### 3.0.3. Response of the request
For any requests, the response is json data, and the format is in form of:

```json
{
    "code":xx,
    "msg":xx,
    "data":xx
}
```
The fileds of the response is explained in the following table.
|Field|Data Type|Meaning|Optional (Y/N)|
|---|---|---|---|
|code|Integer|Status code for the request|N|
|msg|String| Complementary message for the status code|N|
|data|JSON|The requested data|Y|

### 3.0.4. Status Code

The status code is defined in the following table, or you could refer to the const variables defined in *./api/v1/src/response.php*.

| Code | Meaning |
|---|---|
|200|The request is successful|
|400|The *api_key* is missed in request url|
|401|The identity of the user should be authenticated|
|402|The parameters (key-value pair in url) are invalid|
|403|The data included in POST request are invalid|
|404|The requested endpoint/resource is not found|
|500|The server has an internal error|


# 4. How to extend the functionality of this api system
Currently this api system only supports some simple features which could be referenced in the variable *SupportedApiClassPair* in *./api/v1/config.php*. However, it is easy to extend the functionality.

**STEP 1:** Create a new class for implementing the new feature.

1. Create a new file in *./api/v1/src/action/* and the file name would better be corresponding to the functionality.
   
2. In the created new file, create a class which extends the class `Action`.
   
3. Override the method `action`, and implement the business code in it. The code in this new file should be like:

```php
class MyFunction extends Action{
    public function action(array $request_data = null){
        // Write the business code here
    }
}
```

**STEP 2:** Add one line in the variable *SupportedApiClassPair* in *./api/v1/config.php*, to relate the url to the class.

The variable *SupportedApiClassPair* in *./api/v1/config.php* should be something like:

```php
// This associative array will bind the url request to its corresponding 
// handling class
const SupportedApiClassPair = array(
    ...
    ...
    'my_function' => 'MyFunction' // new line
);
```

**Note:** In the new line, the *key: my_function* will appear in the url, and the *value: MyFunction* should be exactly same as the class name which is just created.

**STEP 3:** Call the api.

Now, you are ready to call the api with the following url:

    /api/v1/my_function?api_key=yourkey

# 5. Test
[Codeception](https://codeception.com/) is applied for testing this API.

## 5.1. How to run the test

1. Tweak the configuation of Db module in *./codeception.yml*, which should strictly follow those settings in *./api/v1/config.php*.

2. Start the test by running:
    
    `php vendor/bin/codecept run --steps`

    If a coverage report is needed, add the `--coverage --coverage-html` option like below:

    `php vendor/bin/codecept run --coverage --coverage-html`

    The coverage report could be checked out at *tests/_output/coverage/*.
    
    By default Codeception runs tests in alphabetic order. If you prefer a random order, add the `-o "settings: shuffle: true"` option like below:

    `php vendor/bin/codecept run --coverage --coverage-html -o "settings: shuffle: true"`

    In addition, if you want to run a single test, try the following command:

    `php vendor/bin/codecept run tests/Cest.php:exampleTest --steps`

## 5.2. Potential issue for generating the coverage report
When you run the test, if you come across with the error: `XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set`, adding the environment variable `XDEBUG_MODE=coverage` to the command will fix it. For example, if you are using Windows OS and a command prompt, the following command should work:

`set XDEBUG_MODE=coverage&& php vendor/bin/codecept run --coverage --coverage-html -o "settings: shuffle: true"`

## 5.3. Note

For the sake of data security, testing environment and production environment should be separated. Currently both environments are not isolated from each other and they share the same codebase. So, users should deploy two sets of this code in both testing environment/server and production environment/server.

# 6. Note

This repository is originally built for a technical interview, so everything is coded for fast prototyping. Due to this, some features seemingly irregarding with the functionality of the website, but actually important have not been incorporated, including:

* applying JWT to protect the api (authenticating the client app and users)

# 7. License

MIT
