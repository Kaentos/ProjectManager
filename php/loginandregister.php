<?php 

// Login
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

        $GLOBALS["LUsername"] = $username;

        if(!($stmt = $conn->prepare("SELECT * FROM  user WHERE ( username= ? OR email = ?)"))) {
            sendError("LRF-PT-P");
        }
        if(!$stmt->bind_param("ss", $username, $username)) {
            sendError("LRF-PT-B");
        }
        if(!$stmt->execute()) {
            sendError("LRF-PT-E");
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Temp += ["id" => $row["id"]];
                    $Temp += ["username" => $row["username"]];
                    $Temp += ["role" => $row["role"]];
                } else {
                    sendError("LRF-PT-FA");
                }
                $stmt->close();
            } else {
                if($result->num_rows == 0){
                    $GLOBALS["LoginUserErr"]="Invalid username or password";
                    return false;
                } else {
                    sendError("U2");
                }
            }
        } else {
            sendError("LRF-PT-GR");
        }

        // Verify password
        if(!($stmt = $conn->prepare("SELECT * FROM  usersecurity WHERE idUser= ?"))) {
            sendError("LRF-PT-P");
        }
        if(!$stmt->bind_param("i", $Temp["id"])) {
            sendError("LRF-PT-B");
        }
        if(!$stmt->execute()) {
            sendError("LRF-PT-E");
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    if (!password_verify($password, $row["password"])){
                        $GLOBALS["LoginUserErr"] = "Invalid username or password!";
                        return false;
                    }
                } else {
                    sendError("LRF-PT-FA");
                }
                $stmt->close();
            } else {
                if($result->num_rows == 0){
                    $GLOBALS["LoginUserErr"]="Invalid username or password";
                    return false;
                } else {
                    sendError("US2");
                }
            }
        } else {
            sendError("LRF-PT-GR");
        }

        $Temp;
        return $Temp;
    }
// END Login

// Register

    function AddUser($conn){
        $NewUser = validateInput($conn);
        if (!isset($NewUser["role"])){
            return;
        }

        if(!($stmt = $conn->prepare("INSERT INTO user (email, username, creationDate, lastUpdateDate, idCountry, role) VALUES (?, ?, ?, ?, ?, ?);"))) {
            sendError("LRF-PT-P");
            return;
        }
        if(!$stmt->bind_param("ssssis", $NewUser["email"], $NewUser["username"], $NewUser["creationDate"], $NewUser["lastUpdatedDate"], $NewUser["countryID"], $NewUser["role"])) {
            sendError("LRF-PT-B");
            return;
        }
        if(!$stmt->execute()) {
            sendError("LRF-PT-E-1");
            return;
        } else {
            $user_id = $conn->insert_id;
        }

        if(!($stmt = $conn->prepare("INSERT INTO usersecurity (idUser, password, question, answer) VALUES (?, ?, ?, ?);"))) {
            sendError("LRF-PT-P");
            return;
        }
        if(!$stmt->bind_param("isss", $user_id, $NewUser["password"], $NewUser["question"], $NewUser["answer"])) {
            sendError("LRF-PT-B");
            return;
        }
        if(!$stmt->execute()) {
            if(!($stmt = $conn->prepare("DELETE FROM user where id=?;"))) {
                sendError("LRF-PT-P");
                return;
            }
            if(!$stmt->bind_param("i", $user_id)) {
                sendError("LRF-PT-B");
                return;
            }
            if(!$stmt->execute()){
                sendError("LRF-PT-E-2");
                return;
            }
            sendError("LRF-PT-E-3");
            return;
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

        $GLOBALS["REmail"] = $email;
        $GLOBALS["RUsername"] = $username;
        $GLOBALS["RQuestion"] = $Squestion;

        // Email validation
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Check if email is already taken
            if(!($stmt = $conn->prepare("SELECT id FROM user WHERE email = ?;"))) {
                sendError("LRF-PT-P");
            }
            if(!$stmt->bind_param("s", $email)) {
                sendError("LRF-PT-B");
            }
            if(!$stmt->execute()) {
                sendError("LRF-PT-E");
            }
            if ($result = $stmt->get_result()) {
                if ($result->num_rows == 1){
                    $GLOBALS["REmailErr"] = "$email already taken.";
                    $GLOBALS["RError"] = true;
                    return;
                } else {
                    if($result->num_rows > 1) {
                        sendError("U2");
                    }
                }
                $stmt->close();
            } else {
                sendError("LRF-PT-GR");
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
                sendError("LRF-PT-P");
            }
            if(!$stmt->bind_param("s", $username)) {
                sendError("LRF-PT-B");
            }
            if(!$stmt->execute()) {
                sendError("LRF-PT-E");
            }
            if ($result = $stmt->get_result()) {
                if ($result->num_rows == 1){
                    $GLOBALS["RUsernameErr"] = "$username already taken.";
                    $GLOBALS["RError"] = true;
                    return;
                } else {
                    if($result->num_rows > 1) {
                        sendError("U2");
                    }
                }
                $stmt->close();
            } else {
                sendError("LRF-PT-GR");
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
            sendError("LRF-PT-P");
        }
        if(!$stmt->bind_param("i", $country)) {
            sendError("LRF-PT-B");
        }
        if(!$stmt->execute()) {
            sendError("LRF-PT-E");
        }
        if ($result = $stmt->get_result()) {
            if ($result->num_rows == 1){
                $Temp += ["countryID" => $country];
            } else {
                if($result->num_rows > 1) {
                    sendError("C2");
                } else {
                    $Temp += ["countryID" => null];
                }
            }
            $stmt->close();
        } else {
            sendError("LRF-PT-GR");
        }

        $date = date('Y/m/d h:i:s a', time());
        $Temp += ["creationDate" => $date];
        $Temp += ["lastUpdatedDate" => $date];
        $Temp += ["role" => 0];
        $RError = false;
        return $Temp;
    }

// END Register

?>