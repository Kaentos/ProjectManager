<?php
    
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function showAlert($info){
        echo "<script type='text/javascript'>alert('$info');</script>";
    }

    // Generate Invite Code
    function otherGenInviteCode($conn){
        $Invalid = true;
        do {
            $InviteCode = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 12);
            $query = "SELECT code FROM projects WHERE code='$InviteCode'";
            if ($result = $conn->query($query)) {
                if ($result->num_rows == 0){
                    $Invalid = false;
                } elseif($result->num_rows > 1) {
                    die("Report with error I2");
                }
                $result->close();
            } else {
                die();
            }
        } while($Invalid);
        return $InviteCode;
    }

?>