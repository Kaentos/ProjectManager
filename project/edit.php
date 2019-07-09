<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/removeFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php";
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
            
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    $AllProjectStatus = getProjectStatus($conn);
    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);

    if ($UserRole > 3){
        header("Location: /projectmanager/project/?id=$projectID");
    }

    if(isset($_POST["newCode"])){
        $projectCode = otherGenInviteCode($conn);
        goto updateProject;
    } else {
        $projectCode = $projectData["code"];
    }

    $nameERR = $desERR = $statusERR = -1;
    if(isset($_POST["updateP"])){
        updateProject:
        if(!isset($_POST["name"]) || strlen($_POST["name"]) > 20 || empty($_POST["name"])){
            $ERR = $_POST["name"];
            $nameERR = 0;
            $desERR = -1;
            $statusERR = -1;
        } elseif (!isset($_POST["des"]) || strlen($_POST["des"]) > 60 || empty($_POST["des"])) {
            $ERR = $_POST["des"];
            $desERR = 0;
            $nameERR = -1;
            $statusERR = -1;
        } elseif (!isset($_POST["status"]) || !is_numeric($_POST["status"]) || !checkProjectStatusID($conn, $_POST["status"])) {
            $statusERR = 0;
            $nameERR = -1;
            $desERR = -1;
        } else {
            $statusERR = -1;
            $nameERR = -1;
            $desERR = -1;
            $Data = [
                "name" => $_POST["name"],
                "des" => $_POST["des"],
                "status" => $_POST["status"],
                "code" => $projectCode
            ];
            editProject($conn, $Data, $projectData, $UserData);
        }
    }

    if(isset($_POST["REMProject"])){
        $confirmDelete = true;
    }
    if(isset($_POST["YesDelete"])){
        removeProject($conn, $projectID);
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
        <div class="page-wrapper chiller-theme">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>

            <main class="page-content">
                <div class="container-fluid">
                    <div class="row d-flex justify-content-center">

                        <!-- Current project -->
                        <div class="col-lg-12 col-xl-5">
                            <div class="bg-dark text-white col-12" style="border-radius:5px">
                                <div class="row project-border-bottom">
                                    <div class="col-12" style="margin-bottom:10px; margin-top:10px">
                                        <span style="font-size:1.8rem; font-weight: 500;">Current project details</span>
                                    </div>
                                </div>
                                <div style="word-break: break-word;">
                                    <span class="edit-DIV-InputTitle">Name:</span>
                                    <div class='alert alert-secondary edit-DIV-Static-Input' role='alert'>
                                        <?php echo $projectData["name"] ?>
                                    </div>

                                    <span class="edit-DIV-InputTitle">Description:</span>
                                    <div class='alert alert-secondary edit-DIV-Static-Input' role='alert'>
                                        <?php echo $projectData["des"] ?>
                                    </div>
                                    
                                    <div style="margin-bottom:1rem">
                                        <span class="edit-DIV-InputTitle">Status:</span>
                                        <?php 
                                            echo "<span class='badge badge-$projectData[badge] edit-DIV-InputTitle'>$projectData[Sname]</span>";
                                        ?>
                                    </div>

                                    <span class="edit-DIV-InputTitle">Code:</span>
                                    <div class='alert alert-secondary edit-DIV-Static-Input' role='alert'>
                                        <?php echo $projectData["code"]; ?>
                                    </div>

                                    <span class="edit-DIV-InputTitle">Last update:</span>
                                    <div class='alert alert-secondary edit-DIV-Static-Input' role='alert'>
                                        <?php
                                            echo "
                                                $projectData[creationDate] by $projectData[idUpdateUser]  
                                            ";  
                                        ?>
                                    </div>
                                    
                                    <span class="edit-DIV-InputTitle">Creation:</span>
                                    <div class='alert alert-secondary edit-DIV-Static-Input' role='alert'>
                                        <?php
                                            echo "
                                                $projectData[lastupdatedDate] by $projectData[idCreator]
                                            ";  
                                        ?>
                                    </div>
                                </div>
                                <form method="POST" action="" class="row project-border-top">
                                    <div class="col-12" style="margin-top: 10; margin-bottom: 10px">
                                        
                                        <?php
                                            if(isset($confirmDelete) && $confirmDelete) {
                                                echo "<span class='edit-DIV-InputTitle'>Are you sure?</span>
                                                        <input type='submit' name='YesDelete' class='btn btn-danger edit-DIV-InputTitle' value='Yes'>
                                                        <input type='submit' class='btn btn-success edit-DIV-InputTitle' value='No'>
                                                ";
                                            } else {
                                                echo "<input type='submit' name='REMProject' class='btn btn-danger edit-DIV-InputTitle' value='Remove'>";
                                            }
                                        ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End current details -->

                        <!-- Update project -->
                        <div class="col-lg-12 col-xl-5">
                            <div class="col-12 task-DIV">
                                <div class="btn-toolbar row" style="margin-top:15px">
                                    <div class="col-lg-12 col-xl-12" style="margin-top:5px;">
                                        <span class="task-DIV-title">New project details</span>
                                    </div>
                                </div>
                                <hr class="hr-task">
                                <div style="word-break: break-word;">
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <span class="edit-DIV-InputTitle">Name:</span>
                                            <?php
                                                if ($nameERR == 0){
                                                    echo "
                                                        <input type='text' class='form-control edit-DIV-Input is-invalid' name='name' autocomplete='off' value='$ERR'/>
                                                        <div class='invalid-feedback'>
                                                            Must be have 1 to 20 characters.
                                                        </div>
                                                    ";
                                                } else {
                                                    echo "
                                                        <input type='text' class='form-control edit-DIV-Input is-valid' name='name' autocomplete='off' value='$projectData[name]'/>
                                                    ";
                                                }
                                            ?>
                                        </div>

                                        <div class="form-group">
                                            <span class="edit-DIV-InputTitle">Description:</span>
                                            <?php
                                                if ($desERR == 0){
                                                    echo "
                                                        <textarea class='form-control edit-DIV-Input is-invalid' rows='2' name='des' autocomplete='off'>$ERR</textarea>
                                                        <div class='invalid-feedback'>
                                                            Must be have 1 to 60 characters.
                                                        </div>
                                                    ";
                                                } else {
                                                    echo "
                                                        <textarea class='form-control edit-DIV-Input is-valid' rows='2' name='des' autocomplete='off'>$projectData[des]</textarea>
                                                    ";
                                                }
                                            ?>
                                        </div>
                                        
                                        <span class="edit-DIV-InputTitle">Status:</span>
                                        <div class="form-group">
                                            <select class="form-control edit-DIV-Input <?php if($statusERR == 0) echo "is-invalid" ?>" name="status">
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

                                        <input type="submit" class="btn btn-success edit-DIV-InputTitle" name="updateP" value="Update"/>
                                        <input type="submit" class="btn btn-info edit-DIV-InputTitle" name="newCode" value="Update with new code"/>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End update -->
                        
                        <div class="col-lg-12">
                            <div class="col-12 text-center">
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