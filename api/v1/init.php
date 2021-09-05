<?php
// This script should be run directly for initializing the project

// Database Initialization
// create tables
$sql_create_table_user = "CREATE TABLE IF NOT EXISTS user (
        userid INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(20) NOT NULL UNIQUE,
        password VARCHAR(41) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_crate_table_post = "CREATE TABLE IF NOT EXISTS post (
        postid INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
        userid INTEGER UNSIGNED NOT NULL,
        picture VARCHAR(40) NOT NULL,
        desc_ TEXT NOT NULL,
        createdtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (userid) REFERENCES user(userid)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// database credentials
require "config.php";
// connect to database
$conn = new mysqli($hostname, $username, $password, $db);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . mysqli_connect_error() . "<br>");
}
echo "Connected successfully\n";
// query
if ($conn->query($sql_create_table_user) === TRUE) {
    echo "Table user created successfully\n";
} else {
    echo "Error creating table user: " . $conn->error . "\n";
}
if ($conn->query($sql_crate_table_post) === TRUE) {
    echo "Table post created successfully\n";
} else {
    echo "Error creating table post: " . $conn->error . "\n";
}

$conn->close();