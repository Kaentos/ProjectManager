<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=FIM-OF"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=FIM-GF"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            die(header("Location: /projectmanager/errors/?id=FIM-SCF"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            die(header("Location: /projectmanager/errors/?id=FIM-DBF"));
        }
        
        $conn = ConnectRoot();

        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    
    $desERR = "";
    if(isset($_POST["reportBTN"])){
        if(isset($_POST["reportCode"]) && (strlen($_POST["reportCode"]) > 2 && strlen($_POST["reportCode"]) < 15)){
            $errorCode = $_POST["reportCode"];
            $tempCode = $errorCode;
        } else {
            $errorCode = null;
        }
        if(isset($_POST["reportDes"]) && !empty($_POST["reportDes"])){
            if(strlen($_POST["reportDes"]) > 20 && strlen($_POST["reportDes"]) < 250){
                $reportDes = $_POST["reportDes"];
            } else {
                $tempDes = $_POST["reportDes"];
                $desERR = "Description must have more than 20 characters and less than 250.";
            }
        } else {
            $tempDes = "";
            $desERR = "Empty description.";
        }

        if(isset($reportDes)){
            sendReport($conn, $errorCode, $reportDes, $UserData["id"]);
        }
    }

    function sendReport($conn, $code, $des, $userID){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO reports (code, description, idUser, creationDate) VALUES (?,?,?,?)"))) {
            die(header("Location: /projectmanager/errors/?id=R-PT-P"));
        }
        if(!$stmt->bind_param("ssis", $code, $des, $userID, $currentDate)) {
            die(header("Location: /projectmanager/errors/?id=R-PT-B"));
        }
        if(!$stmt->execute()) {
            die(header("Location: /projectmanager/errors/?id=R-PT-E"));
        } else{
            $stmt->close();
        }
        echo "<script>alert('Report sended, thank you!');</script>";
    }
?>

<html lang="en">
    <head>
        <title>Report</title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-R"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-R"));
            }
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    die(header("Location: /projectmanager/errors/?id=CI-BAR-R"));
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-12 page-title">
                            Report bug/problem
                        </div> 
                    </div>
                    <hr class='w-100'>
                </div>

                <div class="row d-flex justify-content-center">
                    <div class='col-lg-12 col-xl-8 text-light'>
                        <div class='col-12 bg-dark' style='border-radius: 5px;'>
                            <div class='row project-border-bottom'>
                                <div class="col-12 project-title task-margin-tb-10">
                                    Create new project
                                </div>
                            </div>
                            
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-12 task-margin-tb-10">
                                        <span style="font-weight: 600">Error code (only if you have one):</span>
                                        <div class="form-group">
                                            <input type='text' class='form-control' name='reportCode' value='<?php if((isset($tempCode) && !empty($desERR)) && $tempCode != null){ echo $tempCode; } ?>' autocomplete='off'/>
                                        </div>
                                        <span style="font-weight: 600">Description (tell us what happened): *</span>
                                        <div class="form-group">
                                            <?php
                                                if(!empty($desERR)){
                                                    echo "<textarea class='form-control is-invalid' name='reportDes' rows='5'>$tempDes</textarea>
                                                    <div class='invalid-feedback'>
                                                        $desERR
                                                    </div>";
                                                } else {
                                                    echo "<textarea class='form-control' name='reportDes'  rows='5'></textarea>";
                                                }

                                            ?>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row project-border-top">
                                    <div class="col-12 task-margin-tb-10">
                                        <input type="submit" class="btn btn-light edit-DIV-InputTitle" name="reportBTN" value="Send report"/>
                                    </div>
                                </div>
                            </form>

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