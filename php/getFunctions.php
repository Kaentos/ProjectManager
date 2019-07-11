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

    // Task Comments
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

// ISSUES FUNCTIONS

    // Get single issue
    function getSingleIssue($conn, $projectID, $issueID){
        $issueData = array();
        $query = "SELECT i.*, s.name AS status, s.badge FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id WHERE p.id=$projectID AND i.id=$issueID";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                $issueData = $result->fetch_array(MYSQLI_ASSOC);
                $Temp = getUsername($conn,$issueData["idCreator"]);
                $issueData["idCreator"] = $Temp;
                $Temp = getUsername($conn,$issueData["idUpdateUser"]);
                $issueData["idUpdateUser"] = $Temp;
                return $issueData;
            } elseif ($result->num_rows > 1) {
                die("report with error t2");
            } elseif ($result->num_rows == 0) {
                return false;
            } else {
                die();
            }
        } else {
            die("issue");
        }
        die();
    }

    // Get issues 5 for a given project ID
    function get5Issues($conn, $projectID){
        $issuesData = array();
        $query = "SELECT i.*, s.name AS status, s.badge FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id WHERE p.id=$projectID ORDER BY i.lastupdatedDate DESC LIMIT 5";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($issuesData, $row);
                }
            } elseif ($result->num_rows == 0) {
                return;
            } else {
                die();
            }
        } else {
            die();
        }
        return $issuesData;
    }


    // Issue status
    function getIssuesStatus($conn){
        $data = array();
        $query = "SELECT * FROM istatus ORDER BY name;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows > 0){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($data, $row);
                }
            } else {
                die("Error IS0");
            }
        } else {
            die();
        }
        return $data;
    }

    // Issue Comments
    function getIssueComments($conn, $issueID){
        $Data = array();
        $query = "SELECT * FROM issuecomments WHERE idIssue=$issueID ORDER BY creationDate;";
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

// END OF ISSUES



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
            printf("Can't get user session data");
            die();
        }
        die();
    }

    function getUserCountryName($conn, $UserData){
        $query = "SELECT c.name FROM user as u JOIN countries as c ON u.idCountry = c.id  WHERE u.id=$UserData[id]";
        if ($result = $conn->query($query)) {
            $teste = $result;
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    return $row["name"];
                } else {
                    die();
                }
            } else {
                return "None selected";
            }
            $result->close();
        } else {
            die();
        }
        die();
    }

    function getUserSQuestion($conn, $UserData){
        $query = "SELECT s.question FROM user as u JOIN usersecurity as s ON u.id = s.idUser  WHERE u.id=$UserData[id]";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    return $row["question"];
                } else {
                    die();
                }
            } else {
                die();
            }
            $result->close();
        } else {
            die();
        }
        die();
    }


// END USER FUNCTIONS

// Miletones

    function getMilestone($conn, $projectID, $mileID){
        $milesData = array();
        $query = "SELECT m.*, s.name AS status, s.badge FROM milestones AS m INNER JOIN projects AS p ON m.idProject=p.id INNER JOIN mstatus AS s ON m.idStatus=s.id WHERE p.id=$projectID AND m.id=$mileIDID";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                $milesData = $result->fetch_array(MYSQLI_ASSOC);
                $Temp = getUsername($conn,$milesData["idCreator"]);
                $milesData["idCreator"] = $Temp;
                $Temp = getUsername($conn,$milesData["idUpdateUser"]);
                $milesData["idUpdateUser"] = $Temp;
                return $milesData;
            } elseif ($result->num_rows > 1) {
                die();
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

    function getMilestoneStatus($conn){
        $data = array();
        $query = "SELECT * FROM mstatus ORDER BY name;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows > 0){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($data, $row);
                }
            } else {
                die("Error MS0");
            }
        } else {
            die();
        }
        return $data;
    }


// END milestones

?>