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
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            sendError("FIM-SCF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            sendError("FIM-DBF");
        }
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    $ProjectData = array();
    $hasProjects = false;
    $query = "SELECT p.*, s.name as Sname, s.badge AS Sbadge, u.username, pm.idRole AS Role FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE pm.idUser =$UserData[id] ORDER BY p.creationDate DESC LIMIT 5";
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
        sendError("GPD-MPI");
    }

    $TasksData = array();
    $hasTasks = false;
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
        sendError("GTD-MPI");
    }

    $IssuesData = array();
    $hasIssues = false;
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
        sendError("GID-MPI");
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
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    sendError("MPB-MP");
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <!-- 5 Projects -->
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-12 col-xl-4">
                            <span style="font-size:2rem; font-weight: 500;">All projects</span>
                        </div>
                        <div class="col-12 col-xl-8">
                            <a href="/projectmanager/dashboard/projects" class="btn btn-primary float-right" style="margin-top:8px; color:white;">All Project</a>
                        </div>    
                    </div>
                    <hr class='w-100'>
                    <?php
                        if($hasProjects){
                            foreach($ProjectData as $Project){
                                if ($Project["Role"] < 3){
                                $code = $Project["code"];
                                }
                                $dateTimeStamp = strtotime($Project["creationDate"]);
                                $Project["creationDate"] = date('d-m-Y', $dateTimeStamp);
                                $dateTimeStamp = strtotime($Project["lastupdatedDate"]);
                                $Project["lastupdatedDate"] = date('d-m-Y', $dateTimeStamp);

                                echo "
                                <div class='col-12 col-xs-12 col-sm-12 col-md-* col-lg-4 col-xl-4 bg-dark text-light m-1' style='border-radius: 5px;'>
                                    <div class='row project-border-bottom'>
                                        <div class='col-12' style='padding: 20px'>
                                            <a href='/projectmanager/project/?id=$Project[id]' class='project-title'>
                                                $Project[name]
                                            </a>
                                        </div>
                                    </div>

                                    <div class='row project-text' style='margin-top: 10px;'>
                                        <div class='col-12' style='margin-bottom: 10px'>
                                            $Project[des]
                                        </div>
                                        <div class='col-md-12 col-xl-6' style='margin-top: 10px'>
                                            Status: <span class='badge badge-$Project[Sbadge]'>$Project[Sname]</span>
                                            <br>
                                            Updated: <span class='badge badge-light'>$Project[lastupdatedDate]</span>
                                        </div>
                                        <div class='col-md-12 col-xl-6' style='margin-top: 5px'>
                                            Created: <span class='badge badge-light'>$Project[creationDate]</span>
                                            <br>";
                                if (isset($code)){
                                    echo "Code: <span class='badge badge-light'>$code</span>";
                                }
                                echo "    
                                        </div>
                                    </div>
                                    <div class='row project-border-top' style='padding: 10px; margin-top: 10px'>
                                        <div class='col-12 text-center'>
                                            <a href='/projectmanager/project/tasks/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                <i class='fas fa-tasks'></i>
                                            </a>
                                            <a href='/projectmanager/project/issues/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                <i class='fas fa-bug'></i>
                                            </a>
                                            <a href='/projectmanager/project/members/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                <i class='fas fa-users'></i>
                                            </a>
                                            <a href='#' class='btn btn-light' style='margin: 5px'>
                                                <i class='fas fa-flag'></i>
                                            </a>
                                            <a href='#' class='btn btn-light' style='margin: 5px'>
                                                <i class='fas fa-comments'></i>
                                            </a>";
                                if ($Project["Role"] < 3){
                                    echo "
                                        <a href='/projectmanager/project/edit?id=$Project[id]' class='btn btn-primary' style='margin: 5px'>
                                            <i class='fas fa-cog'></i>
                                        </a>";
                                }            
                                echo "
                                        </div>
                                    </div>
                                </div>
                                ";
                                if (isset($code)){
                                    unset($code);
                                }
                            }
                        } else {
                            echo "<div class='col-12'><h4>No projects found, what about creating or joining a new one?</h4></div>";
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