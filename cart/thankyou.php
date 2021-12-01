<?php
    require_once("../proyekpw_lib.php");

    // $response = json_decode($_GET['response'],true);
    $amount = $_GET['amount'];
    $payment_type = $_GET['payment_type'];
    $transaction_time = $_GET['transaction_time'];
    $order_id = $_GET['order_id'];
    $type = $_GET['type'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="../assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <title>Document</title>
</head>
<body>
<nav style="background-color:#88E0EF;border:none; border-bottom:3px solid gray; width:100vw;" class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
               <div class="logo" style="width: 10vw;">
               <a href="../index.php"><img style="margin-top:10px; margin-bottom:10px;" src="../assets/img/Logo/logoweb.png" width="173px" height="70px" alt=""> </a> 
                </div>

            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div style="margin-top: 2vh;"  class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul style="margin-top: 5px;" class="nav navbar-nav navbar-right">
                    <?php
                        if(!isset($_SESSION['currUsername'])){
                    ?>
                        <li><a href="../login.php">Login</a></li>
                        <li><a href="../register.php">Signup</a></li>
                    <?php
                        } else {
                    ?>
                        <!-- <li><button type="submit" style="border: none; background-color:transparent; color:white; display:inline-block;" name="btLogout">Logout</button></li> -->
                        <li><a href=""> Hai, <?=$_SESSION['currUsername']?>!</a></li>
                        <li><a href="../user_history.php">History</a></li>
                        <li><a href="../logout.php">Logout</a></li>
                        <li><a href="./"><img src="../assets/img/icon/cart-2-24.png" alt=""></a></li>
                    <?php
                        }
                    ?>
                </ul>
                <form class="navbar-form navbar-right" role="search" method="get">
                    <!-- <input type="hidden" name="categoryFilter" value="<?= $_GET['categoryFilter'] ?? "none" ?>">
                    <input type="hidden" name="sort" value="<?= $_GET['sort'] ?? "none" ?>"> -->
                    <?= ( isset($_GET['categoryFilter']) ? '<input type="hidden" name="categoryFilter" value="'. $_GET['categoryFilter'] .'">' : "") ?>
                    <?= ( isset($_GET['sort']) ? '<input type="hidden" name="categoryFilter" value="'. $_GET['sort'] .'">' : "") ?>
                    &nbsp; 
                </form>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <div class="container" style="margin-top: 15vh; border:1px solid lightgray; width:800px;">
    <br>
    <h1 style="text-align: center;">Payment <?= ($type=="success"?"Successful":"Pending")?> ! <img style="width: 80px; height:50px;"  src="../assets/img/icon/check-correct.gif" alt=""></h1>
        <div  style="margin-left:200px;">
        <br>
            <span style="font-weight:bold">Amount</span> <span style="margin-left:10vw;">Rp. <?= $amount?></span>
            <br>
            <br>
            <span style="font-weight:bold">Payment Method</span> <span style="margin-left:6.5vw;"><?= $payment_type?></span>
            <br>
            <br>
            <span style="font-weight:bold">Date</span> <span style="margin-left:11.5vw;"><?= $transaction_time?></span>
            <br>
            <br>
            <span style="font-weight:bold">Transaction ID </span> <span style="margin-left:7.8vw;"><?= $order_id?></span>
            <br>
            <br>
            <br>
            <br>
        </div>
        <h1 class="start-50" style="text-align: center;">Thank You!</h1>
        <a href="../index.php"><div style="margin-left:330px;" class="btn btn-success">Back to Home</div></a>
        </div>
</button>
        <br>
        <br>
        <br>
    </div>
</body>
</html>