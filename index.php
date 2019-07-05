<?php
    session_start();
    if ( isset( $_SESSION['user'] ) ) {
        header("location: dashboard/");
    }
    
    include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php";
    include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php";
    $conn = ConnectRoot();

    $LoginUserErr = $REmailErr = $RUsernameErr = $RPasswordErr = $RCPasswordErr = $RQuestionErr = $RAnswerErr = "";
    $RError = false;
    
    // Login Stuff
    if(isset($_POST['LoginBtn'])) {
        $LoginData = Login($conn);
        if ($LoginData != false){
            $LoginData += ["lastActivity" => time()];
            $_SESSION['user'] = $LoginData;
            header("Location: dashboard/");
        }
    }

    function Login($conn){
        $Temp = array();
        if (empty($_POST["LUserEmail"]) || empty($_POST["LPassword"])){
            $GLOBALS["LoginUserErr"] = "Please enter your data.";
            return false;
        } else {
            $username = test_input($_POST["LUserEmail"]);
            $password = test_input($_POST["LPassword"]);
            if (strlen($username) > 16 || strlen($password) > 16){
                $GLOBALS["LoginUserErr"] = "Max input is 16 characters.";
                return false;
            }
        }

        if(!($stmt = $conn->prepare("SELECT * FROM  user WHERE ( username= ? OR email = ?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("ss", $username, $username)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Temp += ["id" => $row["id"]];
                    $Temp += ["username" => $row["username"]];
                    $Temp += ["role" => $row["role"]];
                } else {
                    die("Unexpected error");
                }
                $stmt->close();
            } else {
                if($result->num_rows == 0){
                    $GLOBALS["LoginUserErr"]="Invalid username or password";
                    return false;
                } else {
                    die("Unexpected error. Report error with code: U2.");
                }
            }
        } else {
            printf("Error in select user query");
            return false;
        }

        // Verify password
        if(!($stmt = $conn->prepare("SELECT * FROM  usersecurity WHERE idUser= ?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("i", $Temp["id"])) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    if (!password_verify($password, $row["password"])){
                        $GLOBALS["LoginUserErr"] = "Invalid username or password!";
                        return false;
                    }
                } else {
                    die("Unexpected error");
                }
                $stmt->close();
            } else {
                if($result->num_rows == 0){
                    $GLOBALS["LoginUserErr"]="Invalid username or password";
                    return false;
                } else {
                    die("Unexpected error. Report error with code: U2.");
                }
            }
        } else {
            printf("Error in select user query");
            return false;
        }

        $Temp;
        return $Temp;
    }
    //End of Login Stuff

    //Register Stuff
    if(isset($_POST['RegisterBtn'])) { 
        AddUser($conn);
    }

    function AddUser($conn){
        $NewUser = validateInput($conn);
        if (!isset($NewUser["role"])){
            return;
        }

        if(!($stmt = $conn->prepare("INSERT INTO user (email, username, creationDate, lastUpdateDate, idCountry, role) VALUES (?, ?, ?, ?, ?, ?);"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("ssssis", $NewUser["email"], $NewUser["username"], $NewUser["creationDate"], $NewUser["lastUpdatedDate"], $NewUser["countryID"], $NewUser["role"])) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            $user_id = $conn->insert_id;
        }

        if(!($stmt = $conn->prepare("INSERT INTO usersecurity (idUser, password, question, answer) VALUES (?, ?, ?, ?);"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("isss", $user_id, $NewUser["password"], $NewUser["question"], $NewUser["answer"])) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            if(!($stmt = $conn->prepare("DELETE FROM user where id=?;"))) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            if(!$stmt->bind_param("i", $user_id)) {
                die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute()){
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        $Session_data = array();
        $Session_data += ["id" => $user_id];
        $Session_data += ["username" => $NewUser["username"]];
        $Session_data += ["role" => $NewUser["role"]];
        $Session_data += ["lastActivity" => time()];
        $_SESSION['user'] = $Session_data;
        unset($NewUser);
        header("Location: dashboard/");
    }

    function validateInput($conn){
        // Var declarations
        $Temp = array();
        if (isset($_POST["REmail"]) && isset($_POST["RUsername"])){
            $email = test_input($_POST["REmail"]);
            $username = test_input($_POST["RUsername"]);
        } else {
            return;
        }
        if (isset($_POST["RPassword"]) && isset($_POST["RCPassword"])){
            $password = test_input($_POST["RPassword"]);;
            $Cpassword = test_input($_POST["RCPassword"]);
            $options = [
                'cost' => 12,
            ];
        } else {
            return;
        }
        if (isset($_POST["RQuestion"]) && isset($_POST["RAnswer"])){
            $Squestion = test_input($_POST["RQuestion"]);
            $Sanswer = test_input($_POST["RAnswer"]);
        } else {
            return;
        }
        if (isset($_POST["Rcountry"])){
            $country = $_POST["Rcountry"];
        } else {
            $country = null;
        }

        // Email validation
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Check if email is already taken
            if(!($stmt = $conn->prepare("SELECT id FROM user WHERE email = ?;"))) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            if(!$stmt->bind_param("s", $email)) {
                die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute()) {
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if ($result = $stmt->get_result()) {
                if ($result->num_rows == 1){
                    $GLOBALS["REmailErr"] = "$email already taken.";
                    $GLOBALS["RError"] = true;
                    return;
                } else {
                    if($result->num_rows > 1) {
                        die("Report error with the following code: UE2");
                    }
                }
                $stmt->close();
            } else {
                printf("Error in select user query");
                return false;
            }
        } else {
            $GLOBALS["REmailErr"] = "Incorrect type of email.";
            $GLOBALS["RError"] = true;
            return;
        }
        $GLOBALS["REmailErr"] = "";
        $Temp += ["email" => $email];
        
        // Username validation
        if(preg_match('/^\w{6,16}$/', $username)) { // \w equals "[0-9A-Za-z_]"
            // Check if username is already taken
            if(!($stmt = $conn->prepare("SELECT id FROM user WHERE username = ?;"))) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            if(!$stmt->bind_param("s", $username)) {
                die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute()) {
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if ($result = $stmt->get_result()) {
                if ($result->num_rows == 1){
                    $GLOBALS["RUsernameErr"] = "$username already taken.";
                    $GLOBALS["RError"] = true;
                    return;
                } else {
                    if($result->num_rows > 1) {
                        die("Report error with the following code: U2");
                    }
                }
                $stmt->close();
            } else {
                printf("Error in select user query");
                return false;
            }
        } else{
            $GLOBALS["RUsernameErr"] = "Username must contain at least 6 characters and max of 16. Spaces aren't allowed.";
            $GLOBALS["RError"] = true;
            return;
        }
        $GLOBALS["RUsernameErr"] = "";
        $Temp += ["username" => $username];

        // Password validation
        if(!empty($password)) {
            if (strlen($password) <= 6 || strlen($password) > 16) {
                $GLOBALS["RPasswordErr"] = "Must contain at least 6 and max 16 characters.";
                $GLOBALS["RError"] = true;
                return;
            }
            elseif(!preg_match("#[0-9]+#", $password)) {
                $GLOBALS["RPasswordErr"] = "Must contain at least 1 number.";
                $GLOBALS["RError"] = true;
                return;
            }
            elseif(!preg_match("#[A-Z]+#", $password)) {
                $GLOBALS["RPasswordErr"] = "Must contain at least 1 capital letter.";
                $GLOBALS["RError"] = true;
                return;
            }
            elseif(!preg_match("#[a-z]+#", $password)) {
                $GLOBALS["RPasswordErr"] = "Must contain at least 1 lowercase letter.";
                $GLOBALS["RError"] = true;
                return;
            }
            if ($Cpassword != $password){
                $GLOBALS["RCPasswordErr"] = "Password and confirm password don't match.";
                $GLOBALS["RError"] = true;
                return;
            }
        }
        elseif(!empty($Cpassword)) {
            $GLOBALS["RCPasswordErr"] = "Empty confirm password.";
            $GLOBALS["RError"] = true;
            return;
        } else {
            $GLOBALS["RPasswordErr"] = "Empty password input.";
            $GLOBALS["RError"] = true;
            return;
        }
        $GLOBALS["RPasswordErr"] = "";
        $GLOBALS["RCPasswordErr"] = "";
        $HashedPW = password_hash($password, PASSWORD_BCRYPT, $options);
        $Temp += ["password" => $HashedPW];

        // Question validation
        if(strlen($Squestion) <= 6 || strlen($Squestion) > 30) {
            $GLOBALS["RQuestionErr"] = "Question must contain at least 6 characters and max of 30.";
            $GLOBALS["RError"] = true;
            return;
        }
        $GLOBALS["RQuestionErr"] = "";
        $Temp += ["question" => $Squestion];

        // Answer validation
        if(!preg_match('/^\w{6,16}$/', $Sanswer)) { // \w equals "[0-9A-Za-z_]"
            $GLOBALS["RAnswerErr"] = "Answer must contain at least 6 characters and max of 16. Only letters and numbers, spaces and special characters aren't allowed.";
            $GLOBALS["RError"] = true;
            return;
        }
        $GLOBALS["RAnswerErr"] = "";
        $HashedAns = password_hash($Sanswer, PASSWORD_BCRYPT, $options);
        $Temp += ["answer" => $HashedAns];

        // Country validation
        if(!($stmt = $conn->prepare("SELECT * FROM countries WHERE id = ?;"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("i", $country)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows == 1){
                $Temp += ["countryID" => $country];
            } else {
                if($result->num_rows > 1) {
                    die("Report error with the following code: C2");
                } else {
                    $Temp += ["countryID" => null];
                }
            }
            $stmt->close();
        } else {
            printf("Error in select user query");
            return false;
        }

        $date = date('Y/m/d h:i:s a', time());
        $Temp += ["creationDate" => $date];
        $Temp += ["lastUpdatedDate" => $date];
        $Temp += ["role" => 0];
        $RError = false;
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
                                            Username or email address
                                            <div class="form-group">
                                                <input type="text" class="form-control <?php if(!$RError && !empty($LoginUserErr)) echo "is-invalid" ?>" name="LUserEmail" autofocus value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($LoginUserErr!="") echo $LoginUserErr; ?>
                                                </div>
                                            </div>
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
                            <div class="tab-pane fade show <?php if ($RError){ echo "active"; } ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <h3  class="register-heading">Register</h3>
                                    <div class="row register-form">
                                        <div class="col-md-6">
                                            Email
                                            <div class="form-group">
                                                <input type="email" class="form-control <?php if($RError && !empty($REmailErr)) echo "is-invalid" ?>" name="REmail" placeholder="example@mail.com" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($REmailErr!="") echo $REmailErr; ?>
                                                </div>
                                            </div>
                                            Username
                                            <div class="form-group">   
                                                <input type="text" class="form-control <?php if($RError && !empty($RUsernameErr)) echo "is-invalid" ?>" name="RUsername" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RUsernameErr!="") echo $RUsernameErr; ?>
                                                </div>
                                            </div>
                                            Password
                                            <div class="form-group">
                                                <input type="password" class="form-control <?php if($RError && !empty($RPasswordErr)) echo "is-invalid" ?>" name="RPassword" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RPasswordErr!="") echo $RPasswordErr; ?>
                                                </div>
                                            </div>
                                            Confirm Password
                                            <div class="form-group">
                                                <input type="password" class="form-control <?php if($RError && !empty($RCPasswordErr)) echo "is-invalid" ?>" name="RCPassword" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RCPasswordErr!="") echo $RCPasswordErr; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            Security Question
                                            <div class="form-group">
                                                <input type="text" class="form-control <?php if($RError && !empty($RQuestionErr)) echo "is-invalid" ?>" name="RQuestion" value="" autocomplete="off" />
                                                <div class='invalid-feedback'>
                                                    <?php if ($RQuestionErr!="") echo $RQuestionErr; ?>
                                                </div>
                                            </div>
                                            Security Answer
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


            <!-- <div class="row">
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 customPaddingLeft">
                    <div class="clearfix">
                        <div class="col-md-12" style="background-color: white; text-align: center; border-radius: 10px;">
                            <h2 style="padding: 1%; color: #495057">
                                
                            </h3>
                        </div>
                    </div>

                    <div class="row">
                        
                    </div>
                </div>

                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 customPaddingRight">
                    <div class="clearfix">
                        <div class="col-md-12" style="background-color: white; text-align: center; border-radius: 10px;">
                            <h2 style="padding: 1%; color: #495057">
                                
                            </h3>
                        </div>
                    </div>

                    <div class="row">
                        
                    </div>
                </div>
            </div> -->
            
        </main>

        <!-- JS/Jquery Import -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>    
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>