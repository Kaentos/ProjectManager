<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php";
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
            $issuesData = getIssues($conn, $projectID);
            if(!isset($issuesData)){
                $createIssue = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    if (!$UserRole = checkUserInProject($conn, $projectID, $UserData["id"])){
        header("location: /projectmanager/dashboard/projects");
    }

    // Issues Data
    function getIssues($conn, $projectID){
        $issuesData = array();
        $query = "SELECT i.*, s.name AS status, s.badge FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id WHERE p.id=$projectID ORDER BY i.lastupdatedDate DESC LIMIT 25";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($issuesData, $row);
                }
            } elseif ($result->num_rows == 0) {
                return;
            } else {
                die();
            }
        } else {
            die();
        }
        return $issuesData;
    }

    $orderDic = [
        "name" => "ORDER BY i.name",
        "cd" => "ORDER BY i.creationDate ASC",
        "lud" => "ORDER BY i.lastupdatedDate DESC"
    ];
    $filer1Selected = $filer2Selected = false;
    $NoIssues = false;

    // Apply filters
    if (isset($_POST["searchBTN"])){

        // Filter name
        if(isset($_POST["searchIssue"])){
            $SissueName = $_POST["searchIssue"];
            $SissueName = "%".$SissueName."%";
            $filterName = "AND i.name LIKE ?";
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
                $filterORDER = "ORDER BY i.lastupdatedDate";
            }
        } else {
            $filer1Selected = "lud";
            $filterORDER = "ORDER BY i.lastupdatedDate";
        }

        // Filter group / status
        if(isset($_POST["filterStatus"])){
            if(is_numeric($_POST["filterStatus"]) && checkIssueStatusID($conn, $_POST["filterStatus"])){
                $filterStatusID = "AND i.idStatus=". $_POST["filterStatus"];
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

        if ($filterOK = IssueFilter($conn, $projectID, $filterName, $filterORDER, $filterStatusID, $SissueName)){
            $issuesData = $filterOK;
        }
    }

    function IssueFilter($conn, $projectID, $NAME, $ORDER, $GROUP, $SissueName){
        if(!($stmt = $conn->prepare("SELECT i.*, s.name AS status, s.badge FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id WHERE p.id=$projectID $NAME $GROUP $ORDER LIMIT 25"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        if(!$stmt->bind_param("s", $SissueName)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows > 0){
                $issuesData = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($issuesData, $row); 
                }
                $stmt->close();
            } else {
                $GLOBALS["NoIssues"] = true;
            }
        } else {
            printf("Error in select user query");
            return false;
        }
        if (isset($issuesData)){
            return $issuesData;
        } else {
            return false;
        }
        
    }

    // New issue btn
    if (isset($_POST["newIssueBTN"])){
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
                    $info = "Can\'t validate status value! If you didn\'t change value report with error MIS!";
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

    // User starts following issue
    if(isset($_POST["followIssueBTN"])){
        if(isset($_POST["singleIssueID"]) && is_numeric($_POST["singleIssueID"])){
            if(checkIssueID($conn, $_POST["singleIssueID"], $projectID) ){
                $issueID = $_POST["singleIssueID"];
                addFollowToIssue($conn, $issueID, $UserData["id"]);
            }
            
        }
    }

    // User removes follow from issue
    if(isset($_POST["REMfollowIssueBTN"])){
        if(isset($_POST["singleIssueID"]) && is_numeric($_POST["singleIssueID"])){
            if(checkIssueID($conn, $_POST["singleIssueID"], $projectID) ){
                $issueID = $_POST["singleIssueID"];
                removeUserIssueFollow($conn, $issueID, $UserData["id"]);
            }   
        }
    }

    // Removes himself from project
    if(isset($_POST["QuitProjectBTN"]) && $UserRole > 1){
        removeUserFromProject($conn, $UserData["id"], $projectID);
    }

    $AllIssuesStatus = getIssuesStatus($conn);
?>

<html lang="en">
    <head>
    <title><?php echo "$projectData[name] - Issues"; ?></title>
        <?php
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html";
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html";
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    sendError("MPB-PII");
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
                                        <input type="text" class="form-control" placeholder="Search by issue name" name="searchIssue">
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
                                                foreach($AllIssuesStatus as $issue){
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
                                            <a class='btn btn-dark float-right' data-toggle='modal' href='#newIssueModal' style='margin-bottom: 15px'>
                                                New Issue
                                            </a>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Issue -->
                    <?php
                        if(isset($issuesData) && !$NoIssues){
                            foreach($issuesData as $issue){
                                echo "
                                <div class='col-lg-12 col-xl-6 issue-DIV'>
                                    <div class='btn-toolbar row' style='margin-top:15px'>
                                        <div class='col-lg-12' style='margin-top:5px;'>
                                            <form method='POST' action=''>
                                                <span class='issue-DIV-title2 issue-DIV-text'>
                                                    <a href='/projectmanager/project/issues/issue?id=$projectData[id]&issue=$issue[id]'>
                                                        $issue[name]
                                                    </a>
                                                    <span class='badge badge-$issue[badge]'>$issue[status]</span>
                                                    <span class='badge badge-dark'>$issue[lastupdatedDate]</span>
                                                    
                                                    <input type='hidden' name='singleIssueID' value='$issue[id]'>
                                                    <a href='/projectmanager/project/issues/issue?id=$projectData[id]&issue=$issue[id]#Comments' class='btn bg-dark text-white float-right'>
                                                        <i class='fas fa-comments'></i>
                                                    </a>";
                                if (checkUserIssueFollow($conn, $issue["id"])){
                                    echo "<button type='submit' name='REMfollowIssueBTN' class='btn bg-dark text-white float-right'><i class='fas fa-times'></i></button>";
                                } else {
                                    echo "<button type='submit' name='followIssueBTN' class='btn bg-dark text-white float-right'><i class='fas fa-plus'></i></button>";
                                }
                                echo "
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                    <hr class='hr-issue'>
                                    <div class='issue-DIV-Des' style='word-break: break-word; margin-bottom: 15px'>
                                        $issue[Des]
                                    </div>
                                </div>
                                ";
                            }
                        } elseif (isset($createIssue) && $createIssue) {
                            echo "<p class='issue-DIV-list'> No issues yet, create them! </p>";
                        } elseif ($NoIssues){
                            echo "<p class='issue-DIV-list'> No issues found! </p>";
                        }
                    ?>
                    <!-- END Issue -->
                        
                    </div>
                </div>

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
                                    </div>
                                    <input type="submit" class="btn btn-success font-weight-bold" name="newIssueBTN" value="Create issue">
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