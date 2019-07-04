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
            $membersData = getAllProjectMembers($conn, $projectID);
            if(!isset($membersData)){
                die("MMP");
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    if (!$UserRole = checkUserInProject($conn, $projectID, $UserData["id"])){
        header("location: /projectmanager/dashboard/projects");
    }

    // Members Data
    function getAllProjectMembers($conn, $projectID){
        $membersData = array();
        $query = "SELECT u.id AS userID, u.username, r.* FROM projects AS p INNER JOIN projectmembers AS pm ON p.id=pm.idProject INNER JOIN proles AS r ON pm.idRole = r.id INNER JOIN user AS u ON pm.idUser=u.id WHERE p.id=$projectID ORDER BY r.id LIMIT 25";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($membersData, $row);
                }
            } elseif ($result->num_rows == 0) {
                die("Error MPM");
            } else {
                die();
            }
        } else {
            die();
        }
        return $membersData;
    }

    $orderDic = [
        "name" => "ORDER BY u.username",
        "role" => "ORDER BY r.id"
    ];
    $filer1Selected = $filer2Selected = false;
    $noMembersFound = false;

    // Apply filters
    if (isset($_POST["searchBTN"])){

        // Filter name
        if(isset($_POST["usernameSearch"])){
            $Susername = $_POST["usernameSearch"];
            $Susername = "%".$Susername."%";
            $filterName = "AND u.username LIKE ?";
        }

        // Filter order
        if(isset($_POST["filter"])){
            if(array_key_exists($_POST["filter"], $orderDic)){
                $filterORDER = $orderDic["$_POST[filter]"];
                $filer1Selected = $_POST["filter"];
            } else {
                $info =  "Invalid order filter value! If you didn\'t change anything report with TFV!";
                showAlert($info);
                $filer1Selected = "role";
                $filterORDER = "ORDER BY r.id";
            }
        } else {
            $filer1Selected = "role";
            $filterORDER = "ORDER BY r.id";
        }

        // Filter roles
        if(isset($_POST["filterRoles"])){
            if(is_numeric($_POST["filterRoles"]) && checkTaskStatusID($conn, $_POST["filterRoles"])){
                $filterRolesID = "AND r.id=". $_POST["filterRoles"];
                $filer2Selected = $_POST["filterRoles"];
            } elseif ($_POST["filterRoles"] == -1) {
                $filterRolesID = "";
                $filer2Selected = false;
            } else {
                $info = "Invalid status filter value! If you didn\'t change anything report with TFS!";
                showAlert($info);
                $filer2Selected = false;
                $filterRolesID = "";
            }
        } else {
            $filer2Selected = false;
            $filterRolesID = "";
        }

        if ($filterOK = MemberFilter($conn, $projectID, $filterName, $filterORDER, $filterRolesID, $Susername)){
            $membersData = $filterOK;
        }
    }

    function MemberFilter($conn, $projectID, $NAME, $ORDER, $GROUP, $Susername){
        if(!($stmt = $conn->prepare("SELECT u.id AS userID, u.username, r.* FROM projects AS p INNER JOIN projectmembers AS pm ON p.id=pm.idProject INNER JOIN proles AS r ON pm.idRole = r.id INNER JOIN user AS u ON pm.idUser=u.id WHERE p.id=$projectID $NAME $GROUP $ORDER LIMIT 25"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        if(!$stmt->bind_param("s", $Susername)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows > 0){
                $membersData = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($membersData, $row); 
                }
                $stmt->close();
            } else {
                $GLOBALS["noMembersFound"] = true;
            }
        } else {
            return false;
        }
        if (isset($membersData)){
            return $membersData;
        } else {
            return false;
        }
        
    }

    // Removes user from project
    if(isset($_POST["REMuserFromProjectBTN"])){
        if(isset($_POST["memberID"]) && is_numeric($_POST["memberID"])){
            if($Temp = checkUserInProject($conn, $projectID, $_POST["memberID"])){
                $memberID = $_POST["memberID"];
                removeUserFromProject($conn, $memberID, $projectID);
            }   
        }
    }

    $AllProjectUserRoles = getProjectUserRoles($conn);
?>

<html lang="en">
    <head>
    <title><?php echo "$projectData[name] - Tasks"; ?></title>
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
                            <!-- Username filter -->
                            <div class="col-md-12 col-lg-4 filter-DIV-text">
                                <form method="POST" action="">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search by username" name="usernameSearch">
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
                                            <option <?php if($filer1Selected == "role"){ echo "selected"; } ?> value="role"> Role </option>
                                        </select>
                                        &nbsp
                                        <select name="filterRoles" class="form-control" style="background: #3a3f48; color:white; border: none">
                                            <option value="-1" <?php if(!$filer2Selected){ echo "selected"; } ?>> All Roles </option>
                                            <?php
                                                foreach($AllProjectUserRoles as $role){
                                                    if ($filer2Selected == $role["id"]) {
                                                        echo "<option value='$role[id]' selected>$role[name]</option>";
                                                    } else {
                                                        echo "<option value='$role[id]'>$role[name]</option>";
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
                                            <a class='btn btn-dark float-right' data-toggle='modal' href='#inviteCodeModal' style='margin-bottom: 15px'>
                                                Invite code
                                            </a>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                        if(isset($membersData) && !$noMembersFound){
                            foreach($membersData as $member){
                                echo "
                                <div class='col-lg-6 col-xl-4 task-DIV'>
                                    <div class='btn-toolbar row' style='margin-top:15px'>
                                        <div class='col-lg-12' style='margin-top:5px;'>
                                            <form method='POST' action=''>
                                                <span class='task-DIV-title2 task-DIV-text'>
                                                    $member[username]
                                                    <span class='badge badge-$member[badge]'>$member[name]</span>";
                                
                                if($UserRole  < 3 && $member["id"] != 1) {
                                    echo "<button type='submit' name='REMuserFromProjectBTN' class='btn bg-dark text-white float-right'><i class='fas fa-times'></i></button>";
                                    echo "<button type='submit' name='EditUserRoleBTN' class='btn bg-dark text-white float-right'><i class='fas fa-pen'></i></button>";
                                }
                                
                                echo "

                                                    <input type='hidden' name='memberID' value='$member[userID]'>
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                ";
                            }
                        } elseif ($noMembersFound) {
                            echo "<p class='task-DIV-list'> Didn't find any members. </p>";
                        }
                    ?>
                    <!-- END Task -->
                        
                    </div>
                    
                </div>

                <!-- Invite code modal -->
                <div class="modal fade" id="inviteCodeModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Head -->
                            <div class="modal-header">
                                <span class="modal-title"> Project invite code: </span>
                                <button type="button" class="close" data-dismiss="modal" aria-label=""><span>×</span></button>
                            </div>        
                            <!-- Body -->
                            <div class="modal-body">
                                <span class="modal-subtitle">Project name:</span>
                                <div class='alert alert-secondary edit-DIV-Input'><?php echo $projectData["name"]; ?></div>
                                <span class="modal-subtitle">Code:</span>
                                <div class='alert alert-secondary edit-DIV-Input'><?php echo $projectData["code"]; ?></div>
                                <span class="modal-subtitle">Link:</span>
                                <div class='alert alert-secondary edit-DIV-Input'>http://localhost/projectmanager/invite/?code=<?php echo $projectData["code"]; ?></div>
                            </div>
                                    
                        </div>
                    </div>
                </div> 
                <!-- END invite code modal -->

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