<?php
    // Check if user hasn't do anything in 30min if he didn't do anything, destroys session
    if (time() - $_SESSION['user']['lastActivity'] > 1800) {
        session_destroy();
        header("Location: /projectmanager");
    } else {
        $_SESSION['user']['lastActivity'] = time();
    }
?>