<?php
    require_once("proyekpw_lib.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
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

        $sql = "SELECT ht.id_htrans, ht.mid_order_id, u.username, s.ship_name, ht.trans_time, ht.status, ht.notif_seen FROM `htrans` ht JOIN `users` u ON ht.id_user = u.id JOIN shipping s ON ht.id_shipping = s.shipping_id WHERE `id_user` = (SELECT id FROM users WHERE username = :currUsername);";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindValue(":currUsername", $_SESSION['currUsername']);
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
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <title>History User</title>
    <link rel="icon" href="assets/img/Logo/favicon.ico">
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Fontawesome core CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
    <!--GOOGLE FONT -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <!--Slide Show Css -->
    <link href="assets/ItemSlider/css/main-style.css" rel="stylesheet" />
    <!-- custom CSS here -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        .notifNew{
            background-color: lightgreen;
        }
    </style>
</head>
<style>
    body,html{
        margin:0;
        padding:0;
    }
    li, a{
        color: white;
    }
</style>
<body>
<nav style="background-color:#88E0EF;border:none; border-bottom:3px solid gray;" class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="logo" style="width: 10vw;">
                    <a href="index.php"><img style="margin-top:10px; margin-bottom:10px;" src="./assets/img/Logo/logoweb.png" width="173px" height="70px" alt=""></a>
                </div>

                <button type="button" style="margin-top:-6vh;" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div style="margin-top: 2vh;" class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul style="margin-top: 5px;" class="nav navbar-nav navbar-right">
                    <?php
                    if (!isset($_SESSION['currUser'])) {
                    ?>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Signup</a></li>
                    <?php
                    } else {
                    ?>
                        
                        <li><a href="profile.php">Hai, <?=$_SESSION['currUsername']?></a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <?php
                    }
                        ?>
                </ul>
                <form class="navbar-form navbar-right" role="search" method="get">
                    <!-- <input type="hidden" name="categoryFilter" value="<?= $_GET['categoryFilter'] ?? "none" ?>">
                    <input type="hidden" name="sort" value="<?= $_GET['sort'] ?? "none" ?>"> -->
                    <?= (isset($_GET['categoryFilter']) ? '<input type="hidden" name="categoryFilter" value="' . $_GET['categoryFilter'] . '">' : "") ?>
                    <?= (isset($_GET['sort']) ? '<input type="hidden" name="categoryFilter" value="' . $_GET['sort'] . '">' : "") ?>
                    
                    <div class="form-group">
                        <input type="text" name="searchKey" placeholder="Enter Keyword Here ..." class="form-control">
                    </div>
                    &nbsp;
                    <button style="margin-top: 5px;" type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <br>
    <a href="index.php"><div style="margin-left: 10vw;" class="btn btn-dark">Back to Home</div></a>
    <h3 style="margin-left: 10vw; margin-top:2vh;">History</h3>
    <br>
    <table class="table" style="margin-left: 10vw; width:80vw;">
        <thead style="background-color: gray; color:white;">
            <tr>
                <th style="text-align: center;">No.</th>
                <th style="text-align: center;">Order Id</th>
                <th style="text-align: center;">Shipping Name</th>
                <th style="text-align: center;">Transaction Timestamp</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Items</th>
            </tr>
        </thead>
        <tbody>
            <?php $histCtr = 0;?>
            <?php foreach ($history as $histRow) {?>
                <tr <?= ($histRow['notif_seen'] == 0) ? 'class="notifNew"' : "" ?>>
                    <td style="text-align: center;"><?= ++$histCtr?></td>
                    <td style="text-align: center;"><?= $histRow['mid_order_id']?></td>
                    <td style="text-align: center;"><?= $histRow['ship_name']?></td>
                    <td style="text-align: center;"><?= $histRow['trans_time']?></td>
                    <?php if($histRow['status']== "pending" || $histRow['status']== "attempted" || $histRow['status']== "challenge"){?>
                            <td style="text-transform: capitalize; text-align:center; background-color: #fff3cd;"><?=$histRow['status']?></td>
                        <?php
                            }
                            else if($histRow['status']== "Success" || $histRow['status']== "Settlement"){
                        ?>
                            <td style="background-color: #d1e7dd; text-transform: capitalize; text-align:center;"><?=$histRow['status']?></td>
                        <?php
                            }
                            else if($histRow['status']== "cancel" || $histRow['status']== "deny" || $histRow['status']== "expire"){
                        ?>
                            <td style="background-color: #f8d7da; text-transform: capitalize; text-align:center;"><?=$histRow['status']?></td>
                        <?php
                            }
                        ?>
                    <td>
                        <!-- <button class="showItems" transid='<?= $histRow['id_htrans']?>'>Show Items</button> -->
                        <form action="#" method="post">
                            <input type="hidden" name="order_id" value="<?= $histRow['mid_order_id']?>">
                            <input  style="width:99%; background-color:gray; color:white;" type="submit" value="Show Items">
                        </form>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>

    <br><br><br><br>

    <?php if (isset($order_id)) {?>
        <div style="margin-left: 10vw;"><h3>Order ID = <?= $histItems[0]['id_htrans']?></h3></div>
        <table class="table" style="margin-left: 10vw; width:80vw;">
            <thead style="background-color: gray; color:white;">
                <tr>
                    <th style="text-align: center;">No.</th>
                    <th style="text-align: center;">Nama Ikan</th>
                    <th style="text-align: center;">Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: center;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0;?>
                <?php $histCtr = 0;?>
                <?php foreach ($histItems as $histRow) {?>
                    <tr>
                        <td style="text-align: center;"><?= ++$histCtr?></td>
                        <td style="text-align: center;"><?= $histRow['name']?></td>
                        <td style="text-align: center;">Rp. <?= $histRow['price']?></td>
                        <td style="text-align: center;"><?= $histRow['qty']?></td>
                        <td style="text-align: center;">Rp. <?= $histRow['price'] * $histRow['qty']?></td>
                    </tr>
                    <?php $total += $histRow['price'] * $histRow['qty']?>
                <?php }?>
            </tbody>
        </table>
        <div style="margin-left: 10vw;"> <h3>Total = Rp. <?= $total?></h3></div>
    <?php }?>
</body>
<script src="./assets/bootstrap5/bootstrap-5.1.3-dist/js/bootstrap.min.js"></script>
</html>

<?php
    // update seen notif
    try {
        $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE `htrans` SET `notif_seen` = 1 WHERE `notif_seen` = 0;";
        $stmt = $conn -> prepare($sql);
        $succInsert = $stmt->execute();

    } catch(\PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn=null;
?>