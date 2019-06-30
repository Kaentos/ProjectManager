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

    include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";

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
                // getIssues();
            }
        } else {
            // header("location: /projectmanager/dashboard/projects.php");
        }
    }

    // Project Data
    function getData($conn, $projectID, $userID){
        // Get project data
        $query = "SELECT p.*, s.name as Sname, s.badge, u.username FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE p.id=$projectID AND pm.idUser=$userID;";
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

    // Tasks Data
    function getTasks($conn, $projectID){
        $tasksData = array();
        $query = "SELECT t.*, s.name AS status, s.badge FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id WHERE p.id=$projectID ORDER BY t.lastupdatedDate DESC LIMIT 5";
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

    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);
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
                            <?php
                                echo "
                                    $projectData[name]
                                    <span class='badge badge-$projectData[badge]'>$projectData[Sname]</span>   
                                ";
                                if ($UserRole < 3){
                                    echo "
                                        <a href='#' class='edit-pen'>
                                            <i class='fas fa-pen'></i>
                                        </a>
                                    ";
                                }
                            ?>
                        </span>
                        <br>
                        <span style="font-size:1.3rem; font-weight: 400;">
                            <?php
                                echo $projectData["des"];
                            ?>        
                        </span>
                    </div>
                    <hr>
                    <div class="row d-flex justify-content-center">

                        <!-- Tasks -->
                        <div class="col-lg-12 col-xl-5 task-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12 col-xl-6" style="margin-top:5px;">
                                    <span class="task-DIV-title">Last updated tasks</span>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="btn-group mr-2 DIV-btn-float" style="margin-top:5px">
                                        <a href="#" class="btn btn-success task-DIV-btn">All tasks</a>
                                    </div>
                                    <div class="btn-group mr-2 DIV-btn-float" style="margin-top:5px">
                                        <a href="#" class="btn btn-success task-DIV-btn">New task</a>
                                    </div>
                                </div>
                            </div>
                            <hr class="hr-task">
                            <div style="word-break: break-word;">
                                <?php
                                if(isset($tasksData)){
                                    foreach($tasksData as $task){
                                        echo "
                                        <span class='task-DIV-list'>
                                            <a href='/projectmanager/project/tasks.php?id=$projectData[id]&task=$task[id]'>
                                                $task[name]
                                            </a>
                                            <span class='badge badge-$task[badge]'>$task[status]</span>";
                                            if ($UserRole < 3){
                                                echo "
                                                    <a href='#' class='edit-pen'>
                                                        <i class='fas fa-pen'></i>
                                                    </a>
                                                ";
                                            }
                                        echo "
                                        </span>
                                        <p style='font-size:1.1rem'>
                                            $task[Des]
                                        </p>
                                        ";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <!-- End Tasks -->
                        
                        <!-- col to space out cols -->
                        <div class="col-md-1">
                        </div>
                        
                        <!-- Issues -->
                        <div class="col-lg-12 col-xl-5 issue-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12 col-xl-6" style="margin-top:5px;">
                                    <span class="issue-DIV-title">Last created issues</span>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="btn-group mr-2 DIV-btn-float" style="margin-top:5px">
                                        <a href="#" class="btn btn-danger issue-DIV-btn">All Issues</a>
                                    </div>
                                    <div class="btn-group mr-2 DIV-btn-float" style="margin-top:5px">
                                        <a href="#" class="btn btn-danger issue-DIV-btn">New issue</a>
                                    </div>
                                </div>
                            </div>
                            <hr class="hr-issue">
                            <div style="word-break: break-word;">
                                <?php
                                if(isset($tasksData)){
                                    foreach($tasksData as $task){
                                        echo "
                                        <span style='font-size:1.3rem; font-weight: bold;'>
                                            $task[name]
                                            <span class='badge badge-$task[badge]'>$task[status]</span>    
                                        </span>
                                        <p style='font-size:1.1rem'>
                                            $task[Des]
                                        </p>
                                        ";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <!-- END Issues -->


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