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
            addUserToProject($conn, $ProjectData, $UserData);
        }
    }
    // END check

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
        $query = "INSERT INTO projectmembers (idProject, idUser, idRole) VALUES ('$ProjectData[id]', '$UserData[id]', 5);";
        if (!$conn->query($query)) {
            die();
        } else {
            header("Location: /projectmanager/dashboard/projects.php");
        }
        return;
    }

    if(isset($_POST["validateCode"])){
        if(isset($_POST["code"])){
            $InputCode = $_POST["code"];
        }
        $PData = checkCode($conn, $UserData, $InputCode);
        if(isset($PData)){
            if(!checkInProject($conn, $PData, $UserData)){
                addUserToProject($conn, $PData, $UserData);
            }
        }

    }
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title>New project</title>
        <meta name="description" content="Project Manager">
        <meta name="author" content="Miguel Magueijo">
        <link rel="icon" href="/projectmanager/img/icon.png">

        <!-- CSS -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css" integrity="sha384-i1LQnF23gykqWXg6jxC2ZbCbUMxyw5gLZY6UiUS98LYV5unm8GWmfkIS6jqJfb4E" crossorigin="anonymous">
        <!-- Remove comment to get local fontawesome, comment link above -->
        <!-- <link rel="stylesheet" href="/projectmanager/fontawesome/css/all.css"> -->
        <link rel="stylesheet" href="/projectmanager/css/db.css">
        <link rel="stylesheet" href="/projectmanager/css/Custom.css">
        <link rel="stylesheet" href="/projectmanager/css/bootstrap.min.css">
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="container-fluid">
                    <div>
                        <span style="font-size:2rem; font-weight: 500;">Invalid invite code! project</span>
                    </div>
                    <hr>
                    <form method="post" class="row" action="">
                        <div class="col-md-6">
                            Invite code:
                            <div class="form-group">
                                <?php
                                    if(isset($InviteCode)){
                                        $string = "values='$InviteCode'";
                                    } else {
                                        $string = '';
                                    }
                                    echo "
                                        <input type='text' class='form-control is-invalid' name='code' autocomplete='off' $string/>
                                        <div class='invalid-feedback'>
                                            Enter a valid invite code!
                                        </div>
                                    ";
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="submit" class="btn btn-success" name="validateCode" value="Join project"/>
                        </div>
                    </form>
                </div>
            </main>

        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="/projectmanager/js/db.js"></script>
        <script src="/projectmanager/js/bootstrap.min.js"></script>
    </body>
</html>