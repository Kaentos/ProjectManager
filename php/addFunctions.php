<?php 

    // Follow task
    function addFollowToTask($conn, $taskID, $userID){
        $query = "INSERT INTO taskfollow (idTask, idUser) VALUES ($taskID, $userID)";
        if (!$conn->query($query)) {
            die();
        } else {
            header("Refresh: 0");
            return;
        }
        die();
    }


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

    // New comment for certain task
    function addTaskNewComment($conn, $taskID, $comment, $userID){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO taskcomments (idTask, idUser, comment, creationDate) VALUES (?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("iiss", $taskID, $userID, $comment, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $stmt->close();
        }
        header("Refresh: 0");
    }

?>