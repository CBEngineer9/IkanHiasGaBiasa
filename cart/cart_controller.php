<?php
    require_once("../proyekpw_lib.php");

    $action = $_REQUEST['action'];
    if ($action == "getPriceCount") {
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $sqlTotal = "SELECT c.user_id as \"user_id\",  c.qty as qty, i.price as price FROM cart c JOIN ikan i ON c.ikan_id = i.id WHERE c.`user_id` = (SELECT id FROM users WHERE username = :username);";
            // $sqlTotal = "SELECT SUM(sumdata.qty) as qty_sum, SUM(sumdata.price) as price_sum FROM (SELECT c.user_id as \"user_id\",  c.qty as qty, i.price as price FROM cart c JOIN ikan i ON c.ikan_id = i.id WHERE c.`user_id` = (SELECT id FROM users WHERE username = :username)) as sumdata GROUP BY sumdata.`user_id`;";
            $stmt = $conn->prepare($sqlTotal);
            $stmt -> bindValue(":username",$_SESSION['currUsername']); 
            $stmt -> execute();

            $qResultTotal = $stmt -> fetchAll(PDO::FETCH_ASSOC);
            $total = 0;
            $itemCount = 0;
            foreach ($qResultTotal as $totalRow) {
                $itemCount += 1;
                $total += ($totalRow['qty'] * $totalRow['price']);
            }
    
            $response['total'] = $total;
            $response['itemCount'] = $itemCount;
            
            echo json_encode($response);

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;

    } else if ($action == "addQty") {
        $cart_id = $_REQUEST['cart_id'];
        $amount = $_REQUEST['amount'];

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //check cart qty
            $sql = "SELECT qty FROM cart c WHERE c.cart_id = :cartid";
            $stmt = $conn->prepare($sql);
            $stmt -> bindValue(":cartid",$cart_id);
            $stmt -> execute();
            $resultQty = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($resultQty['qty'] <= 1 && $amount < 0) {
                die("negative qty");
            }

            // check stock
            $sql = "SELECT stock FROM ikan i JOIN cart c ON i.id = c.ikan_id WHERE c.cart_id = :cartid";
            $stmt = $conn->prepare($sql);
            $stmt -> bindValue(":cartid",$cart_id);
            $stmt -> execute();
            $resultStock = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($resultQty['qty'] + $amount >= $resultStock['stock'] ) {
                die("not enough stock");
            }

            //update qty
            $sqlCart = "UPDATE `cart` c SET c.`qty` = (SELECT cb.qty + :amount FROM (SELECT * FROM cart) cb WHERE cb.`cart_id` = :cartid) WHERE c.`cart_id` = :cartid;";
            $stmt = $conn->prepare($sqlCart);
            $stmt -> bindValue(":cartid",$cart_id);
            $stmt -> bindValue(":amount",$amount); 

            $qResultCart = $stmt -> execute();

            if ($qResultCart == 1) {
                //build response
                $sqlResponse = "SELECT c.qty, i.price FROM cart c JOIN ikan i ON c.ikan_id = i.id WHERE c.`cart_id` = :cartid;";
                $stmt = $conn->prepare($sqlResponse);
                $stmt -> bindValue(":cartid",$cart_id); 
                $stmt -> execute();

                $response = $stmt -> fetch(PDO::FETCH_ASSOC);
                echo json_encode($response);
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    } else if ($action == 'addItem') {
        $ikan_id = $_REQUEST['ikan_id'];

        if (!isset($_SESSION['currUsername'])) {
            die('not_logged_in');
        }

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // check stock
            $sql = "SELECT stock FROM ikan WHERE id = :ikan_id";
            $stmt = $conn->prepare($sql);
            $stmt -> bindValue(":ikan_id",$ikan_id); 
            $stmt -> execute();
            $resultStock = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($resultStock['stock'] <= 0) {
                die("empty");
            }
            
            // update stock
            $sql = "UPDATE `ikan` i SET i.`stock` = i.stock - 1 WHERE i.`id` = :ikan_id";
            $stmt = $conn->prepare($sql);
            // $stmt -> bindValue(":qty",$qty); 
            $stmt -> bindValue(":ikan_id",$ikan_id); 
            $succUpdate = $stmt -> execute();

            // update if exists
            $sqlUpdate = "UPDATE cart SET qty = qty + 1 WHERE ikan_id = :ikan_id AND `user_id` = :userid";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt -> bindValue(":ikan_id",$ikan_id);
            $stmt -> bindValue(":userid",$_SESSION['currUser']);
            $succUpdate = $stmt -> execute();
            $updateCount = $stmt -> rowCount();

            if ($updateCount <= 0) {
                $sqlCart = "INSERT INTO `cart` (`user_id`, `ikan_id`, `qty`) VALUES ( (SELECT id FROM users WHERE username = :currUsername), :ikan_id, :qty);";
                $stmt = $conn->prepare($sqlCart);
                $stmt -> bindValue(":currUsername",$_SESSION['currUsername']); 
                $stmt -> bindValue(":ikan_id",$ikan_id); 
                $stmt -> bindValue(":qty",1); 

                $succInsert = $stmt -> execute();
            }

            if ($updateCount > 0 || $succInsert == true) {
                echo "success" ;
            } else {
                echo "fail";
            }

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            // alert("Connection failed: " . $e->getMessage());
        }
        $conn=null;

    } else if ($action == 'removeItem') {
        $cart_id = $_REQUEST['cart_id'];
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $sqlCart = "DELETE FROM cart WHERE `cart_id` = :cartid;";
            $stmt = $conn->prepare($sqlCart);
            $stmt -> bindValue(":cartid",$cart_id); 

            $succDelete = $stmt -> execute();

            if ($succDelete == 1) {
                $sqlCart = "SELECT * FROM cart c JOIN ikan i ON c.ikan_id = i.id JOIN category cat ON cat.cat_id = i.cat_id WHERE `user_id` = :userid;";
                $stmt = $conn->prepare($sqlCart);
                $stmt -> bindValue(":userid",$_SESSION['currUser']); 
                $stmt -> execute();
        
                $qResultCart = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($qResultCart as $cartRow) {
                    echo '<div class="row border-top border-bottom">'.
                                '<div class="row main align-items-center">'.
                                    '<div class="col-2"><img class="img-fluid" src="../'.$cartRow["imageLink"].'"></div>'.
                                    '<div class="col">'.
                                        '<div class="row text-muted">'.$cartRow['cat_name'].'</div>'.
                                        '<div class="row">'.$cartRow['name'].'</div>'.
                                    '</div>'.
                                    '<div class="col"> <a onclick="addQty(\''.$cartRow['cart_id'].'\',-1)">-</a><a href="#" id="qty'.$cartRow['cart_id'].'" class="border"> '.$cartRow['qty'].' </a><a onclick="addQty(\''.$cartRow['cart_id'].'\',1)">+</a> </div>'.
                                    '<div class="col"><span id="price'.$cartRow['cart_id'].'"> '."Rp.". number_format($cartRow['price'] * $cartRow['qty'], 2).'</span> <span onclick="removeItem(\''.$cartRow['cart_id'].'\')" class="close">&#10005;</span></div>'.
                                '</div>'.
                            '</div>';
                }
            }

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    } else if ($action == "getCart") {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            
            $sqlResponse = "SELECT i.id as id, i.name as `name`, i.price as price, c.qty as `quantity` FROM cart c JOIN ikan i ON c.ikan_id = i.id WHERE c.`user_id` = (SELECT id FROM users WHERE username = :username);";
            $stmt = $conn->prepare($sqlResponse);
            $stmt -> bindValue(":username",$_SESSION['currUsername']); 
            $stmt -> execute();

            $response = $stmt -> fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($response);
            
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    } else if ($action == "getUserData") {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlResponse = "SELECT firstname as first_name, lastname as last_name, email, phone FROM users WHERE `id` = (SELECT id FROM users WHERE username = :username);";
            $stmt = $conn->prepare($sqlResponse);
            $stmt -> bindValue(":username",$_SESSION['currUsername']);
            $stmt -> execute();

            $response = $stmt -> fetch(PDO::FETCH_ASSOC);
            echo json_encode($response);
            
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    } else if ($action == "clearCart") {
        $order_id = $_REQUEST['order_id'];
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //set to waiting conf
            // $sql = "UPDATE `htrans` SET `status` = 'Waiting Confirm' WHERE `htrans`.`mid_order_id` = :order_id;";
            // $stmt = $conn -> prepare($sql);
            // $stmt -> bindValue(":order_id",$order_id);
            // $stmt->execute();

            //clear cart
            $sql = "DELETE FROM `cart` WHERE `user_id` = (SELECT id FROM users WHERE username = :username);";
            $stmt = $conn -> prepare($sql);
            $stmt -> bindValue(":username",$_SESSION['currUsername']);
            $succDelCart = $stmt->execute();
            echo $succDelCart;
            
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn=null;
    }
    die;
?>