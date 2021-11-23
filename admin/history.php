<?php
    require_once("../proyekpw_lib.php");

    if (isset($_GET['userid'])) {
        $userid = $_GET['userid'];
    } else {
        die('pls specify userid');
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['toAdmin'])) {
            header("Location:control.php");
        }
        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            try {
                $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                // set the PDO error mode to exception
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
                $sql = "SELECT * FROM `dtrans` dt JOIN ikan i ON dt.ikan_id = i.id WHERE dt.`id_htrans` = :order_id;";
                $stmt = $conn -> prepare($sql);
                $stmt -> bindValue(":order_id",$order_id);
                $stmt->execute();
                $histItems = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        
            } catch(\PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            $conn=null;
        }
    }

    try {
        $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT ht.id_htrans, ht.mid_order_id, u.username, s.ship_name, ht.trans_time, ht.status FROM `htrans` ht JOIN `users` u ON ht.id_user = u.id JOIN shipping s ON ht.id_shipping = s.shipping_id WHERE `id_user` = :iduser;";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindValue(":iduser", $userid);
        $stmt -> execute();
        $history = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    } catch(\PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn=null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History User</title>
</head>
<body>
    <form action="#" method="post">
        <input type="submit" name="toAdmin" value="Back to admin">
    </form>

    <h3>History</h3>
    <table border="1">
        <thead>
            <tr>
                <th>id_trans</th>
                <th>midtrans order id</th>
                <th>username</th >
                <th>shipping name</th>
                <th>transaction timestamp</th>
                <th>status</th>
                <th>items</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $histRow) {?>
                <tr>
                    <td><?= $histRow['id_htrans']?></td>
                    <td><?= $histRow['mid_order_id']?></td>
                    <td><?= $histRow['username']?></td>
                    <td><?= $histRow['ship_name']?></td>
                    <td><?= $histRow['trans_time']?></td>
                    <td><?= $histRow['status']?></td>
                    <td>
                        <!-- <button class="showItems" transid='<?= $histRow['id_htrans']?>'>Show Items</button> -->
                        <form action="#" method="post">
                            <input type="hidden" name="order_id" value="<?= $histRow['mid_order_id']?>">
                            <input type="submit" value="Show Items">
                        </form>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>

    <br><br><br><br>

    <?php if (isset($order_id)) {?>
        Order ID = <?= $histItems[0]['id_htrans']?>
        <table border="1">
            <thead>
                <tr>
                    <th>nama ikan</th>
                    <th>price</th>
                    <th>qty</th>
                    <th>subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0;?>
                <?php foreach ($histItems as $histRow) {?>
                    <tr>
                        <td><?= $histRow['name']?></td>
                        <td><?= $histRow['price']?></td>
                        <td><?= $histRow['qty']?></td>
                        <td><?= $histRow['price'] * $histRow['qty']?></td>
                    </tr>
                    <?php $total += $histRow['price'] * $histRow['qty']?>
                <?php }?>
            </tbody>
        </table>
        Total = <?= $total?>
    <?php }?>
</body>
</html>