<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        error_reporting(E_ERROR | E_PARSE);
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-OF-ADMINR"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-GF-ADMINR"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-EF-ADMINR"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-AF-ADMINR"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-CF-ADMINR"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/removeFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-RF-ADMINR"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            die(header("Location: /projectmanager/errors/?id=CI-SC-ADMINR"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            die(header("Location: /projectmanager/errors/?id=CI-DF-ADMINR"));
        }
        
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    if($UserData["role"] != 20){
        header("Location: /projectmanager/");
    }

    $reportsData = array();
    $hasReports = false;
    $query = "SELECT r.*, u.username FROM reports AS r INNER JOIN user AS u ON r.idUser=u.id ORDER BY r.creationDate";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasReports = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($reportsData, $row);
            }
        } else {
            $hasReports = false;
        }
        $result->close();
    } else {
        die("Can't get reports");
    }

    if(isset($_POST["REMReport"])){
        if(isset($_POST["REMid"]) && is_numeric($_POST["REMid"])){
            removeReport($conn, $_POST["REMid"]);
        }
    }
    
?>

<html lang="en">
    <head>
        <title>Admin - Reports</title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-ADMINR"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-ADMINR"));
            }
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    die(header("Location: /projectmanager/errors/?id=CI-BAR-ADMINR"));
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-12 page-title">
                            All submitted reports
                        </div>    
                    </div>
                    <hr class='w-100'>

                    <div class="col-12">
                        <div class='row'>

                            <?php
                                if(isset($reportsData) && $hasReports){
                                    foreach($reportsData as $report){
                                        echo "

                                        <div class='col-md-12 col-xl-6' style='margin-bottom: 10px'>
                                            <div class='col-12 bg-dark text-white' style='border-radius:5px;'>
                                                <div class='row task-border-bottom'>
                                                    <form method='POST' action='' class='task-margin-tb-10'>
                                                        <div class='col-12 project-title'>
                                                            ID: $report[id] & Code: $report[code]
                                                            <input type='submit' class='btn btn-danger' name='REMReport' value='Remove'>
                                                            <input type='hidden' name='REMid' value='$report[id]'>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div class='row' style='border-radius:5px;'>
                                                    <div class='col-12 task-margin-tb-10 project-text'>
                                                        $report[description]
                                                    </div>
                                                </div>

                                                <div class='row task-border-top' style='border-radius:5px;'>
                                                    <div class='col-12 task-margin-tb-10 project-text'>
                                                        By: $report[username]
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        ";
                                    }
                                } else {
                                    echo "<p class='issue-DIV-list col-12' style='margin-top: 5px'> There is no reports. </p>";
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