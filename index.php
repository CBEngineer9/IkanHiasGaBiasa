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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
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
                    <img style="margin-top:10px; margin-bottom:10px;" src="./assets/img/Logo/logoweb.png" width="173px" height="70px" alt="">


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
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="well well-lg offer-box text-center">
                    Welcome, Dear Customer!
                </div>
                <div class="main box-border">
                    <div id="mi-slider" class="mi-slider">
                        <ul>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan1.png" alt="img01">
                                    <h4>Penguin</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan2.png" alt="img02">
                                    <h4>Discus</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan3.png" alt="img03">
                                    <h4>Guppy</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan4.png" alt="img04">
                                    <h4>Cupang</h4>
                                </a></li>
                        </ul>
                        <ul>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan5.png" alt="img05">
                                    <h4>Komet</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan6.png" alt="img06">
                                    <h4>Louhan</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan7.png" alt="img07">
                                    <h4>Koki</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan8.png" alt="img08">
                                    <h4>Koi</h4>
                                </a></li>
                        </ul>
                        <ul>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan12.png" alt="img12">
                                    <h4>Arwana</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan13.png" alt="img13">
                                    <h4>Neon Tetra</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan14.png" alt="img14">
                                    <h4>Mengbadut</h4>
                                </a></li>
                            <li><a href="#">
                                    <img src="assets/ItemSlider/images/ikan15.png" alt="img15">
                                    <h4>Tetra</h4>
                                </a></li>
                        </ul>
                        <nav>
                            <a href="#">Ikan Biru</a>
                            <a href="#">Ikan Bawa Hoki</a>
                            <a href="#">Ikan Agak RGB</a>
                        </nav>
                    </div>

                </div>
                <br />
            </div>
            <!-- /.col -->

            <div class="col-md-3 text-center">
                <div class=" col-md-12 col-sm-12 col-xs-12">
                    <div class="thumbnail product-box">
                        <img src="assets/img/ikan/ikan5.png" alt="" />
                        <div class="caption">
                            <h3><a href="#">Ikan Komet</a></h3>
                            <p><a href="#">Ikan Komet</a></p>
                        </div>
                    </div>
                </div>
                <div class=" col-md-12 col-sm-12 col-xs-12">

                    <div class="thumbnail product-box">
                        <img src="assets/img/ikan/ikan7.png" alt="" />
                        <div class="caption">
                            <h3><a href="#">Ikan Koki </a></h3>
                            <p><a href="#">Ikan Mas Koki</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-md-3">
                <div>
                    <a href="#" class="list-group-item active">Categories
                    </a>
                    <ul class="list-group">

                        <?php foreach ($qresult2 as $row) { ?>
                            <?php if ($row['cat_name'] != 'dummycat') { ?>
                                <li class="list-group-item clickable" onclick="addCategoryFilter('<?= $row['cat_name'] ?>')">
                                    <?= $row['cat_name'] ?>
                                    <span class="label label-primary pull-right"><?= $row['cat_count'] ?></span>
                                    <!-- TODO coloring -->
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
                <!-- <div>
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-success"><a href="#">New Offer's Coming </a></li>
                        <li class="list-group-item list-group-item-info"><a href="#">New Products Added</a></li>
                        <li class="list-group-item list-group-item-warning"><a href="#">Ending Soon Offers</a></li>
                        <li class="list-group-item list-group-item-danger"><a href="#">Just Ended Offers</a></li>
                    </ul>
                </div> -->
                <!-- /.div -->
                <!-- <div class="well well-lg offer-box offer-colors">


                    <span class="glyphicon glyphicon-star-empty"></span>25 % off  , GRAB IT                 
              
                   <br />
                    <br />
                    <div class="progress progress-striped">
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                            style="width: 70%">
                            <span class="sr-only">70% Complete (success)</span>
                            2hr 35 mins left
                        </div>
                    </div>
                    <a href="#">click here to know more </a>
                </div> -->
                <!-- /.div -->
            </div>
            <!-- /.col -->
            <div class="col-md-9" id="ikanSearchDisplay">
                <div>
                    <ol class="breadcrumb">
                        <?php if ($categoryFilter != 'category.cat_name') { ?>
                            <li><a href="./index.php">Home</a></li>
                            <li class="active"><?= substr($categoryFilter, 1, strlen($categoryFilter) - 2) ?></li>
                        <?php } else { ?>
                            <li class="active">Home</li>
                        <?php } ?>
                    </ol>
                </div>
                <!-- /.div -->
                <div class="row">
                    <div class="btn-group alg-right-pad">
                        <button type="button" class="btn btn-default"><strong><?= $itemCount ?> </strong>items</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                                Sort Products &nbsp;
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a onclick="addSort('price asc')">By Price Low</a></li>
                                <li class="divider"></li>
                                <li><a onclick="addSort('price desc')">By Price High</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row" id="ikanRow0">
                    <div class="col-md-6 text-center col-sm-6 col-xs-6" id="templateCard" style="display:none;">
                        <div class="thumbnail product-box">
                            <img style="height:15vh;" src="assets/img/ikan/ikan3.png" alt="" />
                            <div class="caption">
                                <h3><a href="#">Samsung Galaxy </a></h3>
                                <p>Price : <strong>$ 3,45,900</strong> </p>
                                <p><a href="#">Ptional dismiss button </a></p>
                                <p>Ptional dismiss button in tional dismiss button in </p>
                                <p><button type="submit" value="See Details" name="btDetail"></button></p>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                    <!-- <div class="col-md-6 text-center col-sm-6 col-xs-6">
                        <div class="thumbnail product-box">
                            <img style="height:15vh;" src="assets/img/ikan/ikan2.png" alt="" />
                            <div class="caption">
                                <h3><a href="#">Samsung Galaxy </a></h3>
                                <p>Price : <strong>$ 3,45,900</strong>  </p>
                                <p><a href="#">Ptional dismiss button </a></p>
                                <p>Ptional dismiss button in tional dismiss button in   </p>
                                <p><a href="#" class="btn btn-success" role="button">Add To Cart</a> <a href="#" class="btn btn-primary" role="button">See Details</a></p>
                            </div>
                        </div>
                    </div> -->
                    <!-- /.col -->
                </div>
                <!-- Second Row -->
                <div class="row" id="ikanRow1">
                    <!-- <div class="col-md-6 text-center col-sm-6 col-xs-6">
                        <div class="thumbnail product-box">
                            <img style="height:15vh;" src="assets/img/ikan/ikan10.png" alt="" />
                            <div class="caption">
                                <h3><a href="#">Samsung Galaxy </a></h3>
                                <p>Price : <strong>$ 3,45,900</strong>  </p>
                                <p><a href="#">Ptional dismiss button </a></p>
                                <p>Ptional dismiss button in tional dismiss button in   </p>
                                <p><a href="#" class="btn btn-success" role="button">Add To Cart</a> <a href="#" class="btn btn-primary" role="button">See Details</a></p>
                            </div>
                        </div>
                    </div> -->
                    <!-- /.col -->
                    <!-- <div class="col-md-6 text-center col-sm-6 col-xs-6">
                        <div class="thumbnail product-box">
                            <img style="height:15vh;" src="assets/img/ikan/ikan13.png" alt="" />
                            <div class="caption">
                                <h3><a href="#">Samsung Galaxy </a></h3>
                                <p>Price : <strong>$ 3,45,900</strong>  </p>
                                <p><a href="#">Ptional dismiss button </a></p>
                                <p>Ptional dismiss button in tional dismiss button in   </p>
                                <p><a href="#" class="btn btn-success" role="button">Add To Cart</a> <a href="#" class="btn btn-primary" role="button">See Details</a></p>
                            </div>
                        </div>
                    </div> -->
                    <!-- /.col -->
                </div>
                <div class="row" id="ikanRow2">
                    <div class="col-md-6 text-center col-sm-6 col-xs-6">
                        <div class="thumbnail product-box">
                            <img style="height:15vh;" src="assets/img/ikan/ikan16.png" alt="" />
                            <div class="caption">
                                <h3><a href="#">Samsung Galaxy </a></h3>
                                <p>Price : <strong>$ 3,45,900</strong></p>
                                <p><a href="#">Ptional dismiss button </a></p>
                                <p>Ptional dismiss button in tional dismiss button in </p>
                                <form action="" method="post">
                                    <p><input type="submit" name="btDetail" value="See Details" class="btn btn-primary" role="button"></p>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                    <!-- <div class="col-md-6 text-center col-sm-6 col-xs-6">
                        <div class="thumbnail product-box">
                            <img style="height:15vh;" src="assets/img/ikan/ikan4.png" alt="" />
                            <div class="caption">
                                <h3><a href="#">Samsung Galaxy </a></h3>
                                <p>Price : <strong>$ 3,45,900</strong>  </p>
                                <p><a href="#">Ptional dismiss button </a></p>
                                <p>Ptional dismiss button in tional dismiss button in   </p>
                                <p><a href="#" class="btn btn-success" role="button">Add To Cart</a> <a href="#" class="btn btn-primary" role="button">See Details</a></p>
                            </div>
                        </div>
                    </div> -->
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <ul class="pagination alg-right-pad" id="pagination">
                        <!-- <li><a onclick="gotoBoundaryPage('min')">&laquo;</a></li> -->
                        <?php for ($i = 0; $i < $pageCount; $i++) { ?>
                            <!-- <li><a href="#"><?= $i + 1 ?></a></li> -->
                            <!-- <li><a href="#ikanSearchDisplay" onclick="gotoPage(<?= $i + 1 ?>)"><?= $i + 1 ?></a></li> -->

                        <?php } ?>
                        <!-- <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li> -->
                        <!-- <li><a onclick="gotoBoundaryPage('max')">&raquo;</a></li> -->
                    </ul>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
    <div class="col-md-12 download-app-box text-center">
        </span>Shop Responsibly .
    </div>

    <!--Footer -->
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
                <strong>About Us</strong>
                <p>
                    <br>
                    Sussy fishy is an e-commerce website that is dedicated on <br> selling many kinds of fishes. <br>
                    Developer : Alexander Kevin - 220116902 - Andrew Anderson - 220116904

                </p>

                2021 https://ikanhiasgabiasa.masuk.web.id/ | All Right Reserved
            </div>
            <div class="col-md-4 social-box">
                <strong>We are Social </strong>
                <br>
                <br>
                <a href="#"><i class="fa fa-facebook-square fa-3x "></i></a>
                <a href="#"><i class="fa fa-twitter-square fa-3x "></i></a>
                <a href="#"><i class="fa fa-google-plus-square fa-3x c"></i></a>
                <a href="#"><i class="fa fa-linkedin-square fa-3x "></i></a>
                <a href="#"><i class="fa fa-pinterest-square fa-3x "></i></a>
                <p>
                    Follow us on Social Media!
                </p>
            </div>
        </div>
        <hr>
    </div>
    <!-- /.col -->
    <div class="col-md-12 end-box ">
        &copy; 2021 | &nbsp; All Rights Reserved | &nbsp; https://ikanhiasgabiasa.masuk.web.id/ | &nbsp; 24x7 support
    </div>
    <div class="notif" id="notif" style="display: none;">
        New Notification !
    </div>
    <!-- /.col -->
    <!--Footer end -->
    <!-- PHP passthrough form -->
    <form action="#" method="get" id="searchForm">
        <?= (isset($_GET['categoryFilter']) ? '<input type="hidden" id="categoryFilterInput" name="categoryFilter" value="' . $_GET['categoryFilter'] . '">' : "") ?>
        <?= (isset($_GET['sort']) ? '<input type="hidden" name="sort" id="sortInput" value="' . $_GET['sort'] . '">' : "") ?>
    </form>
    <!--Core JavaScript file  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!--bootstrap JavaScript file  -->
    <script src="assets/js/bootstrap.js"></script>
    <!--Slider JavaScript file  -->
    <script src="assets/ItemSlider/js/modernizr.custom.63321.js"></script>
    <script src="assets/ItemSlider/js/jquery.catslider.js"></script>
    <script>
        $(function() {

            $('#mi-slider').catslider();

        });

        var searchKey = `<?= $_GET['searchKey'] ?? "none" ?>`;
        var categoryFilter = `<?= $_GET['categoryFilter'] ?? "none" ?>`;
        var sort = `<?= $_GET['sort'] ?? "none" ?>`;
        var isLoggedIn = <?= isset($_SESSION['currUsername']) ? 'true' : 'false' ?>

        const qResultIkan = <?= $qResultEncoded ?>;
        var pageCount = Math.trunc(qResultIkan.length / 6) + 1;
        var currPage = 1;
        const MAX_PAGE = 5;
        var lastNotifCount = 0;
        var notifChecker;

        checkNotif();
        notifChecker = setInterval(() => {
            checkNotif();
        }, 5000);

        gotoPage(1);
        refreshPagination();

        function gotoPage(page) {
            // console.log(page);
            currPage = page;
            page--; // for indexing

            for (let i = 0; i < 3; i++) {
                $("#ikanRow" + i).empty();
            }

            for (let i = 0; i < 6; i++) {
                const ikan = qResultIkan[i % 6 + 6 * page];
                if (typeof ikan === 'undefined' || ikan === null) {
                    // variable is undefined or null
                    // console.log('takada');
                } else {
                    // console.log('ada');
                    const rownum = Math.trunc(i / 2);

                    //     `<div class="col-md-6 text-center col-sm-6 col-xs-6">
                    //         <div class="thumbnail product-box">
                    //             <img  style="height:15vh;" src="assets/img/ikan/ikan3.png" alt="" />
                    //             <div class="caption">
                    //                 <h3><a href="#">Samsung Galaxy </a></h3>
                    //                 <p>Price : <strong>$ 3,45,900</strong>  </p>
                    //                 <p><a href="#">Ptional dismiss button </a></p>
                    //                 <p>Ptional dismiss button in tional dismiss button in   </p>
                    //                 <p><a href="#" class="btn btn-success" role="button">Add To Cart</a> <a href="#" class="btn btn-primary" role="button">See Details</a></p>
                    //             </div>
                    //         </div>
                    //     </div>`

                    let divIkan = $("<div>")
                        .attr("id", "ikan" + ikan[0])
                        .addClass("col-md-6 text-center col-sm-6 col-xs-6")
                        .append(
                            $("<div>")
                            .addClass("thumbnail product-box")
                            .append(
                                $("<img>")
                                .css("height", "15vh")
                                .attr("src", ikan["imageLink"])
                            )
                            .append(
                                $("<div>")
                                .addClass("caption")
                                .append(
                                    $('<h3><a href="#">' + ikan['name'] + ' </a></h3>')
                                )
                                .append(
                                    $("<p>")
                                    .html("Price : <strong>RP." + ikan['price'] + "</strong>/" + ikan['satuan'])
                                )
                                .append(
                                    $("<p>")
                                    .html(ikan['description'])
                                )
                                .append(
                                    $("<p>")
                                    .append(
                                        $("<a>")
                                        .addClass("btn btn-success")
                                        .attr("role", "button")
                                        .text("Add To Cart")
                                        .click(function() {
                                            addToCart(ikan["id"]);
                                        })
                                    )
                                    .append(
                                        $("<a>")
                                        .click(function() {
                                            seeDetail(ikan["id"]);
                                        })
                                        .addClass("btn btn-primary")
                                        .attr("role", "button")
                                        .text("See Details")
                                    )
                                )
                            )
                        );

                    $("#ikanRow" + rownum).append(divIkan);
                }
            }

            refreshPagination();
        }

        function refreshPagination() {
            let pagination = document.getElementById('pagination');
            let minOnPagination = currPage - Math.floor(MAX_PAGE / 2);
            let maxOnPagination = currPage + Math.floor(MAX_PAGE / 2);

            $('#pagination').empty();

            $('#pagination').append(
                $('<li>').append(
                    $('<a>').click(
                        function() {
                            gotoBoundaryPage('min');
                        }
                    ).html('&laquo')
                )
            );

            if (minOnPagination > 1) {
                $('#pagination').append(
                    $('<li>').append(
                        $('<a>').click(
                            function() {
                                console.log('not implemented yet');
                            }
                        ).html('...')
                    )
                );
            }

            for (let i = minOnPagination; i <= maxOnPagination; i++) {
                if (i > 0 && i <= pageCount) {
                    $('#pagination').append(
                        $('<li>').append(
                            $('<a>').click(
                                function() {
                                    gotoPage(i);
                                }
                            ).html(i)
                        )
                    );
                }
            }

            if (maxOnPagination < pageCount) {
                $('#pagination').append(
                    $('<li>').append(
                        $('<a>').click(
                            function() {
                                console.log('not implemented yet');
                            }
                        ).html('...')
                    )
                );
            }

            $('#pagination').append(
                $('<li>').append(
                    $('<a>').click(
                        function() {
                            gotoBoundaryPage('max');
                        }
                    ).html('&raquo')
                )
            );

            // <li><a onclick="gotoBoundaryPage('min')">&laquo;</a></li>
        }

        function gotoBoundaryPage(page) {
            if (page == 'min') {
                gotoPage(1);
            } else if (page == 'max') {
                gotoPage(pageCount);
            }
        }

        function addCategoryFilter(category) {
            // let searchForm = document.getElementById("searchForm");
            // if (searchKey != 'none') {
            //     console.log(searchKey);
            //     let searchKeyInput = document.createElement("input");
            //     searchKeyInput.type = "hidden";
            //     searchKeyInput.name = "searchKey";
            //     searchKeyInput.value = searchKey;
            //     searchForm.append(searchKeyInput);
            // }
            let categoryFilterInput = document.getElementById("categoryFilterInput");
            if (typeof categoryFilterInput === 'undefined' || categoryFilterInput === null) {
                categoryFilterInput = document.createElement("input");
                categoryFilterInput.type = "hidden";
                categoryFilterInput.name = "categoryFilter";
            }
            categoryFilterInput.value = category;
            searchForm.append(categoryFilterInput);

            searchForm.submit();
        }

        function addSort(sortBy) {
            let sortInput = document.getElementById("sortInput");
            if (typeof sortInput === 'undefined' || sortInput === null) {
                sortInput = document.createElement("input");
                sortInput.type = "hidden";
                sortInput.name = "sort";
            }
            sortInput.value = sortBy;
            searchForm.append(sortInput);

            searchForm.submit();
        }


        function seeDetail(ikan_id) {
            // chekc login
            if (!isLoggedIn) {
                alert("Please login first");
            } else {
                window.location.href = "detail.php?ikan_id=" + ikan_id;
            }
        }

        function addToCart(ikan_id) {
            $.ajax({
                type: "get",
                url: "./cart/cart_controller.php",
                data: {
                    'action': 'addItem',
                    'ikan_id': ikan_id,
                },
                success: function(response) {
                    if (response == "not_logged_in") {
                        alert('Please Login first');
                    } else if (response == 'empty') {
                        alert('Stock is Empty');
                    } else if (response == 'success') {
                        alert('Item successfuly added to cart');
                    } else {
                        alert("Failed to add to cart");
                    }
                },
                error: function(response) {
                    alert("AJAX ERROR " + response);
                }
            });
        }

        function checkNotif() {
            $.ajax({
                type: "get",
                url: "index_controller.php",
                data: {
                    'action': 'checkNotif',
                },
                success: function(response) {
                    if (response > 0) {
                        $('#histNotifBadge').text(response);
                        if (response > lastNotifCount) {
                            $('#notif').show(1, 'linear', function() {
                                setTimeout(() => {
                                    $('#notif').fadeOut(3000);
                                }, 5000);
                            });
                            lastNotifCount = response;
                        }
                    }
                },
                error: function(response) {
                    alert("AJAX ERROR " + response);
                }
            });
        }

        // history.pushState('data to be passed', 'Title', '/test');

        // window.onpopstate = function(e){
        //     if(e.state){
        //         // document.getElementById("content").innerHTML = e.state.html;
        //         console.log(e.state.html);
        //         document.title = e.state.pageTitle;
        //     }
        // };
    </script>
</body>

</html>