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
            $histRowNum = $_POST['histRowNum'];
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
    <link rel="icon" href="assets/img/Logo/favicon.ico">
    <link rel="stylesheet" href="../assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
</head>
<style>
    body{
        padding: 10px;
    }
</style>
<body>
    <br>
    <div class="ms-2 ">
    <form action="#" method="post">
        <input class="btn btn-dark" type="submit" name="toAdmin" value="Back to admin">
    </form>
    </div>
    <br>
    <h3 class="ms-2">History</h3>
    <table class="mx-auto table">
        <thead class="table-dark">
            <tr>
                <th>Transaction ID</th>
                <th>Midtrans Order ID</th>
                <th>Username</th >
                <th>Shipping Name</th>
                <th>Transaction Timestamp</th>
                <th>Status</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $histRowKey => $histRow) {?>
                <tr>
                    <td><?= $histRow['id_htrans']?></td>
                    <td><?= $histRow['mid_order_id']?></td>
                    <td style="text-transform:capitalize"><?= $histRow['username']?></td>
                    <td><?= $histRow['ship_name']?></td>
                    <td><?= $histRow['trans_time']?></td>
                    <td style="text-transform:capitalize"><?= $histRow['status']?></td>
                    <td>
                        <!-- <button class="showItems" transid='<?= $histRow['id_htrans']?>'>Show Items</button> -->
                        <form action="#" method="post">
                            <input type="hidden" name="order_id" value="<?= $histRow['mid_order_id']?>">
                            <input type="hidden" name="histRowNum" value="<?= $histRowKey?>">
                            <input class="btn btn-dark" type="submit" value="Show Items">
                        </form>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>

    <br><br><br><br>

    <?php if (isset($order_id)) {?>
        <h3>Order ID = <?=$histItems[0]['id_htrans']?></h3>
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th>Nama Ikan</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0;?>
                <?php foreach ($histItems as $histRow) {?>
                    <tr>
                        <td><?= $histRow['name']?></td>
                        <td><?= $histRow['price']?></td>
                        <td><?= $histRow['qty']?></td>
                        <td>Rp. <?= $histRow['price'] * $histRow['qty']?></td>
                    </tr>
                    <?php $total += $histRow['price'] * $histRow['qty']?>
                <?php }?>
            </tbody>
        </table>
        <h4>Total : Rp. <?= $total?></h4><br>
        <h4>Status : <?= $history[$histRowNum]['status']?></h4><br>
    <?php }?>
</body>
</html>