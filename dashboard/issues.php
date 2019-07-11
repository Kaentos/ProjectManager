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
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            sendError("FIM-ADDF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            sendError("FIM-CF");
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

    $IssuesData = array();
    $hasIssues = false;
    $query = "SELECT i.*, s.name AS status, s.badge, p.id AS projectID, p.name AS projectName FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id INNER JOIN issuefollow AS iff ON i.id=iff.idIssue WHERE iff.idUser=$UserData[id] LIMIT 5";
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

    $orderDic = [
        "name" => "ORDER BY i.name",
        "cd" => "ORDER BY i.creationDate ASC",
        "lud" => "ORDER BY i.lastupdatedDate DESC"
    ];
    $filer1Selected = $filer2Selected = false;

    if(isset($_POST["searchBTN"]) && $hasIssues){
        if(isset($_POST["searchIssue"])){
            $nameFilter = $_POST["searchIssue"];
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
                $info =  "Invalid order filter value! If you didn\'t change anything report with IFV!";
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
                $info = "Invalid status filter value! If you didn\'t change anything report with IFS!";
                showAlert($info);
                $filer2Selected = false;
                $filterStatusID = "";
            }
        } else {
            $filer2Selected = false;
            $filterStatusID = "";
        }

        if ($temp = searchIssue($conn, $nameFilter, $filterORDER, $filterStatusID, $UserData)){
            $IssuesData = $temp;
        } else {
            $filterHasIssues = true;
        }
    }

    function searchIssue($conn, $nameFilter, $ORDER, $GROUP, $UserData){
        if(!$stmt = $conn->prepare("SELECT i.*, s.name AS status, s.badge, p.id AS projectID, p.name AS projectName FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id INNER JOIN issuefollow AS iff ON i.id=iff.idIssue WHERE iff.idUser=$UserData[id] AND i.name LIKE ? $GROUP $ORDER LIMIT 25")) {
            // sendError("MPT-PT-P");
            echo "SELECT i.*, s.name AS status, s.badge, p.id AS projectID, p.name AS projectName FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id INNER JOIN issuefollow AS iff ON i.id=iff.idIssue WHERE iff.idUser=$UserData[id] AND i.name LIKE ? $GROUP $ORDER LIMIT 25";
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

    $AllIssuesStatus = getIssuesStatus($conn);
?>

<html lang="en">
    <head>
        <title>Followed issues</title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-MPI"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-MPI"));
            }
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    die(header("Location: /projectmanager/errors/?id=CI-BAR-MPI"));
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                            <div class="col-12 col-lg-4 col-xl-6 page-title">
                                Followed issues
                            </div>
                            <form method="POST" action="" class="col-12 col-lg-8 col-xl-6" style="text-align:right">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by issue name" name="searchIssue">
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
                                </div>
                            </form>    
                        </div>
                        <hr class='w-100'>
                        <?php
                            if($hasIssues && (!isset($filterHasIssues) || !$filterHasIssues)){
                                foreach($IssuesData as $issue){
                                    echo "
                                        <div class='col-md-12 col-xl-4' style='margin-bottom: 10px'>
                                            <div class='col-12 text-white bg-danger' style='border-radius:5px;'>
                                                <div class='row issues-border-bottom'>
                                                    <div class='col-12 task-margin-tb-10'>
                                                        <a href='/projectmanager/project/issues/issue?id=$issue[projectID]&issue=$issue[id]' class='issues-title'>
                                                            $issue[name]
                                                        </a>";
                                    if($issue["badge"] == "danger"){
                                        echo "<span class='badge badge-$issue[badge] custom-badge-border issues-badge-text'>$issue[status]</span>";
                                    } else {
                                        echo "<span class='badge badge-$issue[badge] issues-badge-text'>$issue[status]</span>";
                                    }
                                    echo "         
                                                    </div>
                                                </div>

                                                <div class='row task-margin-tb-10'>
                                                    <div class='col-12 project-text'>
                                                        $issue[Des]
                                                    </div>
                                                </div>

                                                <div class='row issues-border-top'>
                                                    <div class='col-12 project-text task-margin-tb-10'>
                                                        From:
                                                        <a href='/projectmanager/project/?id=$issue[projectID]' style='color:white; text-decoration:none'>
                                                            <b>$issue[projectName]</b>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ";
                                }
                            } elseif (isset($filterHasIssues) && $filterHasIssues) {
                                echo "
                                    <div class='col-12'>
                                        <div class='text-center'>
                                            <h4>No issues found for your search $vname</h4>
                                        </div>
                                    </div>";
                            } else {
                                echo "<div class='col-12'><h4>You don't follow any issue, what about following some?</h4></div>";
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