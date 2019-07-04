<?php

// TASK
    
    // USER FOLLOW
    function removeUserTaskFollow($conn, $taskID, $userID){
        $query = "DELETE FROM taskfollow WHERE idTask=$taskID AND idUser=$userID";
        if (!$conn->query($query)) {
            die();
        } else {
            header("Refresh: 0");
            return;
        }
        die();
    }

// END OF TASK

// PROJECT

    // Remove user from project
    function removeUserFromProject($conn, $userID, $projectID){
        $query = "DELETE FROM projectmembers WHERE idProject=$projectID AND idUser=$userID";
        if (!$conn->query($query)) {
            die();
        }
        $query = "DELETE FROM taskfollow WHERE idUser=$userID";
        if (!$conn->query($query)) {
            die();
        }
        header("Refresh: 0");
    }

// END PROJECT

?>