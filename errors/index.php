<?php
    if (isset($_GET["id"])){
        $errorCode = $_GET["id"];
    } else {
        $errorCode = null;
    }
?>

<html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Project Manager - Error</title>
        <meta name="description" content="Digital Couch">
        <meta name="author" content="Kaentos">
        <link rel="icon" href="/projectmanager/img/icon.png">

        <!-- CSS -->
        <link rel="stylesheet" href="/projectmanager/css/Custom.css">
        <link rel="stylesheet" href="/projectmanager/css/bootstrap.min.css">
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
                        <div class="mb-4 lead">Page not found!</div>
                        <a href="/projectmanager/" class="btn btn-outline-danger" style="color:white">Report</a>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>