<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php";
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    $ProjectData = array();
    $hasProjects = false;
    // Get all projects user is assigned
    $query = "SELECT p.*, s.name as Sname, u.username, pm.idRole AS Role FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE pm.idUser =".$UserData["id"]." ORDER BY p.creationDate DESC LIMIT 5";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasProjects = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($ProjectData, $row);
            }
        } else {
            $hasProjects = false;
        }
        $result->close();
    } else {
        printf("Error in select user query");
        die();
    }

    $TasksData = array();
    $hasTasks = false;
    // Get all tasks user is assigned
    $query = "SELECT t.*, s.name AS status, s.badge, p.id AS projectID FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id INNER JOIN taskfollow AS tf ON t.id=tf.idTask WHERE tf.idUser=$UserData[id] LIMIT 5";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasTasks = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($TasksData, $row);
            }
        } else {
            $hasTasks = false;
        }
        $result->close();
    } else {
        printf("Error in select user query");
        die();
    }

    $IssuesData = array();
    $hasIssues = false;
    // Get all issues user is assigned
    $query = "SELECT i.*, s.name AS status, s.badge, p.id AS projectID FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id INNER JOIN issuefollow AS iff ON i.id=iff.idIssue WHERE iff.idUser=$UserData[id] LIMIT 5";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasIssues = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($IssuesData, $row);
            }
        } else {
            $hasIssues = false;
        }
        $result->close();
    } else {
        printf("Error in select user query");
        die();
    }
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
                    <!-- 5 Projects -->
                    <div>
                        <span style="font-size:2rem; font-weight: 500;">Projects</span>
                        <a href="/projectmanager/dashboard/projects" class="btn btn-primary float-right" style="margin-top:8px; color:white;">All Project</a>
                    </div>
                    <hr>
                    <div class="card-deck">
                        <?php
                            if($hasProjects){
                                foreach($ProjectData as $Project){
                                    if ($Project["idCreator"] == $UserData["id"]){
                                        $code = $Project["code"];
                                    }
                                    $dateTimeStamp = strtotime($Project["creationDate"]);
                                    $Project["creationDate"] = date('d-m-Y', $dateTimeStamp);
                                    echo "
                                        <div class='card text-white bg-primary mb-3' style='max-width: 18rem;'>
                                            <div class='card-header'>
                                                <a href='/projectmanager/project/?id=$Project[id]' style='color:white'>
                                                    $Project[name]
                                                </a>
                                                ";
                                    if ($Project["Role"] < 3){
                                        echo "
                                                <a href='/projectmanager/project/edit?id=$Project[id]' class='btn btn-light float-right'>
                                                    <i class='fas fa-cog'></i>
                                                </a>
                                        ";
                                    }
                                    echo "
                                            </div>
                                            <div class='card-body'>
                                                <h5 class='card-title'>
                                                    $Project[des]
                                                </h5>
                                                <p class='card-text'>
                                                    Status: $Project[Sname] <br>
                                                    Creation Date: $Project[creationDate] <br>";
                                    if (isset($code)){
                                        echo "Invite code: $code";
                                        unset($code);
                                    }
                                    echo "        
                                                </p>
                                            </div>
                                            <div class='card-footer'>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-tasks'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-bug'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-calendar'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-comments'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-flag'></i>
                                                </a>
                                            </div>
                                        </div>
                                    ";
                                }
                            } else {
                                echo "<div class='col-md-12'><h4>No projects found, what about creating or joining a new one?</h4></div>";
                            }  
                        ?>
                    </div>

                    <!-- 5 Tasks -->
                    <div>
                        <span style="font-size:2rem; font-weight: 500;">Tasks</span>
                        <a href="/projectmanager/dashboard/tasks" class="btn btn-success float-right" style="margin-top:8px; color:white;">All Tasks</a>
                    </div>
                    <hr>
                    <div class="task-DIV">
                        <div style="word-break: break-word;">
                            <?php
                            if(isset($TasksData) && $hasTasks){
                                foreach($TasksData as $task){
                                    echo "
                                        <div class='col-md-12'>
                                        <span class='task-DIV-list'>
                                            <a href='/projectmanager/project/tasks/task?id=$task[projectID]&task=$task[id]'>
                                                $task[name]
                                            </a>
                                            <span class='badge badge-$task[badge]'>$task[status]</span>
                                        </span>
                                        <p style='font-size:1.1rem'>
                                            $task[Des]
                                        </p>
                                        </div>
                                    ";
                                }
                            } elseif (!$hasTasks) {
                                echo "<p class='task-DIV-list col-12' style='margin-top: 5px'> No tasks assigned, go to projects and follow some tasks! </p>";
                            }
                            ?>
                        </div>
                    </div>
                    <!-- End Tasks -->

                    <!-- 5 Issues -->
                    <div>
                        <span style="font-size:2rem; font-weight: 500;">Issues</span>
                        <a href="/projectmanager/dashboard/issues" class="btn btn-danger float-right" style="margin-top:8px; color:white;">All Issues</a>
                    </div>
                    <hr>
                    <div class="issue-DIV">
                        <div style="word-break: break-word;">
                            <?php
                            if(isset($IssuesData) && $hasIssues){
                                foreach($IssuesData as $issue){
                                    echo "
                                        <div class='col-md-12'>
                                        <span class='issue-DIV-list'>
                                            <a href='/projectmanager/project/issues/issue?id=$issue[projectID]&issue=$issue[id]'>
                                                $issue[name]
                                            </a>
                                            <span class='badge badge-$issue[badge]'>$issue[status]</span>
                                        </span>
                                        <p style='font-size:1.1rem'>
                                            $issue[Des]
                                        </p>
                                        </div>
                                    ";
                                }
                            } elseif (!$hasIssues) {
                                echo "<p class='issue-DIV-list col-12' style='margin-top: 5px'> No issues assigned, go to projects and follow some issues! </p>";
                            }
                            ?>
                        </div>
                    </div>
                    <!-- End Issues -->




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