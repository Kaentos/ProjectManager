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

    $ProjectData = array();
    $hasProjects = false;

    $query = "SELECT p.*, s.name as Sname, s.badge as Sbadge, u.username, pm.idRole AS Role FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE pm.idUser =$UserData[id] ORDER BY p.creationDate DESC LIMIT 25";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasProjects = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($ProjectData, $row);
            }
        } else {
            $hasProjects = false;
        }
        $result->close();
    } else {
        sendError("GPD-MPP");
    }

    if(isset($_POST["searchBTN"]) && $hasProjects){
        if(isset($_POST["searchProject"])){
            $nameFilter = $_POST["searchProject"];
            $vname = $nameFilter;
            $nameFilter = "%".$nameFilter."%";
        } else {
            $nameFilter = "%%";
        }
        if ($temp = searchProject($conn, $nameFilter, $UserData)){
            $ProjectData = $temp;
        } else {
            $filterHasProjects = true;
        }
    }

    function searchProject($conn, $nameFilter, $UserData){
        if(!$stmt = $conn->prepare("SELECT p.*, s.name as Sname, s.badge as Sbadge, u.username, pm.idRole AS Role FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE pm.idUser =$UserData[id] AND p.name LIKE ? ORDER BY p.creationDate DESC LIMIT 25")) {
            sendError("MPP-PT-P");
        }
        if(!$stmt->bind_param("s", $nameFilter)) {
            sendError("MPP-PT-B");
        }
        if(!$stmt->execute()) {
            sendError("MPP-PT-E");
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
            sendError("MPP-PT-GR");
        }
        if (isset($Data)){
            return $Data;
        } else {
            return false;
        }
    }

    // Join new project
    if (isset($_POST["joinProjectBTN"])){
        if(isset($_POST["inviteCode"]) && strlen($_POST["inviteCode"]) == 12){
            $joinCode = $_POST["inviteCode"];
            unset($codeInvalid);
        } else {
            $codeInvalid = "Input a valid code! Must be 12 characters";
            activateModal("joinProjectModal");
        }

        if (isset($joinCode)){
            if ($projectID = checkCode($conn, $joinCode)){
                if(!checkUserInProject($conn, $projectID, $UserData["id"])){
                    addUserToProject($conn, $projectID, $UserData["id"]);
                } else {
                    $codeInvalid = "You are already in this project!";
                    activateModal("joinProjectModal");
                }
            } else {
                $codeInvalid = "Invalid code. Try a new one!";
                activateModal("joinProjectModal");
            }
        }
        
    }
?>

<html lang="en">
    <head>
        <title>Projects</title>
        <?php
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html";
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html";
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    sendError("MPB-MPP");
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                            <div class="col-12 col-xl-4">
                                <span style="font-size:2rem; font-weight: 500;">All projects</span>
                            </div>
                            <form method="POST" action="" class="col-12 col-xl-8" style="text-align:right">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by project name" name="searchProject">
                                    <div class="input-group-append">
                                        <button type="submit" name="searchBTN" class="btn  btn-dark">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="#joinProjectModal" data-toggle='modal' class="btn btn-dark">Join Project</a>
                                        <a href="newproject.php" class="btn btn-dark">New Project</a>
                                    </div>
                                </div>
                            </form>    
                        </div>
                        <hr class='w-100'>
                        <?php
                            if($hasProjects && (!isset($filterHasProjects) || !$filterHasProjects)){
                                foreach($ProjectData as $Project){
                                    if ($Project["Role"] < 3){
                                        $code = $Project["code"];
                                    }
                                    $dateTimeStamp = strtotime($Project["creationDate"]);
                                    $Project["creationDate"] = date('d-m-Y', $dateTimeStamp);
                                    $dateTimeStamp = strtotime($Project["lastupdatedDate"]);
                                    $Project["lastupdatedDate"] = date('d-m-Y', $dateTimeStamp);
    
                                    echo "
                                    <div class='col-12 col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 text-light'>
                                        <div class='col-12 bg-dark' style='border-radius: 5px;'>
                                            <div class='row project-border-bottom'>
                                                <div class='col-12' style='margin-top: 10px; margin-bottom: 10px'>
                                                    <a href='/projectmanager/project/?id=$Project[id]' class='project-title'>
                                                       $Project[name]
                                                    </a>
                                                </div>
                                            </div>
    
                                            <div class='row project-text' style='margin-top: 10px;'>
                                                <div class='col-12' style='margin-bottom: 10px; word-break: break-all;'>
                                                    $Project[des]
                                                </div>
                                                <div class='col-md-12 col-xl-6' style='margin-top: 10px'>
                                                    Status: ";
                                    if ($Project["Sbadge"] == "dark"){
                                        echo "<span class='badge badge-$Project[Sbadge] custom-badge-border'>$Project[Sname]</span>";
                                    } else {
                                        echo "<span class='badge badge-$Project[Sbadge]'>$Project[Sname]</span>";
                                    }
                                    echo "
                                                    <br>
                                                    Updated: <span class='badge badge-light'>$Project[lastupdatedDate]</span>
                                                </div>
                                                <div class='col-md-12 col-xl-6' style='margin-top: 5px'>
                                                    Created: <span class='badge badge-light'>$Project[creationDate]</span>
                                                    <br>";
                                    if (isset($code)){
                                        echo "Code: <span class='badge badge-light'>$code</span>";
                                    }
                                    echo "    
                                                </div>
                                            </div>
                                            <div class='row project-border-top' style='padding: 10px; margin-top: 10px'>
                                                <div class='col-12 text-center'>
                                                    <a href='/projectmanager/project/tasks/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                        <i class='fas fa-tasks'></i>
                                                    </a>
                                                    <a href='/projectmanager/project/issues/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                        <i class='fas fa-bug'></i>
                                                    </a>
                                                    <a href='/projectmanager/project/members/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                        <i class='fas fa-users'></i>
                                                    </a>
                                                    <a href='#' class='btn btn-light' style='margin: 5px'>
                                                        <i class='fas fa-flag'></i>
                                                    </a>
                                                    <a href='#' class='btn btn-light' style='margin: 5px'>
                                                        <i class='fas fa-comments'></i>
                                                    </a>";
                                    if ($Project["Role"] < 3){
                                        echo "
                                            <a href='/projectmanager/project/edit?id=$Project[id]' class='btn btn-primary' style='margin: 5px'>
                                                <i class='fas fa-cog'></i>
                                            </a>";
                                    }            
                                    echo "
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    ";
                                    if (isset($code)){
                                        unset($code);
                                    }
                                }
                            } elseif (isset($filterHasProjects) && $filterHasProjects) {
                                echo "
                                    <div class='col-12'>
                                        <div class='text-center'>
                                            <h4>No projects found for $vname</h4>
                                        </div>
                                    </div>";
                            } else {
                                echo "<div class='col-12'><h4>No projects found, what about creating or joining a new one?</h4></div>";
                            }
                        ?>
                    </div>

                    <!-- Join project modal -->
                    <div class="modal fade" id="joinProjectModal" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Head -->
                                <div class="modal-header">
                                    <span class="modal-title"> Join another project </span>
                                    <button type="button" class="close" data-dismiss="modal" aria-label=""><span>×</span></button>
                                </div>        
                                <!-- Body -->
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <span class="modal-subtitle">Invide code:</span>
                                        <input type='text' class='form-control edit-DIV-Input <?php if(isset($codeInvalid)) { echo "is-invalid";} ?>' name='inviteCode' autocomplete='off'/>
                                        <div class='invalid-feedback'>
                                            <?php
                                                if (isset($codeInvalid)){
                                                    echo $codeInvalid;
                                                }
                                            ?>
                                        </div>
                                        <br>
                                        <input type="submit" class="btn btn-dark font-weight-bold" name="joinProjectBTN" value="Join!">
                                    </form>                
                                </div>
                                        
                            </div>
                        </div>
                    </div> 
                    <!-- END task modal -->

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