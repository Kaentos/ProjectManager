<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php";
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    if (isset($_GET["id"]) && is_numeric($_GET["id"])){
        $projectID = $_GET["id"];
    } else {
        header("location: /projectmanager/dashboard/projects");
    }

    if (isset($projectID)){
        $projectData = getSingleProjectData($conn, $projectID, $UserData["id"]);
        if (isset($projectData)){
            $tasksData = get5Tasks($conn, $projectID);
            if(!isset($tasksData)){
                $createTask = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    if (isset($_POST["newTaskBTN"])){
        if ( isset($_POST["taskName"]) && strlen($_POST["taskName"]) <= 60 && !empty($_POST["taskName"])) {
            if (isset($_POST["taskDes"]) && strlen($_POST["taskDes"]) <= 150 && !empty($_POST["taskDes"])) {
                if (isset($_POST["taskStatus"]) && is_numeric($_POST["taskStatus"]) && checkTaskStatusID($conn, $_POST["taskStatus"])) {
                    $Data = [
                        "name" => $_POST["taskName"],
                        "des" => $_POST["taskDes"],
                        "status" => $_POST["taskStatus"]
                    ];
                } else {
                    echo "Wrong task status";
                }
            } else {
                echo "Wrong task des";
            }
        } else {
            echo "Wrong task name";
        }

        // addNewTask($conn, $projectID, $UserData["id"]);
    }

    function addNewTask($conn, $projectID, $userID){
        $currentDate = getCurrentDate();
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

    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);
    $AllProjectStatus = getTasksStatus($conn);
?>

<html lang="en">
    <head>
    <title><?php echo $projectData["name"] ?></title>
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
                    

                    
                        <!-- Tasks -->
                        <div class="col-lg-12 col-xl-5 task-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12 col-xl-6" style="margin-top:5px;">
                                    <span class="task-DIV-title">Last updated tasks</span>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <?php 
                                        if(isset($tasksData)) {
                                            echo "
                                            <div class='btn-group mr-2 DIV-btn-float' style='margin-top:5px'>
                                                <a href='/projectmanager/project/tasks/?id=$projectData[id]' class='btn btn-success task-DIV-btn'>All tasks</a>
                                            </div>
                                            ";
                                        }
                                        if ($UserRole < 4){
                                            echo "
                                                
                                                <div class='btn-group mr-2 DIV-btn-float' style='margin-top:5px'>
                                                    <a class='btn btn-success task-DIV-btn' data-toggle='modal' href='#newTaskModal'>
                                                        New Task
                                                    </a>
                                                </div>
                                            ";
                                        }
                                        
                                    ?>
                                    
                                </div>
                            </div>
                            <hr class="hr-task">
                            <div style="word-break: break-word;">
                                <?php
                                if(isset($tasksData)){
                                    foreach($tasksData as $task){
                                        echo "
                                        <span class='task-DIV-list'>
                                            <a href='/projectmanager/project/tasks/task?id=$projectData[id]&task=$task[id]'>
                                                $task[name]
                                            </a>
                                            <span class='badge badge-$task[badge]'>$task[status]</span>";
                                            if ($UserRole < 3){
                                                echo "
                                                    <a href='/projectmanager/project/tasks/edit?id=$projectData[id]&task=$task[id]' class='edit-pen'>
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
                                } elseif (isset($createTask) && $createTask) {
                                    echo "<p class='task-DIV-list'> No tasks yet, create them! </p>";
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
                                } elseif (isset($createTask) && $createTask) {
                                    echo "<p class='task-DIV-list'> No issues yet, create them if you need! </p>";
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

                <!-- New task modal -->
                <div class="modal fade" id="newTaskModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Head -->
                            <div class="modal-header">
                                <span class="modal-title"> Create new task </span>
                                <button type="button" class="close" data-dismiss="modal" aria-label=""><span>×</span></button>
                            </div>        
                            <!-- Body -->
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <span class="modal-subtitle">Task name:</span>
                                    <input type='text' class='form-control edit-DIV-Input' name='taskName' autocomplete='off'/>

                                    <span class="modal-subtitle">Description:</span>
                                    <textarea class='form-control edit-DIV-Input' rows='3' name='taskDes' autocomplete='off'></textarea>

                                    <span class="modal-subtitle">Status:</span>

                                    <div class="form-group">
                                        <select class="form-control edit-DIV-Input" name="taskStatus">
                                            <?php
                                                foreach($AllProjectStatus as $status){
                                                    if ($status["id"] != $projectData["idStatus"]){
                                                        echo "<option value='$status[id]'>$status[name]</option>";
                                                    } else {
                                                        echo "<option value='$status[id]' selected>$status[name]</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Don't change values, if you didn't report it.</div>
                                    </div>
                                    <input type="submit" class="btn btn-success font-weight-bold" name="newTaskBTN" value="Create task">
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