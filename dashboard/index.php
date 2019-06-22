<?php

?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title>Home</title>
        <meta name="description" content="Project Manager">
        <meta name="author" content="Miguel Magueijo">
        <link rel="icon" href="img/icon.png">
            
        <!-- Can't remove / Icons -->
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet"> 

        <!-- CSS -->
        <link rel="stylesheet" href="/projectmanager/css/db.css">
        <link rel="stylesheet" href="/projectmanager/css/Custom.css">
        <link rel="stylesheet" href="/projectmanager/css/bootstrap.min.css">
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
                <i class="fas fa-bars"></i>
            </a>
            <nav id="sidebar" class="sidebar-wrapper">
                <div class="sidebar-content">

                    <!-- Sidebar Title -->
                    <div class="sidebar-brand">
                        <a href="/projectmanager/dashboard">pro sidebar</a>
                        <div id="close-sidebar">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>

                    <!-- Sidebar User -->
                    <div class="sidebar-header">
                        <div class="user-pic">
                            <img class="img-responsive img-rounded" style="height: auto !important;" src="https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg"
                                alt="User picture">
                        </div>
                        <div class="user-info">
                            <span class="user-name">Jhon
                                <strong>Smith</strong>
                            </span>
                            <span class="user-role">Administrator</span>
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

                        </ul>
                    </div>

                </div>
                
                <div class="sidebar-footer">
                    <a href="#">
                        <i class="fa fa-bell"></i>
                        <span class="badge badge-pill badge-warning notification">3</span>
                    </a>
                    <a href="#">
                        <i class="fa fa-envelope"></i>
                        <span class="badge badge-pill badge-success notification">7</span>
                    </a>
                    <a href="#">
                        <i class="fa fa-cog"></i>
                        <span class="badge-sonar"></span>
                    </a>
                    <a href="#">
                        <i class="fa fa-power-off"></i>
                    </a>
                </div>
            </nav>


            <main class="page-content">
                <div class="container-fluid">
                    <h2>Pro Sidebar</h2>
                    <hr>
                        <div class="row">
                            <div class="form-group col-md-12">
                            <p>This is a responsive sidebar template with dropdown menu based on bootstrap 4 framework.</p>
                            <p> You can find the complete code on <a href="https://github.com/azouaoui-med/pro-sidebar-template" target="_blank">
                                Github</a>, it contains more themes and background image option</p>
                            </div>
                            <div class="form-group col-md-12">
                            <iframe src="https://ghbtns.com/github-btn.html?user=azouaoui-med&repo=pro-sidebar-template&type=star&count=true&size=large"
                                frameborder="0" scrolling="0" width="140px" height="30px"></iframe>
                            <iframe src="https://ghbtns.com/github-btn.html?user=azouaoui-med&repo=pro-sidebar-template&type=fork&count=true&size=large"
                                frameborder="0" scrolling="0" width="140px" height="30px"></iframe>
                            </div>
                        </div>
                        <h5>More templates</h5>
                        <hr>
                            <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">          
                            <div class="card rounded-0 p-0 shadow-sm">
                                <img src="https://user-images.githubusercontent.com/25878302/58369568-a49b2480-7efc-11e9-9ca9-2be44afacda1.png" class="card-img-top rounded-0" alt="Angular pro sidebar">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Angular Pro Sidebar</h6>
                                    <a href="https://github.com/azouaoui-med/angular-pro-sidebar" target="_blank" class="btn btn-primary btn-sm">Github</a>
                                    <a href="https://azouaoui-med.github.io/angular-pro-sidebar/demo/" target="_blank" class="btn btn-success btn-sm">Preview</a>
                                </div>
                            </div>          
                            </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">          
                            <div class="card rounded-0 p-0 shadow-sm">
                                <img src="https://user-images.githubusercontent.com/25878302/58369258-33f20900-7ef8-11e9-8ff3-b277cb7ed7b4.PNG" class="card-img-top rounded-0" alt="Angular pro sidebar">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Angular Dashboard</h6>
                                    <a href="https://github.com/azouaoui-med/lightning-admin-angular" target="_blank" class="btn btn-primary btn-sm">Github</a>
                                    <a href="https://azouaoui-med.github.io/lightning-admin-angular/demo/" target="_blank" class="btn btn-success btn-sm">Preview</a>
                                </div>
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