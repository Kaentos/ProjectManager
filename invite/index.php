<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            header("Location: /projectmanager/errors/?id=FIM-OF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            sendError("FIM-GF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            sendError("FIM-SC");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            sendError("FIM-ADD");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            sendError("FIM-SCF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            sendError("FIM-DBF");
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