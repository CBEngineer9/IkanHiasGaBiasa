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

    } else if ($action == "getTrans") {
        $filterStart = $_REQUEST['filterStart'];
        $filterEnd = $_REQUEST['filterEnd'];
        $keyword = $_REQUEST['keyword'];
        $sort = $_REQUEST['sort'];
        $payStatusFilter = $_REQUEST['payStatusFilter'];
        $offset = ($_REQUEST['page']-1) * 50;


        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlHeaderData = "SELECT ht.id_htrans, ht.mid_order_id, u.username, s.ship_name, ht.trans_time, ht.status, ht.notif_seen ";
            $sqlHeaderCount = "SELECT COUNT(*) AS trans_count ";
            $sqlBody = "FROM `htrans` ht JOIN `users` u ON ht.id_user = u.id JOIN shipping s ON ht.id_shipping = s.shipping_id WHERE ht.id_user= :userid ";
            $sqlFilters = " ";
            $sqlSort = "ORDER BY ht.trans_time ";
            
            if ( !empty($filterStart) && !empty($filterEnd) && ( strtotime($filterEnd) - strtotime($filterStart) > 0)) {
                $dbFilterStart = date('Y-m-d',strtotime($filterStart));
                $dbFilterEnd = date('Y-m-d',strtotime($filterEnd));
                $sqlFilters .= "AND trans_time >= :filter_start AND trans_time <= :filter_end ";
            }
            if (!empty($keyword)) {
                // by trans id
                $sqlFilters .= "AND mid_order_id = :keyword ";
            }
            if (!empty($payStatusFilter)) {
                // by trans id
                if ($payStatusFilter == "success") {
                    $sqlFilters .= "AND (`status` = 'Success' OR `status` = 'Settlement') ";
                } else if ($payStatusFilter == "pending") {
                    $sqlFilters .= "AND (`status` = 'pending' OR `status` = 'attempted' OR `status` = 'challenge') ";
                } else if ($payStatusFilter == "fail") {
                    $sqlFilters .= "AND (`status` = 'Denied' OR `status` = 'Expire' OR `status` = 'Cancel') ";
                }
            }
            if ($sort == "newest") {
                $sqlSort .= "desc ";
            }

            $sql = $sqlHeaderCount . $sqlBody . $sqlFilters . $sqlSort;
            $stmt = $conn->prepare($sql);
            $stmt -> bindValue(":userid",$_SESSION['currUser']);
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


            $sql = $sqlHeaderData . $sqlBody . $sqlFilters  . $sqlSort . "LIMIT 50 OFFSET :offset ;";
            $stmt = $conn->prepare($sql);
            $stmt -> bindValue(":userid",$_SESSION['currUser']);
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

            // TODO EXPERIMENTAL : update seen notif
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE `htrans` SET `notif_seen` = 1 WHERE `notif_seen` = 0;";
            $stmt = $conn -> prepare($sql);
            $succInsert = $stmt->execute();


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
    }

    die;
?>