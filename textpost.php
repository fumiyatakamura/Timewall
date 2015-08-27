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
            if(isset($_POST["post"]) && !empty($_POST["post"])){


                // get the data from the post and store in variables
                // $email = $_POST["email"];
                $post = $_POST["post"];
                $email = $_POST["email"];

                try {
                    $conn = new PDO('mysql:host=localhost;dbname=timewall','root','root');
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = "insert into post (post,email,dateCreated) values (:post,:email,:dc);";
                    $date = date('Y-m-d H:i:s');

                    $statement = $conn->prepare($sql);
                    $statement->execute(array(":post" => $post,":email" => $email, ":dc" => $date));

                    $data = array("status" => "success", "msg" => $post);



                } catch(PDOException $e) {
                    $data = array("status" => "fail", "msg" => $e->getMessage());
                }


            } else {
                $data = array("status" => "fail", "msg" => "no text.");
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

