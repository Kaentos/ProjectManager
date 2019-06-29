<?php
    // Check if user hasn't do anything in 30min if he didn't do anything, destroys session
    // If he didn't reach 30min reset session time
    if (time() - $_SESSION['user']['lastActivity'] > 1800) {
        session_destroy();   // destroy session data in storage
        header("Location: /projectmanager");
    } else {
        $_SESSION['user']['lastActivity'] = time();
    }
?>