<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        error_reporting(E_ERROR | E_PARSE);
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-OF-PM"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-GF-PM"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-EF-PM"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-AF-PM"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-CF-PM"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/removeFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-RF-PM"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            die(header("Location: /projectmanager/errors/?id=CI-SC-PM"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            die(header("Location: /projectmanager/errors/?id=CI-DF-PM"));
        }
        
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    if (isset($_GET["id"]) && is_numeric($_GET["id"])){
        $projectID = $_GET["id"];
    } else {
        header("location: /projectmanager/dashboard/projects");
    }

    if (!$UserRole = checkUserInProject($conn, $projectID, $UserData["id"])){
        header("location: /projectmanager/dashboard/projects");
    }

    if (isset($projectID)){
        $projectData = getSingleProjectData($conn, $projectID, $UserData["id"]);
        if (isset($projectData)){
            $milesData = getMilestones($conn, $projectID);
            if(!isset($milesData)){
                $createIssue = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    // Issues Data
    function getMilestones($conn, $projectID){
        $milesData = array();
        $query = "SELECT m.*, s.name AS status, s.badge FROM milestones AS m INNER JOIN projects AS p ON m.idProject=p.id INNER JOIN mstatus AS s ON m.idStatus=s.id WHERE p.id=$projectID ORDER BY m.targetDate LIMIT 25";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($milesData, $row);
                }
            } elseif ($result->num_rows == 0) {
                return;
            } else {
                die();
            }
        } else {
            die();
        }
        return $milesData;
    }

    $orderDic = [
        "name" => "ORDER BY m.name",
        "cd" => "ORDER BY m.creationDate ASC",
        "lud" => "ORDER BY m.lastupdatedDate DESC",
        "tar" => "ORDER BY m.targetDate"
    ];
    $filer1Selected = $filer2Selected = false;
    $NoMilestones = false;

    // Apply filters
    if (isset($_POST["searchBTN"])){

        // Filter name
        if(isset($_POST["searchMile"])){
            $SmileName = $_POST["searchMile"];
            $SmileName = "%".$SmileName."%";
            $filterName = "AND m.name LIKE ?";
        }

        // Filter order
        if(isset($_POST["filter"])){
            if(array_key_exists($_POST["filter"], $orderDic)){
                $filterORDER = $orderDic["$_POST[filter]"];
                $filer1Selected = $_POST["filter"];
            } else {
                $info =  "Invalid order filter value! If you didn\'t change anything report with TFV!";
                showAlert($info);
                $filer1Selected = "tar";
                $filterORDER = "ORDER BY m.targetDate";
            }
        } else {
            $filer1Selected = "tar";
            $filterORDER = "ORDER BY m.targetDate";
        }

        // Filter group / status
        if(isset($_POST["filterStatus"])){
            if(is_numeric($_POST["filterStatus"]) && checkmileSStatusID($conn, $_POST["filterStatus"])){
                $filterStatusID = "AND m.idStatus=". $_POST["filterStatus"];
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

        if ($filterOK = MilestoneFilter($conn, $projectID, $filterName, $filterORDER, $filterStatusID, $SmileName)){
            $milesData = $filterOK;
        }
    }

    function MilestoneFilter($conn, $projectID, $NAME, $ORDER, $GROUP, $SmileName){
        if(!($stmt = $conn->prepare("SELECT m.*, s.name AS status, s.badge FROM milestones AS m INNER JOIN projects AS p ON m.idProject=p.id INNER JOIN mstatus AS s ON m.idStatus=s.id WHERE p.id=$projectID $NAME $GROUP $ORDER LIMIT 25"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        if(!$stmt->bind_param("s", $SmileName)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows > 0){
                $milesData = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($milesData, $row);
                }
                $stmt->close();
            } else {
                $GLOBALS["NoMilestones"] = true;
            }
        } else {
            printf("Error in select user query");
            return false;
        }
        if (isset($milesData)){
            return $milesData;
        } else {
            return false;
        }
        
    }

    // New issue btn
    if (isset($_POST["newMileBTN"]) && $UserRole < 4){
        if ( isset($_POST["mileSName"]) && strlen($_POST["mileSName"]) <= 60 && !empty($_POST["mileSName"])) {
            if (isset($_POST["mileSDate"]) && strlen($_POST["mileSDate"]) == 10) {
                $date = explode("-", $_POST["mileSDate"]);
                if (checkdate($date[1], $date[2], $date[0])){
                    if (isset($_POST["mileSStatus"]) && is_numeric($_POST["mileSStatus"]) && checkmileSStatusID($conn, $_POST["mileSStatus"])) {
                        $Data = [
                            "name" => $_POST["mileSName"],
                            "targetDate" => $_POST["mileSDate"],
                            "status" => $_POST["mileSStatus"]
                        ];
                        addNewMilestone($conn, $projectID, $UserData["id"], $Data);
                    } else {
                        $info = "Can\'t validate status value! If you didn\'t change value report with error MMS!";
                        showAlert($info);
                    }
                } else {
                    $info = "Milestone target date incorrect. (Year-Month-Day)";
                    showAlert($info);
                }
                
            } else {
                $info = "Milestone target date incorrect.";
                showAlert($info);
            }
        } else {
            $info = "Milestone name must have 1 to 60 characters.";
            showAlert($info);
        }
    }

    // Removes himself from project
    if(isset($_POST["QuitProjectBTN"]) && $UserRole > 1){
        removeUserFromProject($conn, $UserData["id"], $projectID);
    }

    $AllMilestonesStatus = getMilestoneStatus($conn);
?>

<html lang="en">
    <head>
        <title><?php echo "$projectData[name] - Milestones"; ?></title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-PM"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-PM"));
            }
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    die(header("Location: /projectmanager/errors/?id=CI-BAR-PI"));
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
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
                    
                    <div class="col-lg-12 filter-DIV">
                        <div class="row" style='margin-top:15px;'>
                            <!-- Name filter -->
                            <div class="col-md-12 col-lg-4 filter-DIV-text">
                                <form method="POST" action="">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search by issue name" name="searchMile">
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
                                            <option <?php if($filer1Selected == "tar"){ echo "selected"; } ?> value="tar"> Target date </option>
                                        </select>
                                        &nbsp
                                        <select name="filterStatus" class="form-control" style="background: #3a3f48; color:white; border: none">
                                            <option value="-1" <?php if(!$filer2Selected){ echo "selected"; } ?>> All status </option>
                                            <?php
                                                foreach($AllMilestonesStatus as $issue){
                                                    if ($filer2Selected == $issue["id"]) {
                                                        echo "<option value='$issue[id]' selected>$issue[name]</option>";
                                                    } else {
                                                        echo "<option value='$issue[id]'>$issue[name]</option>";
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
                                            <a class='btn btn-dark float-right' data-toggle='modal' href='#newMilestoneModal' style='margin-bottom: 15px'>
                                                New milestone
                                            </a>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Issue -->
                    <?php
                        if(isset($milesData) && !$NoMilestones){
                            foreach($milesData as $mile){
                                echo "
                                <div class='col-md-12 col-lg-6 col-xl-4 members-DIV'>
                                    <div class='btn-toolbar row' style='margin-top:15px'>
                                        <div class='col-lg-12' style='margin-top:5px;'>
                                            <form method='POST' action=''>
                                                <span class='issue-DIV-title2 issue-DIV-text'>
                                                    $mile[name]
                                                    <span class='badge badge-$mile[badge]'>$mile[status]</span>
                                                    <span class='badge badge-dark'>$mile[lastupdatedDate]</span>
                                                    
                                                    <input type='hidden' name='singleIssueID' value='$mile[id]'>
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                    <div class='task-DIV-text' style='font-size:1.2rem!important;word-break: break-word; margin-bottom: 15px'>
                                        Target date: $mile[targetDate]
                                    </div>
                                </div>
                                ";
                            }
                        } elseif (isset($createIssue) && $createIssue) {
                            echo "<p class='issue-DIV-list'> No milestones yet, create them! </p>";
                        } elseif ($NoMilestones){
                            echo "<p class='issue-DIV-list'> No milestones found! </p>";
                        }
                    ?>
                    <!-- END Issue -->
                        
                    </div>
                </div>

                <!-- New milestone modal -->
                <div class="modal fade" id="newMilestoneModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Head -->
                            <div class="modal-header">
                                <span class="modal-title"> Create new milestone </span>
                                <button type="button" class="close" data-dismiss="modal" aria-label=""><span>×</span></button>
                            </div>        
                            <!-- Body -->
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <span class="modal-subtitle">Milestone name:</span>
                                    <input type='text' class='form-control edit-DIV-Input' name='mileSName' autocomplete='off'/>

                                    <span class="modal-subtitle">Target Date (year-month-day, ex: 1999-01-02):</span>
                                    <input type='text' class='form-control edit-DIV-Input' name='mileSDate' autocomplete='off'/>

                                    <span class="modal-subtitle">Status:</span>

                                    <div class="form-group">
                                        <select class="form-control edit-DIV-Input" name="mileSStatus">
                                            <?php
                                                foreach($AllMilestonesStatus as $status){
                                                    if ($status["id"] != $projectData["idStatus"]){
                                                        echo "<option value='$status[id]'>$status[name]</option>";
                                                    } else {
                                                        echo "<option value='$status[id]' selected>$status[name]</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <input type="submit" class="btn btn-primary font-weight-bold" name="newMileBTN" value="Create milestone">
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