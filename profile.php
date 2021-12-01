<?php
    require_once("proyekpw_lib.php");
    try {
        $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $editMode = false;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['edit'])) {
                $editMode = true;
            }
            if (isset($_POST['confEdit'])) {
                $email = $_POST['email'];
                $fname = $_POST['fname'];
                $lname = $_POST['lname'];
                $phone = $_POST['phone'];

                $sql = "UPDATE `users` SET `email`=:email,`firstname`=:fname,`lastname`=:lname,`phone`=:phone WHERE id = :userid;";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(":userid", $_SESSION['currUser']);
                $stmt->bindValue(":email", $email);
                $stmt->bindValue(":fname", $fname);
                $stmt->bindValue(":lname", $lname);
                $stmt->bindValue(":phone", $phone);
                $succUpdate = $stmt->execute();

                alert('update success');
            }
        }
        
        $sql = "SELECT * FROM `users` WHERE `id` = :currUser;";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":currUser", $_SESSION['currUser']);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT * FROM `htrans` WHERE `id_user` = :currUser ORDER BY trans_time desc LIMIT 10;";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":currUser", $_SESSION['currUser']);
        $stmt->execute();
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn = null;
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
<style>
    html,body{
        padding: 0;
        height: 100vh;
    }
    body{
        display: flex;
        flex-direction: column;
    }
    .header{
        background-color: #88E0EF;
        padding: 15px;
        border-radius: 10px 10px 0px 0px;
        font-size: 1.5em;
    }
    .header-bottom{
        background-color: #88E0EF;
        padding: 15px;
        border-radius: 0px 0px 10px 10px;
        font-size: 1.5em;
    }
    .details{
        padding: 15px;
        border: 1px solid lightgray;
        font-size: 1.2em;
    }
    .content-holder{
        flex-grow: 2;
    }
</style>
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
                <form action="index.php#ikanSearchDisplay" class="navbar-form navbar-right" role="search" method="get">
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
    <div class="content-holder">
        <div class="content">
            <div class="row row-equal-height col-md-8" style="display: flex; padding:0; margin-left:15%;">
                <div  class="col-lg-8 col-sm-6 col-xs-6" id="left">
                    <?php if ($editMode) { ?>
                        <form action="#" method="post">
                            <div class="header">
                                Profile <input type="submit" name="confEdit" value="Confirm Edit">
                            </div>
                            <div class="details">
                                Username : <?=$_SESSION['currUsername']?>
                            </div>
                            <div class="details">
                                Email : <input type="text" name="email" id="email" value="<?= $userDetails['email']?>">
                            </div>
                            <div class="details">
                                First Name : <input type="text" name="fname" id="fname" value="<?= $userDetails['firstname']?>">
                            </div>
                            <div class="details">
                                Last Name : <input type="text" name="lname" id="lname" value="<?= $userDetails['lastname']?>">
                            </div>
                            <div class="details">
                                Phone : <input type="text" name="phone" id="phone" value="<?= $userDetails['phone']?>">
                            </div>
                        </form>
                    <?php } else { ?>
                        <div class="header">
                            <form action="#" method="post">
                                Profile <input type="submit" name="edit" value="Edit">
                            </form>
                        </div>
                        <div class="details">
                            Username : <?=$_SESSION['currUsername']?>
                        </div>
                        <div class="details">
                            Email : <?= $userDetails['email']?>
                        </div>
                        <div class="details">
                            First Name : <?= $userDetails['firstname']?>
                        </div>
                        <div class="details">
                            Last Name : <?= $userDetails['lastname']?>
                        </div>
                        <div class="details">
                            Phone : <?= $userDetails['phone']?>
                        </div>
                    <?php } ?>
                    <div class="header-bottom">
                    </div>
        
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" id="right" style="display: none;">
                    <div class="header">
                        Transactions
                    </div>
                    <?php foreach ($history as $histRow) { ?>
                        <div class="details">
                            <?= $histRow['mid_order_id']?>
                        </div>
                    <?php } ?>
                    <div class="header-bottom">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 footer-box">
        <div class="row">
            <!-- <div class="col-md-4">
                <strong>Send a Quick Query </strong>
                <hr>
                <form>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" required="required" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" required="required" placeholder="Email address">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <textarea name="message" id="message" required="required" class="form-control" rows="3" placeholder="Message"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div> -->

            <div class="col-md-4">
                <strong>Hi!</strong>
                <p>
                    Sussy fishy is an e-commerce website that is dedicated on <br> selling many kinds of fishes. <br>
                    Developed by : Alexander Kevin - 220116902 - Andrew Anderson - 220116904

                </p>

                2021 https://ikanhiasgabiasa.masuk.web.id/ | All Right Reserved
            </div>
            <div class="col-md-4 social-box">
                <strong>We are Social </strong>
                <br>
                <br>
                <a href="https://www.facebook.com/"><i class="fa fa-facebook-square fa-3x "></i></a>
                <a href="https://www.twitter.com/"><i class="fa fa-twitter-square fa-3x "></i></a>
                <a href="https://www.google.com/"><i class="fa fa-google-plus-square fa-3x "></i></a>
                <a href="https://www.linkedin.com/"><i class="fa fa-linkedin-square fa-3x "></i></a>
                <a href="https://www.pinterest.com/"><i class="fa fa-pinterest-square fa-3x "></i></a>
                <p>
                    Follow us on Social Media!
                </p>
            </div>
        </div>
        <hr>
    </div>
    <div class="col-md-12 end-box">
        &copy; 2021 | &nbsp; All Rights Reserved | &nbsp; https://ikanhiasgabiasa.masuk.web.id/ | &nbsp; 24x7 support
    </div>
</body>

</html>