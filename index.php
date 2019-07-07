<?php
    session_start();
    if ( isset( $_SESSION['user'] ) ) {
        header("Location: /projectmanager/dashboard/");
    } else  {
        error_reporting(E_ERROR | E_PARSE); // Removes php errors and warnings (avoid showing erros to user)
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            header("Location: /projectmanager/errors/?id=FIMI-OF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/login&register.php"){
            sendError("FIMI-LRF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            sendError("FIMI-DBF");
        }
        
        $conn = ConnectRoot();
    }

    // Vars that contain errors info to show
    $LoginUserErr = $REmailErr = $RUsernameErr = $RPasswordErr = $RCPasswordErr = $RQuestionErr = $RAnswerErr = "";
    $RError = false;
    
    // Login Button
    if(isset($_POST['LoginBtn'])) {
        $LoginData = Login($conn);
        if ($LoginData != false){
            $LoginData += ["lastActivity" => time()];
            $_SESSION['user'] = $LoginData;
            header("Location: dashboard/");
        }
    }

    //Register Button
    if(isset($_POST['RegisterBtn'])) { 
        AddUser($conn);
    }

?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title>Welcome to Project Manager</title>
        <meta name="description" content="Digital Couch">
        <meta name="author" content="Kaentos">
        <link rel="icon" href="img/icon.png">
        
        <!-- CSS -->
        <link rel="stylesheet" href="/projectmanager/css/Custom.css" type="text/css">
        <link rel="stylesheet" href="/projectmanager/css/bootstrap.min.css">
    </head>
    <body class="background_color">    
        <main>
            <div class="register">
                <div class="row">
                    <div class="col-md-3 register-left">
                        <img src="img/icon.png" alt=""/>
                        <h3>Welcome to Project Manager</h3>
                        <p>Manage your projects!</p>
                    </div>
                    <div class="col-md-9 register-right">
                        <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?php if (!$RError){ echo "active"; } ?>" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if ($RError){ echo "active"; } ?>" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Register</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            
                            <!-- Login Form -->
                            <div class="tab-pane fade show <?php if (!$RError){ echo "active"; } ?>" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <h3 class="register-heading">Login</h3>
                                    <div class="row register-form">
                                        <div class="col-md-6">
                                            Username or email address *
                                            <div class="form-group">
                                                <input type="text" class="form-control <?php if(!$RError && !empty($LoginUserErr)) echo "is-invalid" ?>" name="LUserEmail" autofocus value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($LoginUserErr!="") echo $LoginUserErr; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            Password *
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="LPassword" value="" autocomplete="off" />
                                            </div>  
                                            <input type="submit" class="btnRegister" name="LoginBtn" value="Login"/>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Register Form -->
                            <div class="tab-pane fade show <?php if ($RError){ echo "active"; } ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <h3  class="register-heading">Register</h3>
                                    <div class="row register-form">
                                        <div class="col-md-6">
                                            Email *
                                            <div class="form-group">
                                                <input type="email" class="form-control <?php if($RError && !empty($REmailErr)) echo "is-invalid" ?>" name="REmail" placeholder="example@mail.com" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($REmailErr!="") echo $REmailErr; ?>
                                                </div>
                                            </div>
                                            Username *
                                            <div class="form-group">   
                                                <input type="text" class="form-control <?php if($RError && !empty($RUsernameErr)) echo "is-invalid" ?>" name="RUsername" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RUsernameErr!="") echo $RUsernameErr; ?>
                                                </div>
                                            </div>
                                            Password *
                                            <div class="form-group">
                                                <input type="password" class="form-control <?php if($RError && !empty($RPasswordErr)) echo "is-invalid" ?>" name="RPassword" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RPasswordErr!="") echo $RPasswordErr; ?>
                                                </div>
                                            </div>
                                            Confirm Password *
                                            <div class="form-group">
                                                <input type="password" class="form-control <?php if($RError && !empty($RCPasswordErr)) echo "is-invalid" ?>" name="RCPassword" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RCPasswordErr!="") echo $RCPasswordErr; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            Security Question *
                                            <div class="form-group">
                                                <input type="text" class="form-control <?php if($RError && !empty($RQuestionErr)) echo "is-invalid" ?>" name="RQuestion" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RQuestionErr!="") echo $RQuestionErr; ?>
                                                </div>
                                            </div>
                                            Security Answer *
                                            <div class="form-group">
                                                <input type="text" class="form-control <?php if($RError && !empty($RAnswerErr)) echo "is-invalid" ?>" name="RAnswer" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RAnswerErr!="") echo $RAnswerErr; ?>
                                                </div>
                                            </div>
                                            Country
                                            <div class="form-group">
                                                <select class="form-control" name="Rcountry">
                                                    <option class="hidden" value="null" selected disabled>Please select your country</option>
                                                    <?php
                                                        $sql = mysqli_query($conn, "SELECT id, name FROM countries");
                                                        while ($row = $sql->fetch_assoc()){
                                                            echo "<option value='". $row['id'] ."'>" . $row['name'] . "</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <input type="submit" class="btnRegister" name="RegisterBtn" value="Register"/>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
        </main>

        <!-- JS/Jquery Import -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>    
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>