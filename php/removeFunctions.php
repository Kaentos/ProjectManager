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

?>