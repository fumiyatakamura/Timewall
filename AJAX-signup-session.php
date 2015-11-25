<?php
    // http://php.net/manual/en/function.session-start.php
    // http://stackoverflow.com/questions/11768816/php-session-variables-not-preserved-with-ajax
    // http://stackoverflow.com/questions/9560240/how-session-start-function-works
    // get the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "timewall";

    $methodType = $_SERVER['REQUEST_METHOD'];
    $data = array("status" => "fail", "msg" => "$methodType");

    if ($methodType === 'POST') {

/*
        foreach ($_POST as $key => $value){
            // simply parrot back what was sent
            $data[$key] = $value;
        }
        echo json_encode($data, JSON_FORCE_OBJECT);
        return;
*/

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // yes, is AJAX call
            // answer POST call and get the data that was sent
            if(isset($_POST["email"]) && !empty($_POST["email"])
                && isset($_POST["password"]) && !empty($_POST["password"])
                && isset($_POST["firstname"]) && !empty($_POST["firstname"])
                && isset($_POST["lastname"]) && !empty($_POST["lastname"])){


                // get the data from the post and store in variables
                $email = $_POST["email"];
                $password = $_POST["password"];
                $firstname = $_POST["firstname"];
                $lastname = $_POST["lastname"];

                try {
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = "insert into users (firstname,lastname,email,password) values (:firstname,:lastname,:email,:password);";

                    $statement = $conn->prepare($sql);
                    $statement->execute(array(":firstname" => $firstname, ":lastname" => $lastname, ":email" => $email, ":password" => $password));

                    $data = array("status" => "success", "firstname" => $firstname, "lastname" => $lastname, "email" => $email, "password" => $password);



                } catch(PDOException $e) {
                    $data = array("status" => "fail", "msg" => $e->getMessage());
                }


            } else {
                $data = array("status" => "fail", "msg" => "You have something that's not filled out!!");
            }



        } else {
            // not AJAX
            $data = array("status" => "fail", "msg" => "Has to be an AJAX call.");
        }


    } else {
        // simple error message, only taking POST requests
        $data = array("status" => "fail", "msg" => "Error: only POST allowed.");
    }

    echo json_encode($data, JSON_FORCE_OBJECT);

?>

