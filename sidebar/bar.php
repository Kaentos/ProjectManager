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
                <img class="img-responsive img-rounded" style="height: auto !important;" src="/projectmanager/img/UIMG/<?php echo $UserData["id"] ?>.png"
                 alt="User picture">
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

        <!-- Sidebar Project / now search -->
        <div class="sidebar-search">
            <div>
                <div class="input-group">
                    <input type="text" class="form-control search-menu" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                    </div>
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

                            <!-- Menu Dashboard -->
                            <li class="sidebar-dropdown">
                                <a href="#">
                                    <i class="fa fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                                <div class="sidebar-submenu">
                                    <ul>
                                        <li>
                                            <a href="#">Dashboard 1</a>
                                        </li>
                                        <li>
                                            <a href="#">Dashboard 2</a>
                                        </li>
                                        <li>
                                            <a href="#">Dashboard 3</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- End Menu Dashboard -->

                            <!-- Menu E-Commerce -->
                            <li class="sidebar-dropdown">
                                <a href="#">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>E-commerce</span>
                                </a>
                                <div class="sidebar-submenu">
                                    <ul>
                                        <li>
                                            <a href="#">Products</a>
                                        </li>
                                        <li>
                                            <a href="#">Orders</a>
                                        </li>
                                        <li>
                                            <a href="#">Credit cart</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- End Menu E-Commerce -->

                            <!-- Menu Components -->
                            <li class="sidebar-dropdown">
                                <a href="#">
                                    <i class="far fa-gem"></i>
                                    <span>Components</span>
                                </a>
                                <div class="sidebar-submenu">
                                    <ul>
                                        <li>
                                        <a href="#">General</a>
                                        </li>
                                        <li>
                                        <a href="#">Panels</a>
                                        </li>
                                        <li>
                                        <a href="#">Tables</a>
                                        </li>
                                        <li>
                                        <a href="#">Icons</a>
                                        </li>
                                        <li>
                                        <a href="#">Forms</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- End Menu Components -->

                            <!-- Menu Example -->
                            <li class="sidebar-dropdown">
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
                            </li>
                            <!-- End Menu Example -->

                            <li class="header-menu">
                                <span>Extra</span>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-book"></i>
                                    <span>Documentation</span>
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
                                        <a href='#'>
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
            <i class="fa fa-cog"></i>
        </a>
        <a href="/projectmanager/logout.php" style="padding-top:5px">
            <i class="fa fa-power-off"></i>
        </a>
    </div>
</nav>