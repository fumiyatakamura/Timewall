<?php
    // http://php.net/manual/en/function.session-start.php
    // http://stackoverflow.com/questions/11768816/php-session-variables-not-preserved-with-ajax
    // http://stackoverflow.com/questions/9560240/how-session-start-function-works
    // get the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $DBHost = "localhost";
    $DBusername = "root";
    $DBpassword = "root";
    $DBname = "timewall";

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
                && isset($_POST["password"]) && !empty($_POST["password"])){


                // get the data from the post and store in variables
                $email = $_POST["email"];
                $password = $_POST["password"];

                try {

                    $conn = new PDO('mysql:host=localhost;dbname=timewall','root','root');
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = "SELECT * FROM users WHERE email = :email AND password = :password";

                    $statement = $conn->prepare($sql);
                    $statement->execute(array(":email" => $email, ":password" => $password));

                    // this should be one if there's a user by that user value and password value
                    $count = $statement->rowCount();

                    if($count > 0) {
                        // success, so fetch the first and hopefully only record

                        // http://stackoverflow.com/questions/15287905/convert-pdo-recordset-to-json-in-php
                        // http://php.net/manual/en/pdostatement.fetchall.php
                        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                        $returnedemail = $rows[0]['email'];
                        $returnedPassword = $rows[0]['password'];
                        $returnedfirstname = $rows[0]['firstname'];

                        // now put into the session that we're logged in
                        // also, could have an HMAC
                        // http://php.net/manual/en/function.hash-hmac.php
                        // http://stackoverflow.com/questions/4495950/how-do-stateless-servers-work/4496016#4496016
                        $_SESSION['firstname'] = $returnedfirstname;
                        $_SESSION['email'] = $returnedemail;
                        $_SESSION['loggedin'] = true;

                        // normally you don't put the session id in since it's already
                        // send in the HTTP header but here it is so that you can
                        // see that it was generated
                        $sid= session_id();
                        $data = array("status" => "success", "sid" => $sid, "firstname" => $returnedfirstname);


                    } else {
                        $data = array("status" => "fail", "msg" => "E-mail and/or password not correct.");
                    }


                } catch(PDOException $e) {
                    $data = array("status" => "fail", "msg" => $e->getMessage());
                }


            } else {
                $data = array("status" => "fail", "msg" => "E-mail and/or password is missing.");
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

