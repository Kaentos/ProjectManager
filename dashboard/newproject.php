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

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $nameERR = $desERR = -1;

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

    if (isset($_POST["projectC"])) {
        $pname = $_POST["pname"];
        $pdes = $_POST["pdes"];
        if (strlen($pname) > 20 || empty($pname)){
            $nameERR = 0;
            $desERR = 1;
        } 
        if (strlen($pdes) > 60 || empty($pdes)){
            $nameERR = 1;
            $desERR = 0;
        }
        addProject($conn, $pname, $pdes, $UserData, $InviteCode);
    }

    function addProject($conn, $pname, $pdes, $UserData, $InviteCode){
        $currentDate = date("Y-m-d h:i:s");
        $status = 2;
        $role = 1;
        if(!($stmt = $conn->prepare("INSERT INTO projects (name, des, code, idStatus, idCreator, creationDate, lastupdatedDate) VALUES (?,?,?,?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("sssiiss", $pname, $pdes, $InviteCode, $status, $UserData["id"], $currentDate, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $last_id = mysqli_insert_id($conn);
            $stmt->close();
        }

        if(!($stmt = $conn->prepare("INSERT INTO projectmembers (idProject, idUser, idRole) VALUES (?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("iii", $last_id, $UserData["id"], $role)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            $query = "DELETE FROM projects WHERE id=$last_id";
            if($result = $conn->query($query)){
                die("Report with error NPD");
            }
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            header("location: projects.php");
        }
        return;
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
                        <span style="font-size:2rem; font-weight: 500;">New project</span>
                    </div>
                    <hr>
                    <form method="post" class="row" action="">
                        <div class="col-md-12">
                            Project name:
                            <div class="form-group">
                                <?php
                                    if ($nameERR == 0){
                                        echo "
                                        <input type='text' class='form-control is-invalid' name='pname' autocomplete='off' value='$pname' />
                                        <div class='invalid-feedback'>
                                            Must be have 1 to 60 characters.
                                        </div>
                                        ";
                                    } elseif ($nameERR == 1){
                                        echo "
                                        <input type='text' class='form-control is-valid' name='pname' autocomplete='off' value='$pname' />
                                        <div class='valid-feedback'>
                                            Good to go!
                                        </div>
                                        ";
                                    } else {
                                        echo "<input type='text' class='form-control' name='pname' autocomplete='off' />";
                                    }
                                ?>
                            </div>
                            Small description:
                            <div class="form-group">
                                <?php
                                    if ($desERR == 0){
                                        echo "
                                        <input type='text' class='form-control is-invalid' name='pdes' autocomplete='off' value='$pdes' />
                                        <div class='invalid-feedback'>
                                            Must be have 1 to 60 characters.
                                        </div>
                                        ";
                                    } elseif ($desERR == 1){
                                        echo "
                                        <input type='text' class='form-control is-valid' name='pdes' autocomplete='off' value='$pdes' />
                                        <div class='valid-feedback'>
                                            Good to go!
                                        </div>
                                        ";
                                    } else {
                                        echo "<input type='text' class='form-control' name='pdes' autocomplete='off' />";
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            Link to invite users to project:
                            <div class="form-group">
                                <span class="form-control is-valid" readonly>http://localhost/projectmanager/invite/?code=<?php echo $InviteCode ?></span>
                                <div class='valid-feedback'>
                                    Link is valid after project creation!
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <input type="submit" class="btn btn-success" name="projectC" value="Create"/>
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