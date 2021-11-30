<?php
require_once("proyekpw_lib.php");

$page = 1;
$pageCount = 0;

$searchKey = "";
$sort = "";
$categoryFilter = "category.cat_name";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['searchKey']) && $_GET['searchKey'] != 'none') {
        $searchKey = $_GET['searchKey'];
    }
    if (isset($_GET['categoryFilter']) && $_GET['categoryFilter'] != 'none') {
        $categoryFilter = "'" . $_GET['categoryFilter'] . "'";
    }
    if (isset($_GET['sort']) && $_GET['sort'] != 'none') {
        $sort = $_GET['sort'];
    }
}
if (isset($_REQUEST["btLogout"])) {
    $_SESSION['currUser'] = -1;
}
if (isset($_REQUEST["btDetail"])) {
    header("Location: detail.php");
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlIkan = "SELECT ikan.id, ikan.name, ikan.stock, ikan.price, ikan.imageLink, ikan.description, ikan.satuan , category.cat_name FROM `ikan` JOIN category ON ikan.cat_id = category.cat_id WHERE ikan.isActive = '1' AND category.cat_name = " . $categoryFilter . " AND ikan.name LIKE :searchKey";
    if ($sort != "") {
        $sqlIkan .= " ORDER BY " . $sort;
    }
    $sqlIkan .= ";";

    $stmt = $conn->prepare($sqlIkan);
    $stmt->bindValue(":searchKey", '%' . $searchKey . '%');
    // $stmt -> bindParam(":catFilter",$categoryFilter); // TODO

    // echo $categoryFilter;

    $stmt->execute();
    $qResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $itemCount = count($qResult);
    $pageCount = intdiv($itemCount, 6) + 1;
    $qResultEncoded = json_encode($qResult);

    $sql2 = "SELECT c.* , coalesce(cc.cat_count,0) AS cat_count  FROM category c LEFT JOIN (SELECT i.cat_id as category_id , COUNT(i.cat_id) AS 'cat_count' FROM ikan i GROUP BY i.cat_id) cc ON cc.category_id = c.cat_id;";
    $stmt2 =  $conn->prepare($sql2);
    $stmt2->execute();
    $qresult2 = $stmt2->fetchAll();

    // echo "<pre>";
    // foreach ($qResult as $baris) {
    //     print $baris["id"] . "\t";
    //     print $baris["name"] . "\t";
    //     print $baris["cat_name"] . "\t";
    //     print $baris["stock"] . "\t";
    //     print $baris["price"] . "\t";
    //     print $baris["imageLink"] . "\t";
    //     print $baris["description"] . "\t";
    // }
    // echo "</pre>";

    // echo "fetched successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    // alert("Connection failed: " . $e->getMessage());
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SussyFishy</title>
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
    <style>
        .jumbotron {
            background-image: url('./assets/img/banner/thumb-1920-121336.jpg');
            background-repeat: no-repeat;
            height: 50vh;
            opacity: 0.7;
            color: white;

        }

        .asd {
            text-align: center;
            border-bottom: 1px solid black;
            width: 40vw;
            margin: auto;
        }

    </style>
    <div class="jumbotron" style="margin-top: -2.2vh;">
        <h1 class="display-4">About Us</h1>
        <br>
        <h2>We are sure that this website is not suspicious and definitely trusted!</h2>
        <br>
        <p class="lead">SussyFishy is an E-Commerce that is dedicated for fish. We want to ensure our customer <br> get the best experience by using our platform. We use secure payment system and the <br> best data encryption system to protect our customers</p>
    </div>
    <br>
    <div class="asd">
        <p style="font-size: 3em;">Our Mission</p>
    </div>
    <br>
    <br>
    <div class="konten" style="height: 35vh;">
       <p style="text-align: center; font-size:1.5em;"> At SussyFishy, we believe there is a better way to shop for fishes. <br> A more efficient, less inefective way of shopping are bought for our customers. <br> We're obsessively passionate about it and we will do anything <br> to achieve our goal. We focus on dealing with the safety of <br> each delivery and most of all customer experience. We're <br> excited to make people happy by shopping through our platform.</p>
    </div>
    <div class="col-md-12 footer-box">


        <!-- <div class="row small-box ">
            <strong>Mobiles :</strong> <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> | 
            <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |
              <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |  <a href="#">Sony</a> | 
            <a href="#">Microx</a> |<a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |
            <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |  
            <a href="#">Sony</a> | <a href="#">Microx</a> | view all items
         
        </div>
        <div class="row small-box ">
            <strong>Laptops :</strong> <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx Laptops</a> | 
            <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |
              <a href="#">Sony Laptops</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |  <a href="#">Sony</a> | 
            <a href="#">Microx</a> |<a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |
            <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |  
            <a href="#">Sony</a> | <a href="#">Microx</a> | view all items
        </div>
        <div class="row small-box ">
            <strong>Tablets : </strong><a href="#">samsung</a> |  <a href="#">Sony Tablets</a> | <a href="#">Microx</a> | 
            <a href="#">samsung </a>|  <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |
              <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung Tablets</a> |  <a href="#">Sony</a> | 
            <a href="#">Microx</a> |<a href="#">samsung Tablets</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |
            <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |  
            <a href="#">Sony</a> | <a href="#">Microx Tablets</a> | view all items
            
        </div>
        <div class="row small-box pad-botom ">
            <strong>Computers :</strong> <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> | 
            <a href="#">samsung Computers</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |
              <a href="#">Sony</a> | <a href="#">Microx</a> |<a href="#">samsung</a> |  <a href="#">Sony</a> | 
            <a href="#">Microx Computers</a> |<a href="#">samsung Computers</a> |  <a href="#">Sony</a> | <a href="#">Microx</a> |
            <a href="#">samsung</a> |  <a href="#">Sony</a> | <a href="#">Microx Computers</a> |<a href="#">samsung</a> |  
            <a href="#">Sony</a> | <a href="#">Microx</a> | view all items
            
        </div> -->
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