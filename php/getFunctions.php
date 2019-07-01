<?php

    // Others

    function getCurrentDate(){
        return date("Y-m-d H-i-s");
    }

    // END OTHERS


    // PROJECT FUNCTIONS

    // Project Data
    function getSingleProjectData($conn, $projectID, $userID){
        // Get project data
        $query = "SELECT p.*, s.name as Sname, s.badge, u.username FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE p.id=$projectID AND pm.idUser=$userID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Temp = getUsername($conn,$row["idCreator"]);
                    $row["idCreator"] = $Temp;
                    $Temp = getUsername($conn,$row["idUpdateUser"]);
                    $row["idUpdateUser"] = $Temp;
                    return $row;
                }
            } elseif ($result->num_rows > 1) {
                die("Error P2, report with error code and project name");
            } else {
                return;
            }
        } else {
            die();
        }
    }

    // Get user role in project
    function getUserProjectRole($conn, $projectID, $userID){
        $query = "SELECT pm.idRole FROM projects AS p INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE p.id=$projectID AND pm.idUser=$userID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if($row = $result->fetch_array(MYSQLI_ASSOC)){
                    return $row["idRole"];
                }
            } elseif ($result->num_rows > 1) {
                die("Error PM2, report with error code and project name");
            } else {
                return;
            }
        } else {
            die();
        }
    }

    // Project status
    function getProjectStatus($conn){
        $data = array();
        $query = "SELECT * FROM pstatus ORDER BY name;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows > 0){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($data, $row);
                }
            } else {
                die("Error PS0");
            }
        } else {
            die();
        }
        return $data;
    }
    
    // END PROJECT FUNCTIONS


    // USER FUNCTIONS

    // Get username
    function getUsername($conn, $userID){
        $query = "SELECT username FROM user WHERE id=$userID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if($row = $result->fetch_array(MYSQLI_ASSOC)){
                    return $row["username"];
                }
            } elseif ($result->num_rows > 1) {
                die("Error U2, report with error code");
            } else {
                return;
            }
        } else {
            die();
        }
    }

    function getSessionUserData($conn, $sessionData) {
        $UserData = array();
        $query = "SELECT * FROM  user WHERE id=".$sessionData["id"];
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $UserData += ["id" => $row["id"]];
                    $UserData += ["username" => $row["username"]];
                    $UserData += ["email" => $row["email"]];
                    $UserData += ["role" => $row["role"]];
                    $_SESSION["user"]["role"] = $row["role"];
                    return $UserData;
                } else {
                    die();
                }
            } else {
                die();
            }
            $result->close();
        } else {
            printf("Error in select user query");
            die();
        }
        die();
    }

    // END USER FUNCTIONS

?>