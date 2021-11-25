<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
<nav style="background-color:#88E0EF;border:none; border-bottom:3px solid gray;" class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="logo" style="width: 10vw;">
                    <img style="margin-top:10px; margin-bottom:10px;" src="./assets/img/Logo/logoweb.png" width="173px" height="70px" alt=""> 
                    
                    
                </div>
                
                <button  type="button" style="margin-top:-6vh;" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div style="margin-top: 2vh;"  class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul style="margin-top: 5px;" class="nav navbar-nav navbar-right">
                    <?php
                        if(!isset($_SESSION['currUser'])){
                    ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Signup</a></li>
                    <?php
                        } else {
                    ?>
                        <!-- <li><button type="submit" style="border: none; background-color:transparent; color:white; display:inline-block;" name="btLogout">Logout</button></li> -->
                        <li><a href=""> Hai, <?=$_SESSION['currUsername']?>!</a></li>
                        <li><a href="user_history.php">History <span class="badge" id="histNotifBadge"></span></a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="./cart"><img src="assets/img/icon/cart-2-24.png" alt=""></a></li>
                    <?php
                        }
                    ?>
                </ul>
                <form class="navbar-form navbar-right" role="search" method="get">
                    <!-- <input type="hidden" name="categoryFilter" value="<?= $_GET['categoryFilter'] ?? "none" ?>">
                    <input type="hidden" name="sort" value="<?= $_GET['sort'] ?? "none" ?>"> -->
                    <?= ( isset($_GET['categoryFilter']) ? '<input type="hidden" name="categoryFilter" value="'. $_GET['categoryFilter'] .'">' : "") ?>
                    <?= ( isset($_GET['sort']) ? '<input type="hidden" name="categoryFilter" value="'. $_GET['sort'] .'">' : "") ?>
                </form>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <div class="container" style="margin-top: 15vh; border:1px solid lightgray; width:90vw;">
    <br>
    <br>
        <h1 style="text-align: center;">Payment Successful!</h1>
        <img style="width: 80px; height:50px; margin-left:31vw;" src="./assets/img/icon/check-correct.gif" alt="">
        <div class="sm" style="margin-left:25vw;">
        <br>
            <span style="font-weight:bold">Amount</span> <span style="margin-left:10vw;">Rp. 100000</span>
            <br>
            <br>
            <span style="font-weight:bold">Payment Method</span> <span style="margin-left:6.5vw;">Virtual Account</span>
            <br>
            <br>
            <span style="font-weight:bold">Date</span> <span style="margin-left:11.5vw;">25/11/2021</span>
            <br>
            <br>
            <span style="font-weight:bold">Transaction ID </span> <span style="margin-left:7.8vw;">ID001</span>
            <br>
            <br>
            <br>
            <br>
        </div>
        <h1 style="text-align: center;">Thank You!</h1>
        <br>
        <br>
        <br>
    </div>
    <a href="index.php"><div style="margin-top: 4vh; margin-left:15.5vw;" class="btn btn-success">Back to Home</div></a>
</body>
</html>