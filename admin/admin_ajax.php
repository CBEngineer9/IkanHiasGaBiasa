<?php
    require_once("../proyekpw_lib.php");

    $action = $_REQUEST['action'];
    if ($action == "deleteUser") {
        $user_id = $_REQUEST['user_id'];
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "DELETE FROM users WHERE id = :userid";
            $stmt = $conn->prepare($sql);
            $stmt -> bindValue(":userid",$user_id);
            $qresultDel = $stmt -> execute();

            echo $qresultDel;

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    } else if ($action == "getTrans") {
        $filterStart = $_REQUEST['filterStart'];
        $filterEnd = $_REQUEST['filterEnd'];
        $keyword = $_REQUEST['keyword'];
        $offset = ($_REQUEST['page']-1) * 50;


        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlHeaderData = "SELECT ht.id_htrans, ht.mid_order_id, u.username, s.ship_name, ht.trans_time, ht.status ";
            $sqlHeaderCount = "SELECT COUNT(*) AS trans_count ";
            $sqlBody = "FROM `htrans` ht JOIN `users` u ON ht.id_user = u.id JOIN shipping s ON ht.id_shipping = s.shipping_id WHERE 1=1 ";
            $sqlFilters = "";
            
            if ( !empty($filterStart) && !empty($filterEnd) && ( strtotime($filterEnd) - strtotime($filterStart) > 0)) {
                $dbFilterStart = date('Y-m-d',strtotime($filterStart));
                $dbFilterEnd = date('Y-m-d',strtotime($filterEnd));
                $sqlFilters .= "AND trans_time >= :filter_start AND trans_time <= :filter_end ";
            }
            if (!empty($keyword)) {
                if (preg_match( "/^IHGB[0-9]{6}$/" , $keyword ) == 1) {
                    // by trans id
                    $sqlFilters .= "AND mid_order_id = :keyword ";
                } else {
                    // by cust name
                    $keyword = '%'.$keyword.'%';
                    $sqlFilters .= "AND firstname LIKE :keyword OR lastname LIKE :keyword ";
                }
            }

            $sql = $sqlHeaderCount . $sqlBody . $sqlFilters;
            $stmt = $conn->prepare($sql);
            if ( !empty($filterStart) && !empty($filterEnd) && ( strtotime($filterEnd) - strtotime($filterStart) > 0)) {
                $stmt -> bindValue(":filter_start",$dbFilterStart);
                $stmt -> bindValue(":filter_end",$dbFilterEnd);
            }
            if (!empty($keyword)) {
                $stmt -> bindValue(":keyword",$keyword);
            }
            $stmt -> execute();
            $transCount = $stmt -> fetch(PDO::FETCH_ASSOC);
            $transPage = ceil($transCount['trans_count']/50);


            $sql = $sqlHeaderData . $sqlBody . $sqlFilters . "LIMIT 50 OFFSET :offset ;";
            $stmt = $conn->prepare($sql);
            $stmt -> bindValue(":offset",$offset,PDO::PARAM_INT);
            if ( !empty($filterStart) && !empty($filterEnd) && ( strtotime($filterEnd) - strtotime($filterStart) > 0)) {
                $stmt -> bindValue(":filter_start",$dbFilterStart);
                $stmt -> bindValue(":filter_end",$dbFilterEnd);
            }
            if (!empty($keyword)) {
                $stmt -> bindValue(":keyword",$keyword);
            }
            $stmt -> execute();
            $transList = $stmt -> fetchAll(PDO::FETCH_ASSOC);


            $response = [
                "page" => $transPage,
                "list" => $transList,
            ];

            echo json_encode($response);

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    } else if ($action == "getDTrans") {
        $order_id = $_REQUEST['order_id'];
        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
            $sql = "SELECT * FROM `dtrans` dt JOIN ikan i ON dt.ikan_id = i.id WHERE dt.`id_htrans` = :order_id;";
            $stmt = $conn -> prepare($sql);
            $stmt -> bindValue(":order_id",$order_id);
            $stmt->execute();
            $histItems = $stmt -> fetchAll(PDO::FETCH_ASSOC);

            $sql = "SELECT ht.id_htrans, ht.mid_order_id, u.username, s.ship_name, ht.trans_time, ht.status FROM `htrans` ht JOIN `users` u ON ht.id_user = u.id JOIN shipping s ON ht.id_shipping = s.shipping_id WHERE ht.mid_order_id = :order_id";
            $stmt = $conn -> prepare($sql);
            $stmt -> bindValue(":order_id",$order_id);
            $stmt->execute();
            $histDetails = $stmt -> fetch(PDO::FETCH_ASSOC);

            $response = [
                "detail" => $histDetails,
                "items" => $histItems,
            ];

            echo json_encode($response);
    
        } catch(\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    } else if ($action == "editPicture") {
        $ikan_id = $_REQUEST['ikan_id'];

        //for file Uploads
        $file = $_FILES["newPic"]["name"];
        $imageFileType = strtolower(pathinfo($file,PATHINFO_EXTENSION));
        $db_target_dir = "assets/img/ikan/";
        $db_target_file = $db_target_dir . "ikan" . $ikan_id . "." . $imageFileType;
        $target_file = "../" . $db_target_file;
        $uploadOk = 1;

        $check = getimagesize($_FILES["newPic"]["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        if (check_file_uploaded_name($file)) {
            echo "File contains illegal character.";
            $uploadOk = 0;
        }
        if (check_file_uploaded_length($file)) {
            echo "File name too long. ";
            $uploadOk = 0;
        }

        // if (file_exists($target_file)) {
        //     echo "Sorry, file already exists.";
        //     $uploadOk = 0;
        // }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["newPic"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["newPic"]["name"])). " has been uploaded.";

                // and update entry in db
                try {
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                    // set the PDO error mode to exception
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $sql = "UPDATE `ikan` SET `imageLink`= :imageLink WHERE id = :ikan_id";
                    $stmt = $conn->prepare($sql);
                    $stmt -> bindValue(":ikan_id",$ikan_id);
                    $stmt -> bindValue(":imageLink",$db_target_file);
                    $qresult = $stmt -> execute();
                    
                } catch(PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                }
                $conn=null;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    die;
?>