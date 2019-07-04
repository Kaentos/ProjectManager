<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php";
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
    } else {
        $currentTaskName = "";
        $currentTaskDes = "";
        $currentTaskStatus = "";
    }

    // Edit task btn
    if (isset($_POST["editTaskBTN"])){
        if ( isset($_POST["taskName"]) && strlen($_POST["taskName"]) <= 60 && !empty($_POST["taskName"])) {
            if (isset($_POST["taskDes"]) && strlen($_POST["taskDes"]) <= 150 && !empty($_POST["taskDes"])) {
                if (isset($_POST["taskStatus"]) && is_numeric($_POST["taskStatus"]) && checkTaskStatusID($conn, $_POST["taskStatus"])) {
                    $Data = [
                        "name" => $_POST["taskName"],
                        "des" => $_POST["taskDes"],
                        "status" => $_POST["taskStatus"]
                    ];
                    $currentTaskName = $Data["name"];
                    $currentTaskDes = $Data["des"];
                    $currentTaskStatus = $Data["status"];
                    editTask($conn, $Data, $taskData, $UserData);
                } else {
                    $info = "Can\'t validate status value! If you didn\'t change value report with error MTS!";
                    showAlert($info);
                }
            } else {
                $info = "Task description must have 1 to 150 characters.";
                showAlert($info);
            }
        } else {
            $info = "Task name must have 1 to 60 characters.";
            showAlert($info);
        }
    }

    $commentERR = false;
    // New comment btn
    if (isset($_POST["newCommentBTN"])){
        if (isset($_POST["comment"]) && !empty($_POST["comment"])){
            if (strlen($_POST["comment"]) <= 150){
                $comment = $_POST["comment"];
                $commentERR = false;
            } else {
                $commentERR = true;
            }
        } else {
            $commentERR = true;
        }
        if (isset($comment)){
            addTaskNewComment($conn, $taskID, $comment, $UserData["id"]);
        }
    }

    $AllTaskComments = getTaskComments($conn, $taskID);

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
        <div class="page-wrapper chiller-theme">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-12" style="padding-left: 0px">
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
                    </div>
                    <hr class="w-100">
                    
                    
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

                <hr class="w-50" style="margin-top:0px; margin-bottom:0px">

                <!-- Comments -->
                <div class="row d-flex justify-content-center" style="margin-top: 0px">
                    <div class="col-12 d-flex justify-content-center" style="padding-left:0px">
                        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-6 Only-task-DIV-title" id="Comments" style="padding-left:5px">
                            Comments
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-10 col-lg-10 col-xl-6" style="background-color:green;" id="#Dis">
                        <form class="col-12" method="POST" action="" style="margin-top: 20px">
                            <div class="row">
                            <div class="col-lg-4 col-xl-4">
                                    <img class="img-thumbnail" style="height: 25%; width: 100%;" src="/projectmanager/img/UIMG/9.png" alt="User picture">
                                </div>
                                <div class="col-lg-8 col-xl-8">
                                    <div>
                                        <textarea class='form-control edit-DIV-Input' placeholder="Write your comment here!" rows='5' name='comment' autocomplete='off'></textarea>
                                    </div>
                                </div>
                                <div class="col-12" style="word-break: break-word; font-size: 1.2rem; margin-top: 5px">
                                    <input type="submit" class="btn btn-dark float-right" name="newCommentBTN" value="Reply">
                                </div>

                            </div>
                        </form>

                        <hr class="w-100" style="border-color:black">

                        <!-- Comment -->
                        <?php
                            if(isset($AllTaskComments) && $AllTaskComments){
                                foreach($AllTaskComments as $comment){
                                    echo "
                                    <div class='col-12' style='margin-bottom: 15px'>
                                        <div class='row'>
                                            <div class='col-lg-4 col-xl-4'>
                                                <img class='img-thumbnail' style='height: 23%; width: 100%;' src='/projectmanager/img/UIMG/9.png'>
                                            </div>
                                            <div class='col-lg-8 col-xl-8'>
                                                <div class='alert alert-light task-comment-text'>
                                                    $comment[comment]
                                                </div>
                                            </div>
                                            <div class='col-12' style='word-break: break-word; margin-top: 5px'>
                                                <div class='alert alert-light'>
                                                    <b>Comment made at $comment[creationDate] by $comment[username]";

                                    if($comment["lastUpdateDate"] != null){
                                        echo ", last update at $comment[lastUpdateDate]";
                                    }
                                    
                                echo "
                                                </b></div>
                                            </div>
                                        </div>
                                    </div>
                                    ";
                                }
                            } else {
                                echo "
                                    <div class='col-12 Only-task-DIV-title' style='margin-bottom: 15px'>
                                        No comments found, be the first one!
                                    </div>
                                ";
                            }
                            
                        ?>

                        <hr class="w-100" style="border-color:black">

                        <!-- Go back to the top -->
                        <div class="col-12" style="margin-bottom: 20px; text-align:right">
                            <a href="#Comments" class="btn btn-dark"> Back to the top </a>
                        </div>
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