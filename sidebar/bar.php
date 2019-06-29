<a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
</a>
<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
    
        <!-- Sidebar Title -->
        <div class="sidebar-brand">
            <a href="/projectmanager/dashboard">Project Manager</a>
            <div id="close-sidebar">
                <i class="fas fa-times"></i>
            </div>
        </div>

        <!-- Sidebar User -->
        <div class="sidebar-header">
            <div class="user-pic">
                <!-- <img class="img-responsive img-rounded" style="height: auto !important;" src="/projectmanager/img/UIMG/<?php echo $UserData["id"] ?>.png"
                 alt="User picture"> -->
                 <img class="img-responsive img-rounded" style="height: auto !important;" src="/projectmanager/img/UIMG/8.png" alt="User picture">
            </div>
            <div class="user-info">
                <span class="user-name">
                    <?php
                        echo "<strong>".$UserData["username"]."</strong>";
                    ?>
                </span>
                <?php
                    if ($UserData["role"] == 20){
                        echo "<span class='user-role'>Administrator</span>";
                    } else {
                        echo "<span class='user-role'>Member</span>";
                    }
                ?>
            </div>
        </div>
        
        <?php
            if(isset($_POST["Sproject"])){
                if ($_POST["Sproject"] == "new"){
                    header("Location: /projectmanager/dashboard/newproject.php");
                } elseif ($_POST["Sproject"] == "join") {
                    header("Location: /projectmanager/invite");
                } elseif (is_numeric($_POST["Sproject"])) {
                    header("Location: /projectmanager/project/?id=$_POST[Sproject]");
                }
            }
            if(isset($_GET["id"])){
                $barProjectID = $_GET["id"];
            }
        ?>
        <!-- Sidebar Project / now search -->
        <div class="sidebar-search">
            <div>
                <div class="input-group">
                    <form method="POST" class="col-md-12" style="margin-bottom:0px">
                        <select name="Sproject" class="form-control" style="background: #3a3f48; color:white; border: none" onchange="this.form.submit()">
                        <?php
                            if (!isset($barProjectID)){
                                echo "<option value='null' disabled selected> Select project </option>";
                            }
                            $query = "SELECT p.* FROM user AS u JOIN projectmembers as pm ON u.id = pm.idUser JOIN projects as p ON pm.idProject = p.id WHERE u.id=".$_SESSION['user']['id'].";";
                            if ($result = $conn->query($query)) {
                                if ($result->num_rows == 0) {
                                    echo "<option value='new'> Create new project </option>";
                                    echo "<option value='join'> Join project </option>";
                                } else {
                                    while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                                        if(isset($barProjectID) && $row["id"] == $barProjectID){
                                            echo "<option value='null' disabled selected> $row[name] </option>";
                                        } else {
                                            echo "<option value='$row[id]'>$row[name]</option>";
                                        }
                                    }
                                }
                                $result->close();
                            } else {
                                printf("Error in select user query");
                                die();
                            }
                        ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

                    <!-- Menu -->
                    <div class="sidebar-menu">
                        <ul>
                            <!-- Menu Title -->
                            <li class="header-menu">
                                <span>General</span>
                            </li>

                            <!-- Menu Projects -->
                            <li>
                                <a href="/projectmanager/dashboard/projects.php">
                                    <!-- <i class="fas fa-folder"></i> -->
                                    <i class="fas fa-project-diagram"></i>
                                    <span>Projects</span>
                                </a>
                            </li>
                            <!-- End Menu Projects -->

                            <!-- Menu Tasks -->
                            <li>
                                <a href="/projectmanager/dashboard/tasks.php">
                                    <i class="fa fa-tasks"></i>
                                    <span>Tasks</span>
                                </a>
                            </li>
                            <!-- End Menu Projects -->

                            <!-- Menu Calendar -->
                            <li>
                                <a href="/projectmanager/dashboard/calendar.php">
                                    <i class="fa fa-calendar"></i>
                                    <span>Calendar</span>
                                </a>
                            </li>
                            <!-- End Menu Calendar -->

                            <!-- Menu Calendar -->
                            <li>
                                <a href="/projectmanager/dashboard/calendar.php">
                                    <i class="fas fa-bug"></i>
                                    <span>Issues</span>
                                </a>
                            </li>
                            <!-- End Menu Calendar -->

                            <!-- Menu Example -->
                            <!-- <li class="sidebar-dropdown">
                                <a href="#">
                                    <i class="fa fa-globe"></i>
                                    <span>Example</span>
                                </a>
                                <div class="sidebar-submenu">
                                    <ul>
                                        <li>
                                        <a href="#">Option 1</a>
                                        </li>
                                        <li>
                                        <a href="#">Option 2</a>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
                            <!-- End Menu Example -->

                            <li class="header-menu">
                                <span>Extra</span>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fas fa-stopwatch"></i>
                                    <span>Timer</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-calendar"></i>
                                    <span>Calendar</span>
                                </a>
                            </li>
                            <!-- Example -->
                            <li>
                                <a href="#">
                                    <i class="fa fa-folder"></i>
                                    <span>Examples</span>
                                </a>
                            </li>
                            <!-- Example -->
                            <?php 
                                if($UserData["role"] == 20){
                                    echo "
                                    <li class='header-menu'>
                                        <span>Admin Tools</span>
                                    </li>
                                    <li class='sidebar-dropdown'>
                                        <a class='pointer-mouse'>
                                            <i class='fa fa-tachometer-alt'></i>
                                            <span>Dashboard</span>
                                        </a>
                                        <div class='sidebar-submenu'>
                                            <ul>
                                                <li>
                                                    <a href='#'>Dashboard 1</a>
                                                </li>
                                                <li>
                                                    <a href='#'>Dashboard 2</a>
                                                </li>
                                                <li>
                                                    <a href='#'>Dashboard 3</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    ";
                                }
                            ?>

                        </ul>
                    </div>

                </div>
                
    <div class="sidebar-footer">
        <a href="/projectmanager/user/" style="padding-top:5px">
            <i class="fas fa-user-cog"></i>
        </a>
        <a href="/projectmanager/logout.php" style="padding-top:5px">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</nav>