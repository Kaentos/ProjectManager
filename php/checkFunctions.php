<?php
    // USER

    // Check if user is allowed to see project data
    function checkUserInProject($conn, $projectID, $userID){
        $query = "SELECT pm.idRole FROM projects AS p INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE p.id=$projectID AND pm.idUser=$userID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if($row = $result->fetch_array(MYSQLI_ASSOC)){
                    return $row["idRole"];
                }
            } elseif ($result->num_rows > 1) {
                die("Error PM2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

    // END USER
    
    // Project

    function checkProjectID($conn, $id){
        // Get project data
        $query = "SELECT * FROM projects WHERE id=$id;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error TS2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

    function checkProjectStatusID($conn, $id){
        $query = "SELECT * FROM pstatus WHERE id=$id;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error PS2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

    function checkProjectUserRoleID($conn, $id){
        $query = "SELECT * FROM proles WHERE id=$id;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error PR2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

    // END PROJECT

// Task

    function checkUserTaskFollow($conn, $taskID){
        $query = "SELECT * FROM taskfollow WHERE idTask=$taskID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error UTF2");
            } else {
                return false;
            }
        } else {
            die();
        }
        return false;
    }

    function checkTaskID($conn, $id, $projectID){
        // Get project data
        $query = "SELECT * FROM tasks AS t JOIN projects AS p ON t.idProject=p.id WHERE t.id=$id AND p.id=$projectID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error TS2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

    function checkTaskStatusID($conn, $id){
        // Get project data
        $query = "SELECT * FROM tstatus WHERE id=$id;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error TS2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

// END TASK

// Issue

    function checkUserIssueFollow($conn, $issueID){
        $query = "SELECT * FROM issuefollow WHERE idIssue=$issueID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error UIF2");
            } else {
                return false;
            }
        } else {
            die();
        }
        return false;
    }

    function checkIssueID($conn, $id, $projectID){
        $query = "SELECT * FROM issues AS i JOIN projects AS p ON i.idProject=p.id WHERE i.id=$id AND p.id=$projectID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error I2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

    function checkIssueStatusID($conn, $id){
        $query = "SELECT * FROM istatus WHERE id=$id;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error IS2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

// END Issue

// Invite

    function checkCode($conn, $code){
        if(!($stmt = $conn->prepare("SELECT * FROM projects WHERE code=?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("s", $code)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            if (!$result = $stmt->get_result()){
                die();
            } else{
                if ($result->num_rows == 1){
                    if($row = $result->fetch_array(MYSQLI_ASSOC)){
                        return $row["id"];
                    }
                } elseif ($result->num_rows > 1) {
                    die();
                } else {
                    return false;
                }
            }   
        }
        die();
    }

// END Invite
?>