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

    if(!($UserData["role"] == 20)){
        header("Location: /projectmanager/");
    }

    $projectsData = array();
    $hasProjects = false;
    $query = "SELECT p.* FROM projects AS p INNER JOIN user AS u WHERE p.idCreator=u.id ORDER BY p.id DESC";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasProjects = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $Temp = getUsername($conn,$row["idCreator"]);
                $row["idCreator"] = $Temp;
                $Temp = getUsername($conn,$row["idUpdateUser"]);
                $row["idUpdateUser"] = $Temp;
                array_push($projectsData, $row);
            }
        } else {
            $hasProjects = false;
        }
        $result->close();
    } else {
        die("Can't get projects");
    }

    if(isset($_POST["REMuser"])){
        if(isset($_POST["REMid"]) && is_numeric($_POST["REMid"])){
            if($_POST["REMid"] != 13 && $_POST["REMid"] != $UserData["id"]){
                removeProject($conn, $_POST["REMid"]);
            }
        }
    }
    
?>

<html lang="en">
    <head>
    <title>Admin - Projects</title>
        <?php
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html";
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html";
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    sendError("MPB-PM");
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-12 page-title">
                            All projects
                        </div>    
                    </div>
                    <hr class='w-100'>

                    <div class="col-12">
                        <div class='row'>

                            <?php
                                if(isset($projectsData) && $hasProjects){
                                    foreach($projectsData as $project){
                                        echo "
                                        <div class='col-md-12 col-xl-6' style='margin-bottom: 10px'>
                                            <div class='col-12 bg-dark text-white' style='border-radius:5px;'>
                                                <div class='row task-border-bottom'>
                                                    <form method='POST' action='' class='task-margin-tb-10'>
                                                        <div class='col-12 project-title'>
                                                            ID: $project[id]
                                                            <input type='submit' class='btn btn-danger' name='REMuser' value='Remove'>
                                                            <input type='hidden' name='REMid' value='$project[id]'>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div class='row' style='border-radius:5px;'>
                                                    <div class='col-12 task-margin-tb-10 project-text'>
                                                        <div class='row'>
                                                            <div class='col-lg-12 col-xl-6'>
                                                                <b>Project name</b>: $project[name]
                                                                <br>
                                                                <b>Project description</b>: $project[des]
                                                            </div>
                                                            <div class='col-lg-12 col-xl-6'>
                                                                <b>Creator</b>: $project[idCreator]
                                                                <br>
                                                                <b>Last updated</b>: $project[lastupdatedDate]
                                                                <br>
                                                                <b>Updater</b>: $project[idUpdateUser]
                                                                <br>
                                                                <b>Created at</b>: $project[creationDate]
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ";
                                    }
                                } else {
                                    echo "<p class='issue-DIV-list col-12' style='margin-top: 5px'> There is no projects. </p>";
                                }

                            ?>
                            
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