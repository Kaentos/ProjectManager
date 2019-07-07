<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/removeFunctions.php";
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

    // Get issue ID from URL GET
    if (isset($_GET["issue"]) && is_numeric($_GET["issue"])){
        if (checkIssueID($conn, $_GET["issue"], $projectID)){
            $issueID = $_GET["issue"];
        } else {
            header("location: /projectmanager/project?id=$projectID");
        }
    } else {
        header("location: /projectmanager/project?id=$projectID");
    }

    // Get issue data
    if (isset($projectID)){
        $projectData = getSingleProjectData($conn, $projectID, $UserData["id"]);
        if (isset($projectData)){
            $issueData = getSingleIssue($conn, $projectID, $issueID);
            if(!isset($issueData)){
                $createIssue = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    if(isset($issueData)){
        $currentIssueName = $issueData["name"];
        $currentIssueDes = $issueData["Des"];
        $currentIssueStatus = $issueData["idStatus"];
    } else {
        $currentIssueName = "";
        $currentIssueDes = "";
        $currentIssueStatus = "";
    }

    // Edit issue btn
    if (isset($_POST["editIssueBTN"])){
        if ( isset($_POST["issueName"]) && strlen($_POST["issueName"]) <= 60 && !empty($_POST["issueName"])) {
            if (isset($_POST["issueDes"]) && strlen($_POST["issueDes"]) <= 150 && !empty($_POST["issueDes"])) {
                if (isset($_POST["issueStatus"]) && is_numeric($_POST["issueStatus"]) && checkIssueStatusID($conn, $_POST["issueStatus"])) {
                    $Data = [
                        "name" => $_POST["issueName"],
                        "des" => $_POST["issueDes"],
                        "status" => $_POST["issueStatus"]
                    ];
                    $currentIssueName = $Data["name"];
                    $currentIssueDes = $Data["des"];
                    $currentIssueStatus = $Data["status"];
                    editIssue($conn, $Data, $issueData, $UserData);
                } else {
                    $info = "Can\'t validate status value! If you didn\'t change value report with error MTS!";
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
            addIssueNewComment($conn, $issueID, $comment, $UserData["id"]);
        }
    }

    // Removes himself from project
    if(isset($_POST["QuitProjectBTN"]) && $UserRole > 1){
        removeUserFromProject($conn, $UserData["id"], $projectID);
    }

    // Remove task
    if (isset($_POST["REMissue"])){
        removeIssue($conn, $projectID, $issueID);
    }

    $AllIssueComments = getIssueComments($conn, $issueID);

    $AllIssuesStatus = getIssuesStatus($conn);
?>

<html lang="en">
    <head>
    <title><?php echo "$projectData[name] - $issueData[name]"; ?></title>
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
                    
                    
                    <!-- Issue -->
                    <?php
                        if(isset($issueData) && !isset($createIssue)){
                            echo "
                            <div class='col-sm-12 col-md-10 col-lg-10 col-xl-6 issue-DIV'>
                                <div class='btn-toolbar row' style='margin-top:15px'>
                                    <div class='col-lg-12' style='margin-top:5px;'>
                                        <form method='POST' action='' class='Only-issue-DIV-title issue-DIV-text'>
                                            Issue name: $issueData[name]";
                                            if ($UserRole < 3){
                                                echo "
                                                    <a href='#editIssueModal' data-toggle='modal' class='edit-pen'>
                                                        <i class='fas fa-pen'></i>
                                                    </a>
                                                    <button type='submit' name='REMissue' class='btn btn-danger'>
                                                        <i class='far fa-times-circle'></i>
                                                    </button>
                                                ";
                                            }
                            echo "
                                        </form>
                                    </div>
                                </div>
                                <hr class='hr-issue'>
                                <div class='Only-issue-DIV-Des' style='word-break: break-word; margin-bottom: 15px'>
                                    <p><b>Creation date:</b> <span class='badge badge-dark'>$issueData[creationDate]</span> by <b> $issueData[idCreator] </b> </p>
                                    <p><b>Last time updated:</b> <span class='badge badge-dark'>$issueData[lastupdatedDate]</span> by <b> $issueData[idUpdateUser] </b> </p>
                                    <p><b>Issue Status: </b><span class='badge badge-$issueData[badge]'>$issueData[status]</span> </p>
                                    <p><b>Issue description:</b><br>
                                    $issueData[Des]</p>
                                    
                                </div>
                            </div>
                            ";
                        } elseif (isset($createIssue) && $createIssue) {
                            echo "<p class='issue-DIV-list'> No issues yet, create them! </p>";
                        }
                    ?>
                    <!-- END Issue -->
                </div>

                <hr class="w-50" style="margin-top:0px; margin-bottom:0px">

                <!-- Comments -->
                <div class="row d-flex justify-content-center" style="margin-top: 0px">
                    <div class="col-12 d-flex justify-content-center" style="padding-left:0px">
                        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-6 Only-issue-DIV-title" id="Comments" style="padding-left:5px">
                            Comments
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-10 col-lg-10 col-xl-6" style="background-color:green;" id="#Dis">
                        <form class="col-12" method="POST" action="" style="margin-top: 20px">
                            <div class="row">
                            <div class="col-4 col-lg-4 col-xl-2">
                                    <img class="img-thumbnail" style="height: 100px; width: auto;" src="/projectmanager/img/UIMG/9.png" alt="User picture">
                                </div>
                                <div class="col-8 col-lg-8 col-xl-10">
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
                            if(isset($AllIssueComments) && $AllIssueComments){
                                foreach($AllIssueComments as $comment){
                                    echo "
                                    <div class='col-12' style='margin-bottom: 15px'>
                                        <div class='row'>
                                            <div class='col-4 col-lg-4 col-xl-2'>
                                                <img class='img-thumbnail' style='height: 100px; width: auto;' src='/projectmanager/img/UIMG/9.png'>
                                            </div>
                                            <div class='col-8 col-lg-8 col-xl-10'>
                                                <div class='alert alert-light issue-comment-text'>
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
                                    <div class='col-12 Only-issue-DIV-title' style='margin-bottom: 15px'>
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

                <!-- Edit issue modal -->
                <div class="modal fade" id="editIssueModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Head -->
                            <div class="modal-header">
                                <span class="modal-title"> Edit: <?php echo $issueData["name"]?> </span>
                                <button type="button" class="close" data-dismiss="modal" aria-label=""><span>×</span></button>
                            </div>        
                            <!-- Body -->
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <span class="modal-subtitle">Issue name:</span>
                                    <input type='text' class='form-control edit-DIV-Input' name='issueName' value='<?php echo "$currentIssueName"; ?>' autocomplete='off'/>

                                    <span class="modal-subtitle">Description:</span>
                                    <textarea class='form-control edit-DIV-Input' rows='3' name='issueDes' autocomplete='off'><?php echo "$currentIssueDes"; ?></textarea>

                                    <span class="modal-subtitle">Status:</span>

                                    <div class="form-group">
                                        <select class="form-control edit-DIV-Input" name="issueStatus">
                                            <?php
                                                foreach($AllIssuesStatus as $status){
                                                    if ($status["id"] != $currentIssueStatus){
                                                        echo "<option value='$status[id]'>$status[name]</option>";
                                                    } else {
                                                        echo "<option value='$status[id]' selected>$status[name]</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Don't change values, if you didn't report it.</div>
                                    </div>
                                    <input type="submit" class="btn btn-success font-weight-bold" name="editIssueBTN" value="Edit issue">
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