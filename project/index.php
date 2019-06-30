<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
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

    if (isset($_GET["id"]) && is_numeric($_GET["id"])){
        $projectID = $_GET["id"];
    } else {
        // header("location: /projectmanager/dashboard/projects.php");
    }

    if (isset($projectID)){
        $projectData = getData($conn, $projectID, $UserData["id"]);
        if (isset($projectData)){
            $tasksData = getTasks($conn, $projectID);
            if(isset($tasksData)){
                getIssues();
            }
        } else {
            // header("location: /projectmanager/dashboard/projects.php");
        }
    }

    function getData($conn, $projectID, $userID){
        // Get project data
        $query = "SELECT p.*, s.name as Sname, u.username FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE p.id=$projectID AND pm.idUser=$userID;";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if($row = $result->fetch_array(MYSQLI_ASSOC)){
                    return $row;
                }
            } elseif ($result->num_rows > 1) {
                die("Error P2, report with error code and project name");
            } else {
                return;
            }
        } else {
            die();
        }
    }

    function getTasks($conn, $projectID){
        $tasksData = array();
        $query = "SELECT t.* FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id WHERE p.id=$projectID";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($tasksData, $row);
                }
            } elseif ($result->num_rows == 0) {
                return;
            } else {
                die();
            }
        } else {
            die();
        }
        return $tasksData;
    }

    function getIssues(){

    }
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title><?php echo $projectData["name"] ?></title>
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
                        <span style="font-size:2rem; font-weight: 500;">
                            Project: <?php echo $projectData["name"]; ?>
                            <span class="badge badge-primary">{STATUS}</span>    
                        </span>
                    </div>
                    <hr>
                    <div class="row d-flex justify-content-center">

                        <!-- Tasks -->
                        <div class="col-md-5" style="background-color: rgba(0,0,0,.1); margin-bottom: 10px; border: 5px solid green">
                            <div style="margin-top:15px">
                                <span style="font-size:1.6rem; font-weight: 500;"> Tasks (last 10) </span>
                                <span class="float-right" style="font-size:1.6rem; font-weight: 500;"> All tasks </span>
                            </div>
                            <hr style="border-color: green">
                            <div style="word-break: break-word;">
                                <?php
                                if(isset($tasksData)){
                                    foreach($tasksData as $task){
                                        print_r($task);
                                        echo "
                                            <p>$task[name]</p>
                                        ";
                                    }
                                }
                                ?>
                                <span style="font-size:1.3rem; font-weight: bold;">
                                    {TITLE}
                                    <span class="badge badge-primary">{STATUS}</span>    
                                </span>
                                <p style="font-size:1.1rem">
                                    {Descrição}
                                </p>
                            </div>
                        </div>
                        <!-- End Tasks -->
                        
                        <!-- col to space out cols -->
                        <div class="col-md-1">
                        </div>
                        
                        <!-- Issues -->
                        <div class="col-md-5" style="background-color: rgba(0,0,0,.1); margin-bottom: 10px; border: 5px solid red;">
                            <div style="margin-top:15px">
                                <span style="font-size:1.6rem; font-weight: 500;"> Issues (last 10) </span>
                                <span class="float-right" style="font-size:1.6rem; font-weight: 500;"> All Issues </span>
                            </div>
                            <hr style="border-color: red">
                            <div>
                            
                            </div>
                        </div>
                        <div class="col-md-6" style="background-color: rgba(0,0,0,.1);">
                            <span style="font-size:1.6rem; font-weight: 500;"> Members </span>
                            <span style="font-size:1.6rem; font-weight: 500;" class="float-right">All members</span>
                            <hr style="border-color: blue; margin-top: 5px">
                        </div>
                    </div>
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