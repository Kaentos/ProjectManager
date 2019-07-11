<?php
    error_reporting(E_ERROR | E_PARSE);
    if (isset($_GET["id"])){
        $errorCode = $_GET["id"];
    } else {
        $errorCode = "Unknown error";
    }
?>

<html>

    <head>
        <title><?php echo $errorCode; ?></title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-ERROR"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-ERROR"));
            }
        ?>
        <style>
            .page-wrap {
                min-height: 100vh;
            }
        </style>
    </head>

    <body class="background_color" style="color: white;">
        <div class="page-wrap d-flex flex-row align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 text-center">
                        <span class="display-1 d-block"><?php echo $errorCode ?></span>
                        <div class="mb-4 lead">Please report this error as soon as possible!</div>
                        <a href="/projectmanager/report" class="btn btn-outline-danger" style="color:white">Report</a>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>