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
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title><?php echo $projectData["name"] ?></title>
        <meta name="description" content="Project Manager">
        <meta name="author" content="Miguel Magueijo">
        <link rel="icon" href="/projectmanager/img/icon.png">

        <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"; ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="container-fluid">
                    <div class="row d-flex justify-content-center">

                        <!-- Current project -->
                        <div class="col-lg-12 col-xl-5 task-DIV">
                            <div class="btn-toolbar row" style="margin-top:15px">
                                <div class="col-lg-12" style="margin-top:5px;">
                                    <span class="task-DIV-title">Current project details</span>
                                </div>
                            </div>
                            <hr class="hr-task">
                            <div style="word-break: break-word;">
                                <span>Name:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary' role='alert'>
                                        <?php echo $projectData["name"] ?>
                                    </div>
                                </div>

                                <span>Description:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary' role='alert'>
                                        <?php echo $projectData["des"] ?>
                                    </div>
                                </div>
                                
                                <div style="margin-bottom:1rem">
                                    <span>Status:</span>
                                    <?php 
                                        echo "<span class='badge badge-$projectData[badge]'>$projectData[Sname]</span>";
                                    ?>
                                </div>

                                <span>Code:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary' role='alert'>
                                        <?php echo $projectData["code"]; ?>
                                    </div>
                                </div>

                                <span>Last update:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary' role='alert'>
                                        <?php
                                            echo "
                                                $projectData[creationDate] by $projectData[idCreator]  
                                            ";  
                                        ?>
                                    </div>
                                </div>
                                
                                <span>Creation:</span>
                                <div class="form-group">
                                    <div class='alert alert-secondary' role='alert'>
                                        <?php
                                            echo "
                                                $projectData[lastupdatedDate] by $projectData[idUpdateUser]  
                                            ";  
                                        ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- End Tasks -->

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
                                    Name:
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="name" autocomplete="off" value=<?php echo "'$projectData[name]'" ?> />
                                    </div>

                                    Description:
                                    <div class="form-group">
                                        <textarea class="form-control" rows="2" name="des" autocomplete="off"><?php echo $projectData["des"] ?></textarea>
                                    </div>

                                    Status:
                                    <div class="form-group">
                                        <select class="form-control" name="status">
                                            <?php
                                                foreach($AllProjectStatus as $status){
                                                    if ($status["id"] != $projectData["idStatus"]){
                                                        echo "<option value='$status[id]'>$status[name]</option>";
                                                    }

                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <input type="submit" class="btn btn-success" name="updateEM" value="Update"/>
                                </form>
                            </div>
                        </div>
                        <!-- End update -->
                        
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