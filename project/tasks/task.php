<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php";
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    // Get project ID from URL GET
    if (isset($_GET["id"]) && is_numeric($_GET["id"])){
        if (checkProjectID($conn, $_GET["id"])){
            $projectID = $_GET["id"];
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
        
    } else {
        header("location: /projectmanager/dashboard/projects");
    }

    // Checks if user has permission to access project content
    if (!checkUserInProject($conn, $projectID, $UserData["id"])){
        header("location: /projectmanager/dashboard/projects");
    }

    // Get task ID from URL GET
    if (isset($_GET["task"]) && is_numeric($_GET["task"])){
        if (checkTaskID($conn, $_GET["task"])){
            $taskID = $_GET["task"];
        } else {
            header("location: /projectmanager/project?id=$projectID");
        }
    } else {
        header("location: /projectmanager/project?id=$projectID");
    }

    // Get task data
    if (isset($projectID)){
        $projectData = getSingleProjectData($conn, $projectID, $UserData["id"]);
        if (isset($projectData)){
            $taskData = getSingleTask($conn, $projectID, $taskID);
            print_r($taskData);
            if(!isset($tasksData)){
                $createTask = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);

    

    $AllTasksStatus = getTasksStatus($conn);
?>

<html lang="en">
    <head>
    <title><?php echo "$projectData[name] - Tasks"; ?></title>
        <?php
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html";
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html";
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <span style="font-size:2rem; font-weight: 500;">
                            <?php
                                echo "
                                    <a href='/projectmanager/project/?id=$projectData[id]' style='text-decoration:none;'>
                                        <span style='color: black;'>$projectData[name]</span>
                                        <span class='badge badge-$projectData[badge]'>$projectData[Sname]</span>
                                    </a>
                                ";
                                if ($UserRole < 3){
                                    echo "
                                        <a href='/projectmanager/project/edit?id=$projectData[id]' class='edit-pen'>
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
                        <hr>
                    </div>
                    
                    <!-- Task -->
                    <?php
                        if(isset($tasksData) && !$NoTasks){
                            foreach($tasksData as $task){
                                echo "
                                <div class='col-lg-12 col-xl-6 task-DIV'>
                                    <div class='btn-toolbar row' style='margin-top:15px'>
                                        <div class='col-lg-12' style='margin-top:5px;'>
                                            <span class='task-DIV-title2 task-DIV-text'>
                                                <a href='/projectmanager/project/task?id=$projectData[id]&task=$task[id]'>
                                                    $task[name]
                                                </a>
                                                <span class='badge badge-$task[badge]'>$task[status]</span>
                                                <span class='badge badge-dark'>$task[lastupdatedDate]</span>";
                                                if ($UserRole < 3){
                                                    echo "
                                                        <a href='#' class='edit-pen'>
                                                            <i class='fas fa-pen'></i>
                                                        </a>
                                                    ";
                                                }
                                echo "
                                                <a href='#' class='btn bg-dark text-white float-right'>
                                                    <i class='fas fa-comments'></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                    <hr class='hr-task'>
                                    <div class='task-DIV-Des' style='word-break: break-word; margin-bottom: 15px'>
                                        $task[Des]
                                    </div>
                                </div>
                                ";
                            }
                        } elseif (isset($createTask) && $createTask) {
                            echo "<p class='task-DIV-list'> No tasks yet, create them! </p>";
                        } elseif ($NoTasks){
                            echo "<p class='task-DIV-list'> No tasks found! </p>";
                        }
                    ?>
                    <!-- END Task -->
                        
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