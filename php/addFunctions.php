<?php 

    // New task
    function addNewTask($conn, $projectID, $userID, $task){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO tasks (idProject, name, des, idStatus, idCreator, idUpdateUser, creationDate, lastupdatedDate) VALUES (?,?,?,?,?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("issiiiss", $projectID, $task["name"], $task["des"], $task["status"], $userID, $userID, $currentDate, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $last_id = mysqli_insert_id($conn);
            $stmt->close();
        }
        header("Refresh: 0");
    }

?>