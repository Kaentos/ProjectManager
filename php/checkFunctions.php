<?php
    
    
    // Project

    function checkProjectStatusID($conn, $id){
        // Get project data
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

    // END PROJECT

    // Task

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
?>