<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        error_reporting(E_ERROR | E_PARSE);
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-OF-I"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-GF-I"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-AF-I"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-CF-I"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            die(header("Location: /projectmanager/errors/?id=CI-SC-I"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            die(header("Location: /projectmanager/errors/?id=CI-DF-I"));
        }
        
        $conn = ConnectRoot();

        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    // Get code verification
    if(isset($_GET["code"]) && strlen($_GET["code"]) == 12){
        $InviteCode = $_GET["code"];
    } else {
        echo "
            <script>
                alert('Invalide invite code!');
                window.location.href='/projectmanager/dashboard/newproject';
            </script>
        ";
    }

    if (isset($InviteCode)){
        $projectCodeID = checkCode($conn, $InviteCode);
        if(isset($projectCodeID) && is_numeric($projectCodeID)){
            if (!is_numeric(checkUserInProject($conn, $projectCodeID, $UserData["id"]))){
                addUserToProject($conn, $projectCodeID, $UserData["id"]);
            } else {
                echo "
                    <script>
                        alert('Already in this project!');
                        window.location.href='/projectmanager/dashboard';
                    </script>
                ";
            }
        } else {
            echo "
                <script>
                    alert('Invalid invite code!');
                    window.location.href='/projectmanager/dashboard/newproject';
                </script>
            ";
        }
    }
?>