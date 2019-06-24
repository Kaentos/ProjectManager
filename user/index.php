<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        $dbHost = "localhost";
        $dbUser = "root";
        $dbPassword = "";
        $dbName = "pmanager";
        $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        $UserData = array();
        $query = "SELECT * FROM  user WHERE id=".$_SESSION["user"]["id"];
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $UserData = $row;
                    $_SESSION["user"]["role"] = $row["role"];
                } else {
                    printf("MAJOR ERROR CAN'T CONVERT USER ROW TO ARRAY");
                    die();
                }
            } else {
                die();
            }
            $result->close();
        } else {
            printf("Error in select user query");
            die();
        }
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $NEmailErr = $NUsernameErr = $NPasswordErr = $NQuestionErr = $DELETEERR = "";

    $id = $UserData["id"];

    $query = "SELECT c.name FROM user as u JOIN countries as c ON u.idCountry = c.id  WHERE u.id=$id";
    if ($result = $conn->query($query)) {
        if ($result->num_rows == 1){
            if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                $UserData["idCountry"] = $row["name"];
            } else {
                printf("MAJOR ERROR CAN'T CONVERT USER ROW TO ARRAY");
                die();
            }
        } else {
            die();
        }
        $result->close();
    } else {
        printf("Error in select user query");
        die();
    }

    $query = "SELECT s.question FROM user as u JOIN usersecurity as s ON u.id = s.idUser  WHERE u.id=$id";
    if ($result = $conn->query($query)) {
        if ($result->num_rows == 1){
            if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                $UserData += ["CurrentQ" => $row["question"]];
            } else {
                printf("MAJOR ERROR CAN'T CONVERT USER ROW TO ARRAY");
                die();
            }
        } else {
            die();
        }
        $result->close();
    } else {
        printf("Error in select user query");
        die();
    }

    // Update username
    if (isset($_POST["updateUN"])){
        updateUsername($conn, $id);
    }
    function updateUsername($conn, $id){
        $username = test_input($_POST["nUsername"]);
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
                    $GLOBALS["NUsernameErr"] = "$username already taken.";
                    return;
                } else {
                    if($result->num_rows > 1) {
                        die("Report error with the following code: U2 and the username you are trying to input.");
                    }
                }
                $stmt->close();
            } else {
                printf("Error in select user query");
                return;
            }
        } else{
            $GLOBALS["NUsernameErr"] = "Insert a valid username (6-16 letters & numbers).";
            return;
        }
        
        // Update username in DB
        if(!($stmt = $conn->prepare("UPDATE user SET username='$username' WHERE id=?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("i", $id)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            $_SESSION["user"]["username"] = $username;
            header("Refresh:0");
            $result->close();
        }
    }

    // Update email
    if (isset($_POST["updateEM"])){
        updateEmail($conn, $id);
    }
    function updateEmail($conn, $id){
        $email = test_input($_POST["nEmail"]);
        $pass = $_POST["EMCPassword"];
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
                    return;
                } else {
                    if($result->num_rows > 1) {
                        die("Report error with the following code: UE2");
                    }
                }
                $stmt->close();
            } else {
                printf("Error in select user query");
                return;
            }
        } else {
            $GLOBALS["NEmailErr"] = "Incorrect type of email.";
            return;
        }

        if (ConfirmPassword($pass, $id, $conn)){
            // Updates in db
            if(!($stmt = $conn->prepare("UPDATE user SET email=? WHERE id=?;"))) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            if(!$stmt->bind_param("si", $email, $id)) {
                die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute()) {
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            } else {
                header("Refresh:0");
                $stmt->close();
            }
        } else {
            $GLOBALS["NEmailErr"] = "Wrong password.";
            return;
        }
    }

    // Update country
    if (isset($_POST["updateC"])){
        if (isset($_POST["Ncountry"])){
            $country = $_POST["Ncountry"];
        } else {
            goto endC;
        }

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
                if(!($stmt = $conn->prepare("UPDATE user SET idCountry=? WHERE id=?"))) {
                    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                }
                if(!$stmt->bind_param("ii", $country, $id)) {
                    die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
                }
                if(!$stmt->execute()) {
                    die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
                } else {
                    header("Refresh: 0");
                }
            } else {
                if($result->num_rows > 1) {
                    die("Report error with the following code: C2 and the country you are trying to change.");
                }
            }
            $stmt->close();
        } else {
            printf("Error in select user query");
            return;
        }
        endC:;
    }

    // Update question and answer
    if (isset($_POST["updateQA"])){
        updateQA($conn, $id);
    }
    function updateQA($conn, $id){
        $question = test_input($_POST["nQuestion"]);
        $answer = test_input($_POST["nAnswer"]);
        $options = [
            'cost' => 12,
        ];
        $HashedA = password_hash($answer, PASSWORD_BCRYPT, $options);
        $oldA = test_input($_POST["oAnswer"]);

        if(empty($oldA)){
            $GLOBALS["NQuestionErr"] = "Empty current answer.";
        } else {
            $query = "SELECT * FROM usersecurity WHERE idUser=$id;";
            $result = mysqli_query($conn,$query);
            if(!$result) {
                die("Error:". mysqli_error($conn));
            } else {
                if(!$row = mysqli_fetch_assoc($result)){
                    die();
                } else {
                    if (!password_verify($oldA, $row["answer"])){
                        $GLOBALS["NQuestionErr"] = "Wrong current answer.";
                        return;
                    } elseif (password_verify($answer, $row["answer"])){
                        $GLOBALS["NQuestionErr"] = "New Answer and current are the same.";
                        return;
                    }
                }
            }
        }
        
        if(strlen($question) <= 6 || strlen($question) > 30) {
            $GLOBALS["NQuestionErr"] = "Question must have at least min of 6 and max 30 letters.";
            echo $question;
            return;
        }
        if(!preg_match('/^\w{6,16}$/', $answer)) { // \w equals "[0-9A-Za-z_]"
            $GLOBALS["NQuestionErr"] = "Answer must have min 6 and max 16 letters/numbers (spaces not included).";
            return;
        }

        if(!($stmt = $conn->prepare("UPDATE usersecurity SET question=?, answer=? WHERE idUser=?"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("ssi", $question, $HashedA, $id)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            header("Refresh:0");
        }
    }

    // Update password
    if (isset($_POST["updatePass"])){
        updatePass($conn, $id);
    }
    function updatePass($conn, $id){
        $pw = test_input($_POST["nPassword"]);
        $pw2 = test_input($_POST["CnPassword"]);
        $oldPass = $_POST["OPassword"];
        if (ConfirmPassword($oldPass, $id, $conn)){
            if(!empty($pw) && ($pw == $pw2)) {
                if (strlen($pw) <= 6 || strlen($pw) > 16) {
                    $GLOBALS["NPasswordErr"] = "New password must contain 6 to 16 characters.";
                    return;
                }
                elseif(!preg_match("#[0-9]+#", $pw)) {
                    $GLOBALS["NPasswordErr"] = "New password must contain at least 1 number.";
                    return;
                }
                elseif(!preg_match("#[A-Z]+#", $pw)) {
                    $GLOBALS["NPasswordErr"] = "New password must contain at least 1 capital letter.";
                    return;
                }
                elseif(!preg_match("#[a-z]+#", $pw)) {
                    $GLOBALS["NPasswordErr"] = "New password must contain at least 1 lowercase letter.";
                    return;
                }
            }
            elseif(!empty($_POST["password"])) {
                $GLOBALS["NPasswordErr"] = "Confirm password invalid.";
                return;
            } else {
                $GLOBALS["NPasswordErr"] = "Empty password input.";
                return;
            }

            if (ConfirmPassword($pw, $id, $conn)){
                $GLOBALS["NPasswordErr"] = "You are trying to update your password with current password.";
                return;
            }
            $sql = "UPDATE usersecurity SET password='$pw' WHERE idUser=$id";
            if (mysqli_query($conn, $sql)) {
                header("Refresh:0");
            } else {
                die("Error: " . mysqli_error($conn));
            }
        } else {
            $GLOBALS["NPasswordErr"] = "Wrong current password.";
        }
    }

    // Function to confirm if current password is correct
    function ConfirmPassword($pass, $id, $conn){
        $query = "SELECT * FROM usersecurity WHERE idUser=$id;";
        $result = mysqli_query($conn,$query);
        if(!$result) {
            die("Error:". mysqli_error($conn));
        } else {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($pass, $row["password"])){
                return true;
            } else {
                return false;
            }
                
        }
    }

    // Deletes account
    if (isset($_POST["DELETEACC"])){
        if (ConfirmPassword($_POST["DELPassword"], $id, $conn) ){
            $sql = "DELETE FROM usersecurity WHERE idUser = $id";
            if (mysqli_query($conn, $sql)) {
                $sql = "DELETE FROM user WHERE id=$id";
                if (mysqli_query($conn, $sql)) {
                    session_destroy();
                    header("Refresh:0");
                } else {
                    die("Error: " . mysqli_error($conn));
                }
            } else {
                die("Error: " . mysqli_error($conn));
            }
        } else {
            $GLOBALS["DELETEERR"] = "Invalid password.";
        }
    }


?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title>User Settings</title>
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
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row bg_white" style="border-radius: 5px;">
                    <form class="row col-md-12" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="col-md-12" style="text-align:center; padding-top: 15px">  
                            <h1>User settings</h1>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <p><h3>Geral</h3></p>

                            <!-- Update username -->
                            Current username:
                            <div class='alert alert-secondary' role='alert'><?php echo $UserData["username"]; ?></div>
                            <?php if ($NUsernameErr!="") echo "<div class='alert alert-danger' role='alert'> $NUsernameErr </div>" ?>
                            New username:
                            <div class="form-group">
                                <input type="text" class="form-control" name="nUsername" value="" autocomplete="off" />
                            </div>
                            <input type="submit" class="btn btn-success" name="updateUN" value="Update"/>
                            <br><br>
                            
                            <!-- Update email -->
                            Current email:
                            <div class='alert alert-secondary' role='alert'> <?php echo $UserData["email"]; ?> </div>
                            <?php if ($NEmailErr!="") echo "<div class='alert alert-danger' role='alert'> $NEmailErr </div>" ?>
                            New email:
                            <div class="form-group">
                                <input type="email" class="form-control" name="nEmail" placeholder="example@mail.com" autocomplete="off" />
                            </div>
                            Password:
                            <div class="form-group">
                                <input type="password" class="form-control" name="EMCPassword" autocomplete="off" />
                            </div>
                            <input type="submit" class="btn btn-success" name="updateEM" value="Update"/>
                            <br><br>

                            <!-- Update country -->
                            Current Country:
                            <div class='alert alert-secondary' role='alert'> <?php echo $UserData["idCountry"]; ?> </div>
                            New Country:
                            <div class="form-group">
                                <select class="form-control" name="Ncountry">
                                    <option class="hidden" value="null" selected disabled>Please select your new country</option>
                                    <?php
                                        $sql = mysqli_query($conn, "SELECT id, name FROM countries");
                                        while ($row = $sql->fetch_assoc()){
                                            if ($row['name'] != $UserData["idCountry"]){
                                                echo "<option value='". $row['id'] ."'>" . $row['name'] . "</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <input type="submit" class="btn btn-success" name="updateC" value="Update"/>
                        </div>

                        <div class="col-md-6">
                            <p><h3>Security</h3></p>

                            <!-- Update question & answer -->
                            Current question:
                            <div class='alert alert-secondary' role='alert'> <?php echo $UserData["CurrentQ"]; ?> </div>
                            <?php if ($NQuestionErr!="") echo "<div class='alert alert-danger' role='alert'> $NQuestionErr </div>" ?>
                            New question:
                            <div class="form-group">
                                <input type="text" class="form-control" name="nQuestion" autocomplete="off" />
                            </div>
                            New answer:
                            <div class="form-group">
                                <input type="text" class="form-control" name="nAnswer" autocomplete="off" />
                            </div>
                            Current answer:
                            <div class="form-group">
                                <input type="text" class="form-control" name="oAnswer" autocomplete="off" />
                            </div>
                            <input type="submit" class="btn btn-success" style="float:right;" name="updateQA" value="Update"/>
                            <br><br>

                            <!-- Update password -->
                            <?php if ($NPasswordErr!="") echo "<div class='alert alert-danger' role='alert'> $NPasswordErr </div>" ?>
                            New Password:
                            <div class="form-group">
                                <input type="password" class="form-control" name="nPassword" autocomplete="off" />
                            </div>
                            Confirm new Password:
                            <div class="form-group">
                                <input type="password" class="form-control" name="CnPassword" autocomplete="off" />
                            </div>
                            Current Password:
                            <div class="form-group">
                                <input type="password" class="form-control" name="OPassword" autocomplete="off" />
                            </div>
                            <input type="submit" class="btn btn-success" style="float:right;" name="updatePass" value="Update"/>
                        </div>


                        <!-- Delete account -->
                        <div class="col-md-12" style="padding-top:5%">
                            <h3 style="color:red">Delete Account</h3>
                            <h5 style="color:red">We cannot recover your account if you delete it. Be careful.</h5>
                            <?php if ($DELETEERR!="") echo "<div class='alert alert-danger' role='alert'> $DELETEERR </div>" ?>
                            Password:
                            <div class="form-group">
                                <input type="password" class="form-control" name="DELPassword" autocomplete="off" />
                            </div>  
                            <input type="submit" class="btn btn-danger" name="DELETEACC" value="Delete"/>
                        </div>
                    </form>
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