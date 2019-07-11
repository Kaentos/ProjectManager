<?php
    function ConnectRoot(){
        $dbHost = "localhost";
        $dbUser = "root";
        $dbPassword = "";
        $dbName = "pmanager";
        $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
        if ($conn->connect_errno) {
            printf("Connect failed: %s\n", $conn->connect_error);
            die();
        }
        return $conn;
    }
?>