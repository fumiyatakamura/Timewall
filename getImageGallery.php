<?php
    // empty JSON
    $methodType = $_SERVER['REQUEST_METHOD'];

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "timewall";

    if ($methodType === 'GET') {

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(isset($_GET['email'])){
            $sql = "SELECT * FROM post WHERE email = :email ORDER BY ID DESC";
            $statement = $conn->prepare($sql);
            $statement->execute(array(":email" => $_GET['email']));
        } else {
            $sql = "SELECT * FROM post ORDER BY ID DESC";
            $statement = $conn->prepare($sql);
            $statement->execute();
        }

        $count = $statement->rowCount();

        if($count == 0) {
            echo "<p>No post currently in the gallery.</p>";
        } else {

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                echo "<div class='item'><p>" . $row['post'] . "</p></div>";
            }

        }

    } catch(PDOException $e) {
        echo "<p>" . $e->getMessage() . "</p>\n";
    }



    } else {
        //only taking GET requests
        echo "<p>Has to be a <code>GET</code>.</p>";
    }



?>
