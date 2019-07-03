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
    if (!$UserRole = checkUserInProject($conn, $projectID, $UserData["id"])){
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
            if(!isset($taskData)){
                $createTask = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    if(isset($taskData)){
        $currentTaskName = $taskData["name"];
        $currentTaskDes = $taskData["Des"];
        $currentTaskStatus = $taskData["idStatus"];
    }

    $AllTasksStatus = getTasksStatus($conn);
?>

<html lang="en">
    <head>
    <title><?php echo "$projectData[name] - $taskData[name]"; ?></title>
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
                <div class="row d-flex justify-content-center">
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
                        if(isset($taskData) && !isset($createTask)){
                            echo "
                            <div class='col-sm-12 col-md-10 col-lg-10 col-xl-6 task-DIV'>
                                <div class='btn-toolbar row' style='margin-top:15px'>
                                    <div class='col-lg-12' style='margin-top:5px;'>
                                        <span class='Only-task-DIV-title task-DIV-text'>
                                            Task name: $taskData[name]";
                                            if ($UserRole < 3){
                                                echo "
                                                    <a href='#editTaskModal' data-toggle='modal' class='edit-pen'>
                                                        <i class='fas fa-pen'></i>
                                                    </a>
                                                ";
                                            }
                            echo "
                                        </span>
                                    </div>
                                </div>
                                <hr class='hr-task'>
                                <div class='Only-task-DIV-Des' style='word-break: break-word; margin-bottom: 15px'>
                                    <p><b>Creation date:</b> <span class='badge badge-dark'>$taskData[creationDate]</span> by <b> $taskData[idCreator] </b> </p>
                                    <p><b>Last time updated:</b> <span class='badge badge-dark'>$taskData[lastupdatedDate]</span> by <b> $taskData[idUpdateUser] </b> </p>
                                    <p><b>Task Status: </b><span class='badge badge-$taskData[badge]'>$taskData[status]</span> </p>
                                    <p><b>Task description:</b><br>
                                    $taskData[Des]</p>
                                    
                                </div>
                            </div>
                            ";
                        } elseif (isset($createTask) && $createTask) {
                            echo "<p class='task-DIV-list'> No tasks yet, create them! </p>";
                        }
                    ?>
                    <!-- END Task -->
                        
                    </div>
                </div>

                <!-- Edit task modal -->
                <div class="modal fade" id="editTaskModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Head -->
                            <div class="modal-header">
                                <span class="modal-title"> Edit: <?php echo $taskData["name"]?> </span>
                                <button type="button" class="close" data-dismiss="modal" aria-label=""><span>×</span></button>
                            </div>        
                            <!-- Body -->
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <span class="modal-subtitle">Task name:</span>
                                    <input type='text' class='form-control edit-DIV-Input' name='taskName' value='<?php echo "$currentTaskName"; ?>' autocomplete='off'/>

                                    <span class="modal-subtitle">Description:</span>
                                    <textarea class='form-control edit-DIV-Input' rows='3' name='taskDes' autocomplete='off'><?php echo "$currentTaskDes"; ?></textarea>

                                    <span class="modal-subtitle">Status:</span>

                                    <div class="form-group">
                                        <select class="form-control edit-DIV-Input" name="taskStatus">
                                            <?php
                                                foreach($AllTasksStatus as $status){
                                                    if ($status["id"] != $currentTaskStatus){
                                                        echo "<option value='$status[id]'>$status[name]</option>";
                                                    } else {
                                                        echo "<option value='$status[id]' selected>$status[name]</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Don't change values, if you didn't report it.</div>
                                    </div>
                                    <input type="submit" class="btn btn-success font-weight-bold" name="editTaskBTN" value="Edit task">
                                </form>                
                            </div>
                                    
                        </div>
                    </div>
                </div> 
                <!-- END task modal -->

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