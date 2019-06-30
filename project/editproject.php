<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php";

        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    if (isset($_GET["id"]) && is_numeric($_GET["id"])){
        $projectID = $_GET["id"];
    } else {
        header("location: /projectmanager/dashboard/projects.php");
    }

    if (isset($projectID)){
        $projectData = getSingleProjectData($conn, $projectID, $UserData["id"]);
        if (isset($projectData)){
            
        } else {
            header("location: /projectmanager/dashboard/projects.php");
        }
    }

    $AllProjectStatus = getProjectStatus($conn);
    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);

    $nameERR = $desERR = $statusERR = -1;
    if(isset($_POST["updateP"])){
        if(!isset($_POST["name"]) || strlen($_POST["name"]) <= 20){
            $nameERR = 0;
        } elseif (!isset($_POST["des"]) || strlen($_POST["des"]) <= 60) {
            $desERR = 0;
        } elseif (!isset($_POST["status"]) || is_numeric($_POST["status"]) || !checkStatusID($conn, $_POST["status"])) {
            $statusERR = 0;
        } else {
            $Data = [
                "name" => $_POST["name"],
                "des" => $_POST["des"],
                "status" => $_POST["status"]
            ];
            editProject($conn, $Data, $projectData, $UserData);
        }
    }

    function editProject($conn, $Data, $projectData, $UserData){
        if (!($projectData["name"] == $Data["name"] && $projectData["des"] == $Data["des"] && $projectData["idStatus"] == $Data["status"])){
            $currentDate = getCurrentDate();

            if(!($stmt = $conn->prepare("UPDATE projects SET name=?, des=?, code=?, idStatus=?, idUpdateUser=?, lastupdatedDate=? WHERE id=?"))) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            if(!$stmt->bind_param("sssiisi", $Data["name"], $Data["des"], $projectData["code"], $Data["status"], $UserData["id"], $currentDate, $projectData["id"])) {
                die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute()) {
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            } else {
                header("Refresh:0");
            }
        }

        return;
    }
?>

<html lang="en">
    <head>
        <title><?php echo "Editing - $projectData[name]" ?></title>
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

                        <!-- Current project -->
                        <div class="col-lg-12 col-xl-5 edit-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12" style="margin-top:5px;">
                                    <span class="edit-DIV-title">Current project details</span>
                                </div>
                            </div>
                            <hr class="hr-edit">
                            <div style="word-break: break-word;">
                                <span class="edit-DIV-InputTitle">Name:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary edit-DIV-Input' role='alert'>
                                        <?php echo $projectData["name"] ?>
                                    </div>
                                </div>

                                <span class="edit-DIV-InputTitle">Description:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary edit-DIV-Input' role='alert'>
                                        <?php echo $projectData["des"] ?>
                                    </div>
                                </div>
                                
                                <div style="margin-bottom:1rem">
                                    <span class="edit-DIV-InputTitle">Status:</span>
                                    <?php 
                                        echo "<span class='badge badge-$projectData[badge] edit-DIV-InputTitle'>$projectData[Sname]</span>";
                                    ?>
                                </div>

                                <span class="edit-DIV-InputTitle">Code:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary edit-DIV-Input' role='alert'>
                                        <?php echo $projectData["code"]; ?>
                                    </div>
                                </div>

                                <span class="edit-DIV-InputTitle">Last update:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary edit-DIV-Input' role='alert'>
                                        <?php
                                            echo "
                                                $projectData[creationDate] by $projectData[idCreator]  
                                            ";  
                                        ?>
                                    </div>
                                </div>
                                
                                <span class="edit-DIV-InputTitle">Creation:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary edit-DIV-Input' role='alert'>
                                        <?php
                                            echo "
                                                $projectData[lastupdatedDate] by $projectData[idUpdateUser]  
                                            ";  
                                        ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- End current details -->

                        <div class="col-lg-0 col-xl-1"></div>

                        <!-- Update project -->
                        <div class="col-lg-12 col-xl-5 task-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12 col-xl-12" style="margin-top:5px;">
                                    <span class="task-DIV-title">New project details</span>
                                </div>
                            </div>
                            <hr class="hr-task">
                            <div style="word-break: break-word;">
                                <form method="post" action="">
                                    <span class="edit-DIV-InputTitle">Name:</span>
                                    <div class="form-group">
                                        <input type="text" class="form-control edit-DIV-Input" name="name" autocomplete="off" value=<?php echo "'$projectData[name]'" ?> />
                                    </div>

                                    <span class="edit-DIV-InputTitle">Description:</span>
                                    <div class="form-group">
                                        <textarea class="form-control edit-DIV-Input" rows="2" name="des" autocomplete="off"><?php echo $projectData["des"] ?></textarea>
                                    </div>

                                    <span class="edit-DIV-InputTitle">Status:</span>
                                    <div class="form-group">
                                        <select class="form-control edit-DIV-Input" name="status">
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
                                    </div>

                                    <input type="submit" class="btn btn-success" name="updateP" value="Update"/>
                                </form>
                            </div>
                        </div>
                        <!-- End update -->
                        
                        <div class="col-lg-11">
                            <div class="row">
                                <a href="/projectmanager/project/?id=<?php echo $projectData["id"] ?>" class="btn btn-dark edit-DIV-InputTitle">
                                    Back to <?php echo $projectData["name"] ?>
                                </a>
                            </div>
                        </div>
                    </div>
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