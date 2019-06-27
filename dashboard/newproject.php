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

    $Invalid = true;
    do{
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
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title>Home</title>
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
                                <input type="text" class="form-control" name="name" autocomplete="off" />
                            </div>
                            Small description:
                            <div class="form-group">
                                <input type="text" class="form-control" name="des" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            Link to invite users to project:
                            <div class="form-group">
                                <span class="form-control" readonly>http://localhost/projectmanager/invite/<?php echo $InviteCode ?></span>
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