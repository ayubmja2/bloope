<?php
    try{
        $dbh = new PDO('mysql:host=localhost;dbname=bloope;', "root", "root");
    }catch(Exception $e){
        die("Could not connect to DB: ".$e->getMessage());
    }
    $db_conn = mysqli_connect("localhost", "root", "root", "bloope");
    if (!$db_conn) {
        echo '{"error": DB_CONNECTION, "message": "' . $mysqli_connect_error() . '"}';
        exit();
    }