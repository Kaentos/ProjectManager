<?php

// Project

function editProject($conn, $Data, $projectData, $UserData){
    if (!($projectData["name"] == $Data["name"] && $projectData["des"] == $Data["des"] && $projectData["idStatus"] == $Data["status"] && $projectData["code"] == $Data["code"])){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("UPDATE projects SET name=?, des=?, code=?, idStatus=?, idUpdateUser=?, lastupdatedDate=? WHERE id=?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("sssiisi", $Data["name"], $Data["des"], $Data["code"], $Data["status"], $UserData["id"], $currentDate, $projectData["id"])) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            header("Refresh:0");
        }
    }

    return;
}

function editUserRoleInProject($conn, $userID, $projectID, $roleID){
    $query = "UPDATE projectmembers SET idRole=$roleID WHERE idProject=$projectID AND idUser=$userID";
    if (!$conn->query($query)) {
        die();
    } else {
        header("Refresh: 0");
        return;
    }
    die();
}


// END Project

// Task 

function editTask($conn, $Data, $taskData, $UserData){
    if (!($taskData["name"] == $Data["name"] && $taskData["Des"] == $Data["des"] && $taskData["idStatus"] == $Data["status"])){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("UPDATE tasks SET name=?, Des=?, idStatus=?, idUpdateUser=?, lastupdatedDate=? WHERE id=?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("ssiisi", $Data["name"], $Data["des"], $Data["status"], $UserData["id"], $currentDate, $taskData["id"])) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            header("Refresh:0");
        }
    }

    return;
}

// END Task


?>