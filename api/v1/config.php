<?php

// details of database
// Replace the values inside the single quotes below with the values for your MySQL configuration. 
// If not using the default port 3306, then append a colon and port number to the hostname (e.g. $hostname = 'example.com:3307';).
$hostname 	= 'localhost';
$db 		= 'instapic';
$username 	= 'instapic_user';
$password 	= 'xd249fnc4d';

// This associative array will bind the url request to its corresponding 
// handling class
const SupportedApiClassPair = array(
    'signup' => 'Signup',
    'signin' => 'Signin',
    'signin_query' => 'LoginStatusQuery',
    'signout' => 'Signout',
    'post' => 'Post',
    'posts' => 'Posts'
);

// directory for storing the uploaded pictures
// Considering getcwd() will give api/v1, and the pic/ folder 
// is the sibling folder of api, so the directory for storing pictures should look like:
$dir_for_picture = "../../pic/";
// this variable will be used for generating the url for uploaded pictures
$dir_for_picture_url = "pic/";

// an integer indicating by default how many posts should be loaded
// each time the client app fetches the data
$num_of_posts_for_each_request_default = 10;

// a const for registering the client app
// only the client app who has the one of the api key can be recognized by this api system
const API_KEYS = array("your_api_key");

// The maximum size of the uploaded image file
// The one exceeding this will not be allowed
//
// Note: This number should be less than the file size for uploading specified by 
// php.ini (refer to the variables [UPLOAD_MAX_FILESIZE] and [POST_MAX_SIZE]). 
const MAX_PICTURE_SIZE_BYTE = 500000;
?>