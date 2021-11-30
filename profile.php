<?php
require_once("proyekpw_lib.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
</head>

<body>
    <nav style="background-color:#88E0EF;border:none; border-bottom:3px solid gray;" class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="logo" style="width: 10vw;">
                    <a href="./">
                        <img style="margin-top:10px; margin-bottom:10px;" src="./assets/img/Logo/logoweb.png" width="173px" height="70px" alt="">
                    </a>
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

                        <li><a href="profile.php"> Hai, <?= $_SESSION['currUsername'] ?>!</a></li>
                        <li><a href="about.php">About Us</a></li>
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
    <div class="row row-equal-height" style="margin:auto;">
        <div style="border:1px solid black;" class="col-xs-4" id="left">
            <p>short</p>
        </div>
        <div style="border:1px solid black;" class="col-xs-4" id="right">
            <p>long</p>
            <p>long</p>
            <p>long</p>
            <p>long</p>
            <p>long</p>
        </div>
    </div>
</body>

</html>