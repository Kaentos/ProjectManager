<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/removeFunctions.php";
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
            $tasksData = get5Tasks($conn, $projectID);
            $issuesData = get5Issues($conn, $projectID);
            if(!isset($tasksData)){
                $createTask = true;
            }
            if(!isset($issuesData)){
                $createIssue = true;
            }
            $membersData = get5Members($conn, $projectID);
            if(!isset($membersData)){
                die();
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }
    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);

    // New task btn
    if (isset($_POST["newTaskBTN"]) && $UserRole < 4){
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

    // New issue btn
    if (isset($_POST["newIssueBTN"]) && $UserRole < 4){
        if ( isset($_POST["issueName"]) && strlen($_POST["issueName"]) <= 60 && !empty($_POST["issueName"])) {
            if (isset($_POST["issueDes"]) && strlen($_POST["issueDes"]) <= 150 && !empty($_POST["issueDes"])) {
                if (isset($_POST["issueStatus"]) && is_numeric($_POST["issueStatus"]) && checkIssueStatusID($conn, $_POST["issueStatus"])) {
                    $Data = [
                        "name" => $_POST["issueName"],
                        "des" => $_POST["issueDes"],
                        "status" => $_POST["issueStatus"]
                    ];
                    addNewIssue($conn, $projectID, $UserData["id"], $Data);
                } else {
                    $info = "Can\'t validate status value! If you didn\'t change value report with error ITS!";
                    showAlert($info);
                }
            } else {
                $info = "Issue description must have 1 to 150 characters.";
                showAlert($info);
            }
        } else {
            $info = "Issue name must have 1 to 60 characters.";
            showAlert($info);
        }
    }

    if(isset($_POST["QuitProjectBTN"]) && $UserRole > 1){
        removeUserFromProject($conn, $UserData["id"], $projectID);
    }


    $AllTasksStatus = getTasksStatus($conn);
    $AllIssuesStatus = getIssuesStatus($conn);
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
        <div class="page-wrapper chiller-theme">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    sendError("MPB-PI");
                }
            ?>


            <main class="page-content">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-12" style="padding-left: 0px">
                        <form method="POST" action="">
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
                                    if ($UserRole > 1){
                                        echo "
                                            <button type='submit' class='btn btn-danger float-right' name='QuitProjectBTN'> <i class='fas fa-times'></i> </button>
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
                        </form>
                    </div>
                    <hr class="w-100">
                    
                        <!-- Tasks -->
                        <div class="col-lg-12 col-xl-5 task-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12 col-xl-6" style="margin-top:5px;">
                                    <span class="task-DIV-title">
                                        <a href='/projectmanager/project/tasks/?id=<?php echo $projectData["id"] ?>' style="text-decoration: none; color: black">
                                            Last updated tasks
                                        </a>    
                                    </span>
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
                                            <span class='badge badge-$task[badge]'>$task[status]</span>
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
                                    <span class="issue-DIV-title">
                                        <a href='/projectmanager/project/issues/?id=<?php echo $projectData["id"] ?>' style="text-decoration: none; color: black">
                                            Last updated issues
                                        </a>    
                                    </span>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <?php 
                                        if(isset($issuesData)) {
                                            echo "
                                            <div class='btn-group mr-2 DIV-btn-float' style='margin-top:5px'>
                                                <a href='/projectmanager/project/issues/?id=$projectData[id]' class='btn btn-danger issue-DIV-btn'>All issues</a>
                                            </div>
                                            ";
                                        }
                                        if ($UserRole < 4){
                                            echo "
                                                
                                                <div class='btn-group mr-2 DIV-btn-float' style='margin-top:5px'>
                                                    <a class='btn btn-danger issue-DIV-btn' data-toggle='modal' href='#newIssueModal'>
                                                        New issue
                                                    </a>
                                                </div>
                                            ";
                                        }
                                        
                                    ?>
                                    
                                </div>
                            </div>
                            <hr class="hr-issue">
                            <div style="word-break: break-word;">
                                <?php
                                if(isset($issuesData)){
                                    foreach($issuesData as $issue){
                                        echo "
                                        <span class='issue-DIV-list'>
                                            <a href='/projectmanager/project/issues/issue?id=$projectData[id]&issue=$issue[id]'>
                                                $issue[name]
                                            </a>
                                            <span class='badge badge-$issue[badge]'>$issue[status]</span>
                                        </span>
                                        <p style='font-size:1.1rem'>
                                            $issue[Des]
                                        </p>
                                        ";
                                    }
                                } elseif (isset($createIssue) && $createIssue) {
                                    echo "<p class='issue-DIV-list'> No issues yet, create them! </p>";
                                }
                                ?>
                            </div>
                        </div>
                        <!-- END Issues -->

                        <!-- Milestones -->
                        <div class="col-lg-12 col-xl-5 members-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12 col-xl-6" style="margin-top:5px;">
                                    <span class="members-DIV-title">Milestones - Coming soon!</span>
                                </div>
                            </div>
                        </div>
                        <!-- END milestones -->


                        <!-- col to space out cols -->
                        <div class="col-md-1">
                        </div>


                        <!-- Members -->
                        <div class="col-lg-12 col-xl-5 members-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12 col-xl-6" style="margin-top:5px;">
                                    <span class="members-DIV-title">
                                        <a href='/projectmanager/project/members/?id=<?php echo $projectData["id"] ?>' style="text-decoration: none; color: black">
                                            Members
                                        </a>
                                    </span>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="btn-group mr-2 DIV-btn-float" style="margin-top:5px">
                                        <a href="/projectmanager/project/members?id=<?php echo $projectID ?>" class="btn btn-primary members-DIV-btn">All members</a>
                                    </div>
                                </div>
                            </div>
                            <hr class="hr-members">
                            <div class="row" style="word-break: break-word;">
                                <?php
                                if(isset($membersData)){
                                    foreach($membersData as $member){
                                        echo "
                                        <div class='col-12 col-md-6'>
                                            <span style='font-size:1.3rem; font-weight: bold;'>
                                                <img class='img-thumbnail' style='height: 100px; width: auto;' src='/projectmanager/img/UIMG/9.png'>
                                                $member[username]
                                                <span class='badge badge-$member[badge]'>$member[name]</span>    
                                            </span>
                                        </div>
                                        ";
                                    }
                                }
                                ?>
                            </div>
                            <br>
                        </div>
                        <!-- END Members -->

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

                <!-- New issue modal -->
                <div class="modal fade" id="newIssueModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Head -->
                            <div class="modal-header">
                                <span class="modal-title"> Create new issue </span>
                                <button type="button" class="close" data-dismiss="modal" aria-label=""><span>×</span></button>
                            </div>        
                            <!-- Body -->
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <span class="modal-subtitle">Issue name:</span>
                                    <input type='text' class='form-control edit-DIV-Input' name='issueName' autocomplete='off'/>

                                    <span class="modal-subtitle">Description:</span>
                                    <textarea class='form-control edit-DIV-Input' rows='3' name='issueDes' autocomplete='off'></textarea>

                                    <span class="modal-subtitle">Status:</span>

                                    <div class="form-group">
                                        <select class="form-control edit-DIV-Input" name="issueStatus">
                                            <?php
                                                foreach($AllIssuesStatus as $status){
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
                                    <input type="submit" class="btn btn-danger font-weight-bold" name="newIssueBTN" value="Create issue">
                                </form>                
                            </div>
                                    
                        </div>
                    </div>
                </div> 
                <!-- END issue modal -->
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