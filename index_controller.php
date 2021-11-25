<?php
    require_once("proyekpw_lib.php");

    $action = $_REQUEST['action'];
    if ($action == "checkNotif") {
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $sqlNotif = "SELECT COUNT(*) AS notif_count FROM htrans ht JOIN users u ON ht.id_user = u.id WHERE u.username = :username AND notif_seen = 0;";
            $stmt = $conn->prepare($sqlNotif);
            $stmt -> bindValue(":username",$_SESSION['currUsername']); 
            $stmt -> execute();

            $qResultNotif = $stmt -> fetch(PDO::FETCH_ASSOC);
            $response = $qResultNotif['notif_count'];

            echo $response;

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;

    }
?>