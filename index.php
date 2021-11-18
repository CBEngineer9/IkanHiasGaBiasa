<?php
    require_once("proyekpw_lib.php");

    $page = 1;
    $pageCount = 0;
    
    $searchKey = "";
    $categoryFilter = "category.cat_name";
    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        if (isset($_GET['searchKey'])) {
            $searchKey = $_GET['searchKey'];
        }
        if (isset($_GET['categoryFilter'])) {
            $categoryFilter = "'".$_GET['categoryFilter']."'";
        }
    }
    if(isset($_REQUEST["btDetail"])){
        header("Location: detail.php");
    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sqlIkan = "SELECT ikan.id, ikan.name, ikan.stock, ikan.price, ikan.imageLink, ikan.description, ikan.satuan , category.cat_name FROM `ikan` JOIN category ON ikan.cat_id = category.cat_id WHERE ikan.isActive = '1' AND category.cat_name = ".$categoryFilter." AND ikan.name LIKE :searchKey";
        $sqlIkan .= ";";

        $stmt = $conn->prepare($sqlIkan);
        $stmt -> bindValue(":searchKey",'%'.$searchKey.'%');
        // $stmt -> bindParam(":catFilter",$categoryFilter); // TODO

        // echo $categoryFilter;
        
        $stmt -> execute();

        $qResult = $stmt->fetchAll();
        $pageCount = intdiv(count($qResult),6) + 1;
        $qResultEncoded = json_encode($qResult);

        $sql2 = "SELECT c.* , coalesce(cc.cat_count,0) AS cat_count  FROM category c LEFT JOIN (SELECT i.cat_id as category_id , COUNT(i.cat_id) AS 'cat_count' FROM ikan i GROUP BY i.cat_id) cc ON cc.category_id = c.cat_id;";
        $stmt2 =  $conn -> prepare($sql2);
        $stmt2 -> execute();
        $qresult2 = $stmt2 -> fetchAll();

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
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        // alert("Connection failed: " . $e->getMessage());
    }
    $conn=null;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SussyFishy</title>
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
                    <img style="margin-top:10px; margin-bottom:10px;" src="assets/img/Logo/tumblr_myvpf71CVu1spjmmdo1_1280.png" width="50px" height="50px" alt=""> <strong>NAMA WEBSITE</strong> 
                </div>
                <button  type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div style="margin-top: 5px;"  class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul style="margin-top: 5px;" class="nav navbar-nav navbar-right">
                    <li><a href="#">Track Order</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Signup</a></li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">24x7 Support <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><strong>Call: </strong>+09-456-567-890</a></li>
                            <li><a href="#"><strong>Mail: </strong>info@yourdomain.com</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><strong>Address: </strong>
                                <div>
                                    234, New york Street,<br />
                                    Just Location, USA
                                </div>
                            </a></li>
                        </ul>
                    </li>
                    <li><a href="cart.php"><img src="assets/img/icon/cart-2-24.png" alt=""></a></li>
                </ul>
                <form class="navbar-form navbar-right" role="search" method="get">
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


                   Today's Offer : &nbsp; <span class="glyphicon glyphicon-cog"></span>&nbsp;40 % off  on purchase of $ 2,000 and above till 24 dec !                
              
               
                </div>
                <div class="main box-border">
                    <div id="mi-slider" class="mi-slider">
                        <ul>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan1.png" alt="img01"><h4>Boots</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan2.png" alt="img02"><h4>Oxfords</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan3.png" alt="img03"><h4>Loafers</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan4.png" alt="img04"><h4>Sneakers</h4>
                            </a></li>
                        </ul>
                        <ul>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan5.png" alt="img05"><h4>Belts</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan6.png" alt="img06"><h4>Hats &amp; Caps</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan7.png" alt="img07"><h4>Sunglasses</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan8.png" alt="img08"><h4>Scarves</h4>
                            </a></li>
                        </ul>
                        <ul>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan9.png" alt="img09"><h4>Casual</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan10.png" alt="img10"><h4>Luxury</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan11.png" alt="img11"><h4>Sport</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan16.png" alt="img11"><h4>Sport</h4>
                            </a></li>
                        </ul>
                        <ul>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan12.png" alt="img12"><h4>Carry-Ons</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan13.png" alt="img13"><h4>Duffel Bags</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan14.png" alt="img14"><h4>Laptop Bags</h4>
                            </a></li>
                            <li><a href="#">
                                <img src="assets/ItemSlider/images/ikan15.png" alt="img15"><h4>Briefcases</h4>
                            </a></li>
                        </ul>
                        <nav>
                            <a href="#">Shoes</a>
                            <a href="#">Accessories</a>
                            <a href="#">Watches</a>
                            <a href="#">Bags</a>
                        </nav>
                    </div>
                    
                </div>
                <br />
            </div>
            <!-- /.col -->
            
            <div class="col-md-3 text-center">
                <div class=" col-md-12 col-sm-6 col-xs-6" >
                    <div class="offer-text">
                        30% off here
                    </div>
                    <div class="thumbnail product-box">
                        <img src="assets/img/ikan/ikan5.png" alt="" />
                        <div class="caption">
                            <h3><a href="#">Samsung Galaxy </a></h3>
                            <p><a href="#">Ptional dismiss button </a></p>
                        </div>
                    </div>
                </div>
                <div class=" col-md-12 col-sm-6 col-xs-6">
                    <div class="offer-text2">
                        30% off here
                    </div>
                    <div class="thumbnail product-box">
                        <img src="assets/img/ikan/ikan7.png" alt="" />
                        <div class="caption">
                            <h3><a href="#">Samsung Galaxy </a></h3>
                            <p><a href="#">Ptional dismiss button </a></p>
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

                        <?php foreach ($qresult2 as $row) {?>
                            <li class="list-group-item" onclick="addCategoryFilter('<?= $row['cat_name']?>')">
                                <?= $row['cat_name']?>
                                <span class="label label-primary pull-right"><?= $row['cat_count']?></span>
                                <!-- TODO coloring -->
                            </li>
                        <?php }?>
                        
                        <!-- <li class="list-group-item">Mobile
                            <span class="label label-primary pull-right">234</span>
                        </li>
                        <li class="list-group-item">Computers
                            <span class="label label-success pull-right">34</span>
                        </li>
                        <li class="list-group-item">Tablets
                            <span class="label label-danger pull-right">4</span>
                        </li>
                        <li class="list-group-item">Appliances
                            <span class="label label-info pull-right">434</span>
                        </li>
                        <li class="list-group-item">Games & Entertainment
                            <span class="label label-success pull-right">34</span>
                        </li> -->

                    </ul>
                </div>
                <!-- /.div -->
                <!-- <div>
                    <a href="#" class="list-group-item active list-group-item-success">Clothing & Wears
                    </a>
                    <ul class="list-group">

                        <li class="list-group-item">Men's Clothing
                             <span class="label label-danger pull-right">300</span>
                        </li>
                        <li class="list-group-item">Women's Clothing
                             <span class="label label-success pull-right">340</span>
                        </li>
                        <li class="list-group-item">Kid's Wear
                             <span class="label label-info pull-right">735</span>
                        </li>

                    </ul>
                </div> -->
                <!-- /.div -->
                <!-- <div>
                    <a href="#" class="list-group-item active">Accessaries & Extras
                    </a>
                    <ul class="list-group">
                        <li class="list-group-item">Mobile Accessaries
                             <span class="label label-warning pull-right">456</span>
                        </li>
                        <li class="list-group-item">Men's Accessaries
                             <span class="label label-success pull-right">156</span>
                        </li>
                        <li class="list-group-item">Women's Accessaries
                             <span class="label label-info pull-right">400</span>
                        </li>
                        <li class="list-group-item">Kid's Accessaries
                             <span class="label label-primary pull-right">89</span>
                        </li>
                        <li class="list-group-item">Home Products
                             <span class="label label-danger pull-right">90</span>
                        </li>
                        <li class="list-group-item">Kitchen Products
                             <span class="label label-warning pull-right">567</span>
                        </li>
                    </ul>
                </div> -->
                <!-- /.div -->
                <div>
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-success"><a href="#">New Offer's Coming </a></li>
                        <li class="list-group-item list-group-item-info"><a href="#">New Products Added</a></li>
                        <li class="list-group-item list-group-item-warning"><a href="#">Ending Soon Offers</a></li>
                        <li class="list-group-item list-group-item-danger"><a href="#">Just Ended Offers</a></li>
                    </ul>
                </div>
                <!-- /.div -->
                <div class="well well-lg offer-box offer-colors">


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
                </div>
                <!-- /.div -->
            </div>
            <!-- /.col -->
            <div class="col-md-9" id="ikanSearchDisplay">
                <div>
                    <ol class="breadcrumb">
                        <?php if ($categoryFilter != 'category.cat_name') {?>
                            <li><a href="#">Home</a></li>
                            <li class="active"><?= substr($categoryFilter,1,strlen($categoryFilter)-2)?></li>
                            <?php } else {?>
                            <li class="active">Home</li>
                        <?php } ?>
                    </ol>
                </div>
                <!-- /.div -->
                <div class="row">
                    <div class="btn-group alg-right-pad">
                        <button type="button" class="btn btn-default"><strong>1235  </strong>items</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                                Sort Products &nbsp;
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="#">By Price Low</a></li>
                                <li class="divider"></li>
                                <li><a href="#">By Price High</a></li>
                                <!-- <li class="divider"></li>
                                <li><a href="#">By Popularity</a></li>
                                <li class="divider"></li>
                                <li><a href="#">By Reviews</a></li> -->
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row" id="ikanRow0">
                    <div class="col-md-6 text-center col-sm-6 col-xs-6" id="templateCard" style="display:none;">
                        <div class="thumbnail product-box">
                            <img  style="height:15vh;" src="assets/img/ikan/ikan3.png" alt="" />
                            <div class="caption">
                                <h3><a href="#">Samsung Galaxy </a></h3>
                                <p>Price : <strong>$ 3,45,900</strong>  </p>
                                <p><a href="#">Ptional dismiss button </a></p>
                                <p>Ptional dismiss button in tional dismiss button in   </p>
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
                                <p>Ptional dismiss button in tional dismiss button in   </p>
                                <form action="" method="post">
                                    <p><input type="submit" name="btDetail" value="See Details" class="btn btn-primary"  role="button"></p>
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
                    <ul class="pagination alg-right-pad">
                        <li><a onclick="gotoBoundaryPage('min')">&laquo;</a></li>
                        <?php for ($i=0; $i < $pageCount; $i++) { ?>
                            <!-- <li><a href="#"><?= $i+1?></a></li> -->
                            <li><a href="#ikanSearchDisplay" onclick="gotoPage(<?= $i+1?>)"><?= $i+1?></a></li>
                            
                        <?php }?>
                        <!-- <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li> -->
                        <li><a onclick="gotoBoundaryPage('max')">&raquo;</a></li>
                    </ul>
                </div> 
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
    <div class="col-md-12 download-app-box text-center">

        <span class="glyphicon glyphicon-download-alt"></span>Download Our Android App and Get 10% additional Off on all Products . <a href="#" class="btn btn-danger btn-lg">DOWNLOAD  NOW</a>

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
            <div class="col-md-4">
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
            </div>

            <div class="col-md-4">
                <strong>Our Location</strong>
                <hr>
                <p>
                     234, New york Street,<br />
                                    Just Location, USA<br />
                    Call: +09-456-567-890<br>
                    Email: info@yourdomain.com<br>
                </p>

                2014 www.yourdomain.com | All Right Reserved
            </div>
            <div class="col-md-4 social-box">
                <strong>We are Social </strong>
                <hr>
                <a href="#"><i class="fa fa-facebook-square fa-3x "></i></a>
                <a href="#"><i class="fa fa-twitter-square fa-3x "></i></a>
                <a href="#"><i class="fa fa-google-plus-square fa-3x c"></i></a>
                <a href="#"><i class="fa fa-linkedin-square fa-3x "></i></a>
                <a href="#"><i class="fa fa-pinterest-square fa-3x "></i></a>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. La luce che tu dai Nel cuore resterà A ricordarci che L'eterna stella sei. Nella mia preghiera Quanta fede c'è
                </p>
            </div>
        </div>
        <hr>
    </div>
    <!-- /.col -->
    <div class="col-md-12 end-box ">
        &copy; 2021 | &nbsp; All Rights Reserved | &nbsp; www.yourdomain.com | &nbsp; 24x7 support | &nbsp; Email us: info@yourdomain.com
    </div>
    <!-- /.col -->
    <!--Footer end -->
    <!-- PHP passthrough form -->
    <form action="#" method="get" id="searchForm">
        <!-- <input id="searchKeyInput" type="hidden" name="searchKey" value=""> -->
        <!-- <input id="categoryInput" type="hidden" name="categoryFilter" value=""> -->
    </form>
    <!--Core JavaScript file  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!--bootstrap JavaScript file  -->
    <script src="assets/js/bootstrap.js"></script>
    <!--Slider JavaScript file  -->
    <script src="assets/ItemSlider/js/modernizr.custom.63321.js"></script>
    <script src="assets/ItemSlider/js/jquery.catslider.js"></script>
    <script>
        $(function () {

            $('#mi-slider').catslider();

        });

        var searchKey = `<?= $_GET['searchKey'] ?? "none" ?>`;
        // var searchKeyExist = <?= ( isset($_GET['searchKey'] ) ? 'true' : "false" )?>;
        // if (searchKeyExist == true) {
        //     location.hash = "#ikanSearchDisplay";
        //     var searchKey = ;
        // }
        var categoryFilter = `<?= $_GET['categoryFilter'] ?? "none" ?>`;
        // var isCategoryFilter = <?= ( isset($_GET['categoryFilter'] ) ? 'true' : "false" )?>;
        // if (isCategoryFilter == true) {
        //     var categoryFilter = ;
        // }

        const qResultIkan = <?=$qResultEncoded?>;
        var pageCount = Math.trunc(qResultIkan.length/6)+1;
        var currPage = 1;

        gotoPage(1);

        function gotoPage(page){
            // console.log(page);
            page--; // for indexing

            for (let i = 0; i < 3; i++) {
                $("#ikanRow"+i).empty();
            }

            for (let i = 0; i < 6; i++) {
                const ikan = qResultIkan[i%6 + 6*page];
                if (typeof ikan === 'undefined' || ikan === null) {
                    // variable is undefined or null
                    console.log('takada');
                } else {
                    console.log('ada');
                    const rownum = Math.trunc(i/2);
                    
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

                    // let row = document.getElementById("ikanRow"+rownum);
                    // let divIkan = document.getElementById("templateCard").cloneNode(true);
                    // divIkan.id = "ikan"+ikan[0];
                    // divIkan.style.display = "block";
                    // let gambar = divIkan.chil
                    // row.append(divIkan);

                    let divIkan = $("<div>")
                        .attr("id","ikan"+ikan[0])
                        .addClass("col-md-6 text-center col-sm-6 col-xs-6")
                        .append(
                            $("<div>")
                            .addClass("thumbnail product-box")
                            .append(
                                $("<img>")
                                .css("height","15vh")
                                .attr("src",ikan["imageLink"])
                            )
                            .append(
                                $("<div>")
                                .addClass("caption")
                                .append(
                                    $('<h3><a href="#">'+ ikan['name'] +' </a></h3>')
                                )
                                .append(
                                    $("<p>")
                                    .html("Price : <strong>RP." + ikan['price'] + "</strong>/" + ikan['satuan'])
                                )
                                .append(
                                    $("<p>")
                                    .html('<a href="#">Ptional dismiss button </a>')
                                )
                                .append(
                                    $("<p>")
                                    // .append(
                                    //     $("<a>")
                                    //     .addClass("btn btn-success")
                                    //     .attr("href","#")
                                    //     .attr("role","button")
                                    //     .text("Add To Cart")
                                    // )
                                    .append(
                                        $("<a>")
                                        .click(function() {
                                            seeDetail(ikan["id"]); // TODO TEST
                                        })
                                        .addClass("btn btn-primary")
                                        .attr("href","#")
                                        .attr("role","button")
                                        .text("See Details")
                                    )
                                )
                            )
                        )
                    ;

                    $("#ikanRow"+rownum).append(divIkan);
                }
            }
        }
        
        function gotoBoundaryPage(page){
            if (page == 'min') {
                gotoPage(1);
            } else if (page == 'max') {
                gotoPage(pageCount);
            }
        }
        
        function addCategoryFilter(category) {
            console.log("hai");
            let searchForm = document.getElementById("searchForm");
            if (searchKey != 'none') {
                console.log(searchKey);
                let searchKeyInput = document.createElement("input");
                searchKeyInput.type = "hidden";
                searchKeyInput.name = "searchKey";
                searchKeyInput.value = searchKey;
                searchForm.append(searchKeyInput);
            }
            let categoryFilterInput = document.createElement("input");
            categoryFilterInput.type = "hidden";
            categoryFilterInput.name = "categoryFilter";
            categoryFilterInput.value = category;
            searchForm.append(categoryFilterInput);

            searchForm.submit();
        }


        function seeDetail(ikan_id) {
            window.location.href = "detail.php?ikan_id="+ikan_id;
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