<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        error_reporting(E_ERROR | E_PARSE);
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-OF-MPT"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-GF-MPT"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-AF-MPT"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-CF-MPT"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            die(header("Location: /projectmanager/errors/?id=CI-SC-MPT"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            die(header("Location: /projectmanager/errors/?id=CI-DF-MPT"));
        }

        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    $TasksData = array();
    $hasTasks = false;
    $query = "SELECT t.*, s.name AS status, s.badge, p.id AS projectID, p.name AS projectName FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id INNER JOIN taskfollow AS tf ON t.id=tf.idTask WHERE tf.idUser=$UserData[id] LIMIT 25";
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
        sendError("GTD-MPT");
    }

    $orderDic = [
        "name" => "ORDER BY t.name",
        "cd" => "ORDER BY t.creationDate ASC",
        "lud" => "ORDER BY t.lastupdatedDate DESC"
    ];
    $filer1Selected = $filer2Selected = false;

    if(isset($_POST["searchBTN"]) && $hasTasks){
        if(isset($_POST["searchTask"])){
            $nameFilter = $_POST["searchTask"];
            $vname = $nameFilter;
            $nameFilter = "%".$nameFilter."%";
        } else {
            $nameFilter = "%%";
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

        if ($temp = searchTask($conn, $nameFilter, $filterORDER, $filterStatusID, $UserData)){
            $TasksData = $temp;
        } else {
            $filterHasTasks = true;
        }
    }

    function searchTask($conn, $nameFilter, $ORDER, $GROUP, $UserData){
        if(!$stmt = $conn->prepare("SELECT t.*, s.name AS status, s.badge, p.id AS projectID, p.name AS projectName FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id INNER JOIN taskfollow AS tf ON t.id=tf.idTask WHERE tf.idUser=$UserData[id] AND t.name LIKE ? $GROUP $ORDER LIMIT 25")) {
            sendError("MPT-PT-P");
        }
        if(!$stmt->bind_param("s", $nameFilter)) {
            sendError("MPT-PT-B");
        }
        if(!$stmt->execute()) {
            sendError("MPT-PT-E");
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows > 0){
                $Data = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($Data, $row); 
                }
                $stmt->close();
            } else {
                return false;
            }
        } else {
            sendError("MPT-PT-GR");
        }
        if (isset($Data)){
            return $Data;
        } else {
            return false;
        }
    }

    $AllTasksStatus = getTasksStatus($conn);
?>

<html lang="en">
    <head>
        <title>Followed tasks</title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-MPT"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-MPT"));
            }
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    die(header("Location: /projectmanager/errors/?id=CI-BAR-MPT"));
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                            <div class="col-12 col-lg-4 col-xl-6 page-title">
                                Followed tasks
                            </div>
                            <form method="POST" action="" class="col-12 col-lg-8 col-xl-6" style="text-align:right">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by task name" name="searchTask">
                                    <div class="input-group-append">
                                        <button type="submit" name="searchBTN" class="btn btn-dark">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <select name="filter" class="form-control bg-dark text-white">
                                            <option <?php if(!$filer1Selected){ echo "selected"; } ?> disabled> Order by... </option>
                                            <option <?php if($filer1Selected == "name"){ echo "selected"; } ?> value="name"> Name </option>
                                            <option <?php if($filer1Selected == "cd"){ echo "selected"; } ?> value="cd"> Creation date </option>
                                            <option <?php if($filer1Selected == "lud"){ echo "selected"; } ?> value="lud"> Last update date </option>
                                        </select>
                                        &nbsp
                                        <select name="filterStatus" class="form-control bg-dark text-white">
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
                                </div>
                            </form>    
                        </div>
                        <hr class='w-100'>
                        <?php
                            if($hasTasks && (!isset($filterHasTasks) || !$filterHasTasks)){
                                foreach($TasksData as $task){
                                    echo "
                                        <div class='col-md-12 col-xl-4' style='margin-bottom: 10px'>
                                            <div class='col-12 text-white bg-success' style='border-radius:5px;'>
                                                <div class='row task-border-bottom'>
                                                    <div class='col-12 task-margin-tb-10'>
                                                        <a href='/projectmanager/project/tasks/task?id=$task[projectID]&task=$task[id]' class='task-title'>
                                                            $task[name]
                                                        </a>";
                                    if($task["badge"] == "success"){
                                        echo "<span class='badge badge-$task[badge] custom-badge-border task-badge-text'>$task[status]</span>";
                                    } else {
                                        echo "<span class='badge badge-$task[badge] task-badge-text'>$task[status]</span>";
                                    }
                                    echo "         
                                                    </div>
                                                </div>

                                                <div class='row task-margin-tb-10'>
                                                    <div class='col-12 project-text'>
                                                        $task[Des]
                                                    </div>
                                                </div>

                                                <div class='row task-border-top'>
                                                    <div class='col-12 project-text task-margin-tb-10'>
                                                        From:
                                                        <a href='/projectmanager/project/?id=$task[projectID]' style='color:white; text-decoration:none'>
                                                            <b>$task[projectName]</b>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ";
                                }
                            } elseif (isset($filterHasTasks) && $filterHasTasks) {
                                echo "
                                    <div class='col-12'>
                                        <div class='text-center'>
                                            <h4>No tasks found for your search $vname</h4>
                                        </div>
                                    </div>";
                            } else {
                                echo "<div class='col-12'><h4>You aren't following any tasks, how about following some?</h4></div>";
                            }
                        ?>
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