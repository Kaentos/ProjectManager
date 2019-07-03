<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php";
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
            $tasksData = getTasks($conn, $projectID);
            if(!isset($tasksData)){
                $createTask = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    // Tasks Data
    function getTasks($conn, $projectID){
        $tasksData = array();
        $query = "SELECT t.*, s.name AS status, s.badge FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id WHERE p.id=$projectID ORDER BY t.lastupdatedDate DESC LIMIT 25";
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

    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);
    $orderDic = [
        "name" => "ORDER BY t.name",
        "cd" => "ORDER BY t.creationDate DESC",
        "lud" => "ORDER BY t.lastupdatedDate DESC"
    ];
    $filer1Selected = $filer2Selected = false;
    $NoTasks = false;

    // Apply filters
    if (isset($_POST["searchBTN"])){

        // Filter name
        if(isset($_POST["searchTask"])){
            $StaskName = $_POST["searchTask"];
            $StaskName = "%".$StaskName."%";
            $filterName = "AND t.name LIKE ?";
        }

        // Filter order
        if(isset($_POST["filter"])){
            if(array_key_exists($_POST["filter"], $orderDic)){
                $filterORDER = $orderDic["$_POST[filter]"];
                $filer1Selected = $_POST["filter"];
            } else {
                $info =  "Invalid order filter value! If you didn\'t change anything report with TFV!";
                showAlert($info);
                $filer1Selected = "lud";
                $filterORDER = "ORDER BY t.lastupdatedDate";
            }
        } else {
            $filer1Selected = "lud";
            $filterORDER = "ORDER BY t.lastupdatedDate";
        }

        // Filter group / status
        if(isset($_POST["filterStatus"])){
            if(is_numeric($_POST["filterStatus"]) && checkTaskStatusID($conn, $_POST["filterStatus"])){
                $filterStatusID = "AND t.idStatus=". $_POST["filterStatus"];
                $filer2Selected = $_POST["filterStatus"];
            } elseif ($_POST["filterStatus"] == -1) {
                $filterStatusID = "";
                $filer2Selected = false;
            } else {
                $info = "Invalid status filter value! If you didn\'t change anything report with TFS!";
                showAlert($info);
                $filer2Selected = false;
                $filterStatusID = "";
            }
        } else {
            $filer2Selected = false;
            $filterStatusID = "";
        }

        if ($filterOK = TaskFilter($conn, $projectID, $filterName, $filterORDER, $filterStatusID, $StaskName)){
            $tasksData = $filterOK;
        }
    }

    function TaskFilter($conn, $projectID, $NAME, $ORDER, $GROUP, $StaskName){
        if(!($stmt = $conn->prepare("SELECT t.*, s.name AS status, s.badge FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id WHERE p.id=$projectID $NAME $GROUP $ORDER LIMIT 25"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        if(!$stmt->bind_param("s", $StaskName)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows > 0){
                $tasksData = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($tasksData, $row); 
                }
                $stmt->close();
            } else {
                $GLOBALS["NoTasks"] = true;
            }
        } else {
            printf("Error in select user query");
            return false;
        }
        if (isset($tasksData)){
            return $tasksData;
        } else {
            return false;
        }
        
    }

    // New task btn
    if (isset($_POST["newTaskBTN"])){
        if ( isset($_POST["taskName"]) && strlen($_POST["taskName"]) <= 60 && !empty($_POST["taskName"])) {
            if (isset($_POST["taskDes"]) && strlen($_POST["taskDes"]) <= 150 && !empty($_POST["taskDes"])) {
                if (isset($_POST["taskStatus"]) && is_numeric($_POST["taskStatus"]) && checkTaskStatusID($conn, $_POST["taskStatus"])) {
                    $Data = [
                        "name" => $_POST["taskName"],
                        "des" => $_POST["taskDes"],
                        "status" => $_POST["taskStatus"]
                    ];
                    addNewTask($conn, $projectID, $UserData["id"], $Data);
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
        <div class="page-wrapper chiller-theme">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
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
                    
                    <div class="col-lg-12 filter-DIV">
                        <div class="row" style='margin-top:15px;'>
                            <!-- Name filter -->
                            <div class="col-md-12 col-lg-4 filter-DIV-text">
                                <form method="POST" action="">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search by task name" name="searchTask">
                                        <div class="input-group-append">
                                            <button type="submit" name="searchBTN" class="btn  btn-dark">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                            </div>
                            <!-- Selects filter -->
                            <div class="col-12 col-sm-12 col-md-9 col-lg-6">
                                    <div class="input-group">
                                        <select name="filter" class="form-control" style="background: #3a3f48; color:white; border: none">
                                            <option <?php if(!$filer1Selected){ echo "selected"; } ?> disabled> Order by... </option>
                                            <option <?php if($filer1Selected == "name"){ echo "selected"; } ?> value="name"> Name </option>
                                            <option <?php if($filer1Selected == "cd"){ echo "selected"; } ?> value="cd"> Creation date </option>
                                            <option <?php if($filer1Selected == "lud"){ echo "selected"; } ?> value="lud"> Last update date </option>
                                        </select>
                                        &nbsp
                                        <select name="filterStatus" class="form-control" style="background: #3a3f48; color:white; border: none">
                                            <option value="-1" <?php if(!$filer2Selected){ echo "selected"; } ?>> All status </option>
                                            <?php
                                                foreach($AllTasksStatus as $task){
                                                    if ($filer2Selected == $task["id"]) {
                                                        echo "<option value='$task[id]' selected>$task[name]</option>";
                                                    } else {
                                                        echo "<option value='$task[id]'>$task[name]</option>";
                                                    }
                                                    
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                </form>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-2">
                                <?php
                                    if ($UserRole < 4){
                                        echo "
                                            <a class='btn btn-dark float-right' data-toggle='modal' href='#newTaskModal' style='margin-bottom: 15px'>
                                                New Task
                                            </a>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
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
                                                <a href='/projectmanager/project/tasks/task?id=$projectData[id]&task=$task[id]'>
                                                    $task[name]
                                                </a>
                                                <span class='badge badge-$task[badge]'>$task[status]</span>
                                                <span class='badge badge-dark'>$task[lastupdatedDate]</span>
                                                <a href='/projectmanager/project/tasks/task?id=$projectData[id]&task=$task[id]#Comments' class='btn bg-dark text-white float-right'>
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
                                                foreach($AllTasksStatus as $status){
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