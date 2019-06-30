<?php
    
    
    // Project

    function checkStatusID($conn, $id){
        // Get project data
        $query = "SELECT * FROM pstatus WHERE id=$id;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows > 1) {
                die("Error S2, report with error code and project name");
            } else {
                return false;
            }
        } else {
            die();
        }
    }

    // END PROJECT
?>