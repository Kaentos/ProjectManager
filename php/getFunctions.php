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

    // Project status
    function getProjectUserRoles($conn){
        $Data = array();
        $query = "SELECT * FROM proles ORDER BY id;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows > 0){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($Data, $row);
                }
            } else {
                die("Error PUR0");
            }
        } else {
            die();
        }
        return $Data;
    }

    // Get 5 members for a given project ID
    function get5Members($conn, $projectID){
        $membersData = array();
        $query = "SELECT u.id AS userID, u.username, r.* FROM projects AS p INNER JOIN projectmembers AS pm ON p.id=pm.idProject INNER JOIN proles AS r ON pm.idRole = r.id INNER JOIN user AS u ON pm.idUser=u.id WHERE p.id=$projectID ORDER BY r.id LIMIT 5";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($membersData, $row);
                }
            } elseif ($result->num_rows == 0) {
                return;
            } else {
                die();
            }
        } else {
            die();
        }
        return $membersData;
    }
    
    // END PROJECT FUNCTIONS


    // TASK FUNCTIONS

    // Task status

    // Get single task
    function getSingleTask($conn, $projectID, $taskID){
        $taskData = array();
        $query = "SELECT t.*, s.name AS status, s.badge FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id WHERE p.id=$projectID AND t.id=$taskID";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                $taskData = $result->fetch_array(MYSQLI_ASSOC);
                $Temp = getUsername($conn,$taskData["idCreator"]);
                $taskData["idCreator"] = $Temp;
                $Temp = getUsername($conn,$taskData["idUpdateUser"]);
                $taskData["idUpdateUser"] = $Temp;
                return $taskData;
            } elseif ($result->num_rows > 1) {
                die("report with error t2");
            } elseif ($result->num_rows == 0) {
                return false;
            } else {
                die();
            }
        } else {
            die();
        }
        die();
    }

    // Get tasks 5 for a given project ID
    function get5Tasks($conn, $projectID){
        $tasksData = array();
        $query = "SELECT t.*, s.name AS status, s.badge FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id WHERE p.id=$projectID ORDER BY t.lastupdatedDate DESC LIMIT 5";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($tasksData, $row);
                }
            } elseif ($result->num_rows == 0) {
                return;
            } else {
                die();
            }
        } else {
            die();
        }
        return $tasksData;
    }


    // Task status
    function getTasksStatus($conn){
        $data = array();
        $query = "SELECT * FROM tstatus ORDER BY name;";
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

    function getTaskComments($conn, $taskID){
        $Data = array();
        $query = "SELECT * FROM taskcomments WHERE idTask=$taskID ORDER BY creationDate;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows > 0){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Temp = getUsername($conn, $row["idUser"]);
                    $row += ["username" => $Temp];
                    array_push($Data, $row);
                }
                return $Data;
            } else {
                return false;
            }
        } else {
            die();
        }
        return false;
    }

    // END OF TASK



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