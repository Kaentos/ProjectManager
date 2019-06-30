<?php

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
        $query = "SELECT * FROM pstatus;";
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

?>