<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php";
        $conn = ConnectRoot();

        $UserData = array();
        $query = "SELECT * FROM  user WHERE id=".$_SESSION["user"]["id"];
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $UserData += ["id" => $row["id"]];
                    $UserData += ["username" => $row["username"]];
                    $UserData += ["role" => $row["role"]];
                    $_SESSION["user"]["role"] = $row["role"];
                } else {
                    printf("MAJOR ERROR CAN'T CONVERT USER ROW TO ARRAY");
                    die();
                }
            } else {
                die();
            }
            $result->close();
        } else {
            printf("Error in select user query");
            die();
        }
    }

    // Get code verification
    if(!isset($_GET["code"])){
        $hasInvite = false;
    } else {
        $hasInvite = true;
        $InviteCode = $_GET["code"];
    }
    // END get code verification

    // Checks current code if verification is true
    if($hasInvite){
        $ProjectData = checkCode($conn, $UserData, $InviteCode);
    }
    if(isset($ProjectData)){
        $InProject = checkInProject($conn, $ProjectData, $UserData);
    }
    if(isset($InProject)){
        if($InProject) {
            echo "
                <script>
                    alert('You are already in the project: $ProjectData[name].');
                    window.location.href='/projectmanager/dashboard/projects';
                </script>
            ";
        } else {
            addUserToProject($conn, $ProjectData, $UserData);
        }
    }
    // END check current code

    function checkCode($conn, $UserData, $code){
        if(!($stmt = $conn->prepare("SELECT * FROM projects WHERE code=?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("s", $code)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            if (!$result = $stmt->get_result()){
                die();
            } else{
                if ($result->num_rows == 1){
                    if(!$ProjectData = $result->fetch_assoc()){
                        die();
                    }
                    return $ProjectData;
                } elseif ($result->num_rows > 1) {
                    die();
                } else {
                    return;
                }
            }   
        }
        die();
    }

    function checkInProject($conn, $ProjectData, $UserData){
        $query = "SELECT * FROM projectmembers WHERE idProject=$ProjectData[id] AND idUser=$UserData[id];";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                return true;
            } elseif ($result->num_rows == 0) {
                return false;
            } else {
                die();
            }
            $result->close();
        } else {
            die();
        }
        die();
    }

    function addUserToProject($conn, $ProjectData, $UserData){
        $memberRole = 4;
        $query = "INSERT INTO projectmembers (idProject, idUser, idRole) VALUES ('$ProjectData[id]', '$UserData[id]', $memberRole);";
        if (!$conn->query($query)) {
            die("$ProjectData[id], $UserData[id], $memberRole");
        } else {
            header("Location: /projectmanager/dashboard/projects");
        }
        return;
    }

    // Check input code
    if(isset($_POST["validateCode"])){
        if(isset($_POST["code"])){
            $InputCode = $_POST["code"];
        }
        $PData = checkCode($conn, $UserData, $InputCode);
        if(isset($PData)){
            if(!checkInProject($conn, $PData, $UserData)){
                addUserToProject($conn, $PData, $UserData);
            } else {
                echo "
                    <script>
                        alert('You are already in the project: $PData[name].');
                        window.location.href='/projectmanager/dashboard/projects';
                    </script>
                ";
            }
        }

    }
    // END check input code
?>