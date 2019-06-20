<?php
    session_start();
    if ( isset( $_SESSION['user'] ) ) {
        header("location: logged.php");
    }

    //include "serverConRegister.php";
    $dbHost = "localhost";
    $dbUser = "root";
    $dbPassword = "";
    $dbName = "pmanager";
    $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
    
    if ($conn->connect_errno) {
        printf("Connect failed: %s\n", $conn->connect_error);
        exit();
    }

    $LoginUserErr = $REmailErr = $RUsernameErr = $RPasswordErr = $RCPasswordErr = $RQuestionErr = $RAnswerErr = "";

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    // Login Stuff
    if(isset($_POST['LoginBtn'])) {
        $LoginData = Login($conn);
        if ($LoginData != false){
            //$_SESSION['user'] = $LoginData;
            //header("Refresh:0");
            print_r($LoginData);
        }
    }

    function Login($conn){
        $Temp = array();
        if ($_POST["LUserEmail"] == "" || $_POST["LPassword"] == ""){
            $GLOBALS["LoginUserErr"] = "Please enter your data.";
            return false;
        } else {
            $username = test_input($_POST["LUserEmail"]);
            $password = test_input($_POST["LPassword"]);
            if (strlen($username) > 16 || strlen($password) > 16){
                $GLOBALS["LoginUserErr"] = "Max input is 16 characters.";
                return false;
            }
            // $options = [
            //     'cost' => 12,
            // ];
            // $HashedPW = password_hash($password, PASSWORD_BCRYPT, $options);
        }

        $query = "SELECT * FROM  user WHERE ( username='$username' OR email = '$username')";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Temp += ["id" => $row["id"]];
                    $Temp += ["username" => $row["username"]];
                    $Temp += ["role" => $row["role"]];
                    print_r($Temp);
                } else {
                    printf("MAJOR ERROR CAN'T CONVERT USER ROW TO ARRAY");
                    return;
                }
            } else {
                $GLOBALS["LoginUserErr"] = "Invalid username or password!";
                return false;
            }
            $result->close();
        } else {
            printf("Error in select user query");
            return;
        }
        
        $query = "SELECT * FROM usersecurity WHERE idUser='$Temp[id]';";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    if (!password_verify($password, $row["password"])){
                        $GLOBALS["LoginUserErr"] = "Invalid username or password!";
                        return false;
                    }
                    echo "<br>";
                    print_r($row);
                    echo "<br>";
                } else {
                    printf("MAJOR ERROR CAN'T CONVERT USER ROW TO ARRAY");
                    return;
                }
            } else {
                $GLOBALS["LoginUserErr"] = "Invalid username or password!";
                return false;
            }
            $result->close();
        } else {
            printf("Error in select user query");
            return;
        }

        $LoginData = $Temp;
        return $LoginData;
    }
    //End of Login Stuff

    //Register Stuff
    if(isset($_POST['RegisterBtn'])) { 
        AddUser($conn);
    }

    function AddUser($conn){
        $teste = validateInput($conn);
        print_r($teste);
        // $NewUser = validateInput($conn);
        // if (!isset($NewUser["role"])){
        //     return;
        // }
        // $query = "INSERT INTO user (username, email, creationDate, idCountry, idRole) VALUES ('$NewUser[username]', '$NewUser[email]', '$NewUser[creationDate]', '$NewUser[countryID]', '$NewUser[role]');";
        // $result = mysqli_query($conn,$query);
        // if(!$result) {
        //     die("ADD in user Error:". mysqli_error($conn));
        // }
        // $user_id = mysqli_insert_id($conn);

        // $query = "INSERT INTO usersecurity (idUser, password, question, answer) VALUES ('$user_id', '$NewUser[password]', '$NewUser[question]', '$NewUser[answer]');";
        // $result = mysqli_query($conn,$query);
        // if(!$result) {
        //     die("ADD in security Error:". mysqli_error($conn));
        // }

        // $Session_data = array();
        // $Session_data += ["id" => $user_id];
        // $Session_data += ["username" => $NewUser["username"]];
        // $Session_data += ["role" => 1];
        // $_SESSION['USER'] = $Session_data;
        // unset($NewUser);
        // header("location: index.php");
    }

    function validateInput($conn){
        // Var declarations
        $Temp = array();
        $email = test_input($_POST["REmail"]);
        $username = test_input($_POST["RUsername"]);
        // $password = test_input($_POST["RPassword"]);;
        // $Cpassword = test_input($_POST["RCPassword"]);
        // $Squestion = test_input($_POST["RQuestion"]);
        // $Sanswer = test_input($_POST["RAnswer"]);
        // $country = $_POST["Rcountry"];

        // Email validation
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Check if email is already taken
            $query = "SELECT id FROM user WHERE email = '$email';";
            if ($result = $conn->query($query)) {
                if ($result->num_rows > 0){
                    $GLOBALS["REmailErr"] = "$email already taken.";
                    return;
                }
                $result->close();
            } else {
                printf("Error in select register-email query");
                return;
            }
        } else {
            $GLOBALS["REmailErr"] = "Incorrect type of email.";
            return;
        }
        $Temp += ["email" => $email];
        
        // Username validation
        if(preg_match('/^\w{6,16}$/', $username)) { // \w equals "[0-9A-Za-z_]"
            // Check if username is already taken
            $query = "SELECT id FROM user WHERE username = '$username';";
            if ($result = $conn->query($query)) {
                if ($result->num_rows > 0){
                    $GLOBALS["RUsernameErr"] = "$username already taken.";
                    return;
                }
                $result->close();
            } else {
                printf("Error in select register-username query");
                return;
            }
        } else{
            $GLOBALS["RUsernameErr"] = "Username must contain at least 6 characters and max 16 characters (spaces aren't allowed).";
            return;
        }
        $Temp += ["username" => $username];

        // // Password validation
        // if(!empty($pw) && ($pw == $pw2)) {
        //     if (strlen($pw) <= 6 || strlen($pw) > 16) {
        //         $GLOBALS["RPasswordErr"] = "Must contain at least 6 and max 16 characters.";
        //         return;
        //     }
        //     elseif(!preg_match("#[0-9]+#", $pw)) {
        //         $GLOBALS["RPasswordErr"] = "Must contain at least 1 number.";
        //         return;
        //     }
        //     elseif(!preg_match("#[A-Z]+#", $pw)) {
        //         $GLOBALS["RPasswordErr"] = "Must contain at least 1 capital letter.";
        //         return;
        //     }
        //     elseif(!preg_match("#[a-z]+#", $pw)) {
        //         $GLOBALS["RPasswordErr"] = "Must contain at least 1 lowercase letter.";
        //         return;
        //     }
        // }
        // elseif(!empty($_POST["password"])) {
        //     $GLOBALS["RCPasswordErr"] = "Confirm password invalid.";
        //     return;
        // } else {
        //     $GLOBALS["RPasswordErr"] = "Empty password input.";
        //     return;
        // }
        // $Validated += ["password" => md5($pw)];

        // // Question validation
        // if(strlen($Squestion) <= 6 || strlen($Squestion) > 30) {
        //     $GLOBALS["RQuestionErr"] = "Must have at least min of 6 and max 30 letters.";
        //     echo $Squestion;
        //     return;
        // }
        // $Validated += ["question" => $Squestion];

        // // Answer validation
        // if(!preg_match('/^\w{6,16}$/', $Sanswer)) { // \w equals "[0-9A-Za-z_]"
        //     $GLOBALS["RAnswerErr"] = "Must have min 6 and max 16 letters/numbers (spaces not included).";
        //     return;
        // }
        // $Validated += ["answer" => md5($Sanswer)];

        // $result = mysqli_query($conn, "SELECT * FROM countries WHERE id = '$country';");
        // if(mysqli_num_rows($result) > 0) {
        //     $Validated += ["countryID" => $country];
        // } else {
        //     $Validated += ["countryID" => null];
        // }
        
        
        // // Adds current date and associate member role
        // $Validated += ["creationDate" => date("Y-m-d")];
        // $Validated += ["role" => 1];
        // return $Validated;
        return $Temp;
    }
    //End of Register Stuff

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
        <link rel="stylesheet" href="css/Custom.css" type="text/css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <style>
            .customPaddingLeft{
                padding-left:5%;
            }
            .customPaddingRight{
                padding-right:5%;
            }
            ::-webkit-scrollbar {
                display: none;
            }
            @media screen and (max-width: 990px) {
                .onlyText{
                    padding-left: 0rem; 
                    padding-right: 0.5rem
                }
                .customPaddingLeft{
                    padding-left: 5%;
                    padding-right: 5%;
                }
                .customPaddingRight{
                    padding-left: 5%;
                    padding-right: 5%;
                }
            }
            @media screen and (max-width: 1200px) {
                .customPaddingLeft{
                    padding-left: 5%;
                    padding-right: 5%;
                }
                .customPaddingRight{
                    padding-left: 5%;
                    padding-right: 5%;
                }
            }
        </style>
    </head>
    <body class="background_color">    
        <main>
            <div class="register">
                <div class="row">
                    <div class="col-md-3 register-left">
                        <img src="img/logofinal.png" alt=""/>
                        <h3>Welcome to Project Manager</h3>
                        <p>Manage your projects!</p>
                    </div>
                    <div class="col-md-9 register-right">
                        <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Register</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            
                            <!-- Login Form -->
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <h3 class="register-heading">Login</h3>
                                    <div class="row register-form">
                                        <div class="col-md-6">
                                            Username or email address
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="LUserEmail" autofocus value="" autocomplete="off" />
                                            </div>
                                            <?php if ($LoginUserErr!="") echo "<div class='alert alert-secondary' role='alert'> $LoginUserErr </div>" ?>
                                        </div>
                                        <div class="col-md-6">
                                            Password
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="LPassword" value="" autocomplete="off" />
                                            </div>  
                                            <input type="submit" class="btnRegister" name="LoginBtn" value="Login"/>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Register Form -->
                            <div class="tab-pane fade show" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <h3  class="register-heading">Register</h3>
                                    <div class="row register-form">
                                        <div class="col-md-6">
                                            Email
                                            <div class="form-group">
                                                <input type="email" class="form-control" name="REmail" placeholder="example@mail.com" value="" autocomplete="off" />
                                            </div>
                                            <?php if ($REmailErr!="") echo "<div class='alert alert-secondary' role='alert'> $REmailErr </div>" ?>
                                            Username
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="RUsername" value="" autocomplete="off" />
                                            </div>
                                            <?php if ($RUsernameErr!="") echo "<div class='alert alert-secondary' role='alert'> $RUsernameErr </div>" ?>
                                            Password
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="RPassword" value="" autocomplete="off" />
                                            </div>
                                            <?php if ($RPasswordErr!="") echo "<div class='alert alert-secondary' role='alert'> $RPasswordErr </div>" ?>
                                            Confirm Password
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="RCPassword" value="" autocomplete="off" />
                                            </div>
                                            <?php if ($RCPasswordErr!="") echo "<div class='alert alert-secondary' role='alert'> $RCPasswordErr </div>" ?>
                                        </div>
                                        <div class="col-md-6">
                                            Security Question
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="RQuestion" value="" autocomplete="off" />
                                            </div>
                                            <?php if ($RQuestionErr!="") echo "<div class='alert alert-secondary' role='alert'> $RQuestionErr </div>" ?>
                                            Security Answer
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="RAnswer" value="" autocomplete="off" />
                                            </div>
                                            <?php if ($RAnswerErr!="") echo "<div class='alert alert-secondary' role='alert'> $RAnswerErr </div>" ?>
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


            <div class="row">
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 customPaddingLeft">
                    <!-- Movies -->
                    <div class="clearfix">
                        <div class="col-md-12" style="background-color: white; text-align: center; border-radius: 10px;">
                            <h2 style="padding: 1%; color: #495057">
                                Last added movies
                            </h3>
                        </div>
                    </div>

                    <div class="row">
                        
                    </div>
                </div>

                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 customPaddingRight">
                    <!-- Series -->
                    <div class="clearfix">
                        <div class="col-md-12" style="background-color: white; text-align: center; border-radius: 10px;">
                            <h2 style="padding: 1%; color: #495057">
                                Last added series
                            </h3>
                        </div>
                    </div>

                    <div class="row">
                        
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