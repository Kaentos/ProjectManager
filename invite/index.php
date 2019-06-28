<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        $dbHost = "localhost";
        $dbUser = "root";
        $dbPassword = "";
        $dbName = "pmanager";
        $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

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

    if(!isset($_GET["code"])){
        $hasInvite = false;
    } else {
        $hasInvite = true;
        $InviteCode = $_GET["code"];
    }

    if(empty($InviteCode) || !isset($InviteCode)){
        echo "error";
    }

    if($hasInvite){
        if(!($stmt = $conn->prepare("SELECT * FROM projects WHERE code=?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("s", $InviteCode)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            if (!$result = $stmt->get_result()){
                die();
            } else{
                if ($result->num_rows == 1){
                    $hasInvite = true;
                    if(!$ProjectData = $result->fetch_assoc()){
                        die();
                    }
                } elseif ($result->num_rows > 1) {
                    die();
                } else {
                    $hasInvite = false;
                }
            }   
        }
    }


    if($hasInvite){
        $query = "SELECT * FROM projectmembers WHERE idProject=$ProjectData[id] AND idUser=$UserData[id];";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                $InProject = true;
            } elseif ($result->num_rows == 0) {
                $InProject = false;
            } else {
                die();
            }
            $result->close();
        } else {
            die();
        }
    } else {
        echo "Invalid Invite";
    }

    if($InProject){
        echo "";
        echo "
            <script>
                alert('You are already in the project: $ProjectData[name].');
                window.location.href='/projectmanager/dashboard/projects.php';
            </script>
        ";
    } else {
        echo "Valid Invite<br>";
        print_r($ProjectData);
        $query = "INSERT INTO projectmembers (idProject, idUser, idRole) VALUES ('$ProjectData[id]', '$UserData[id]', 5);";
        if (!$conn->query($query)) {
            die();
        } else {
            header("Location: /projectmanager/dashboard/projects.php");
        }
    }
?>