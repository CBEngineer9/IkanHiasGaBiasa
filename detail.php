<?php
    require_once("proyekpw_lib.php");

    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        if (!isset($_GET['ikan_id'])) {
            header("Location:./");
            die;
        }
        $ikan_id = $_GET['ikan_id'];

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $sqlIkan = "SELECT ikan.id, ikan.name, ikan.stock, ikan.price, ikan.imageLink, ikan.description, ikan.satuan , category.cat_name FROM `ikan` JOIN category ON ikan.cat_id = category.cat_id  WHERE ikan.id = :ikan_id;";
            $stmt = $conn->prepare($sqlIkan);
            $stmt -> bindParam(":ikan_id",$ikan_id); 
            $stmt -> execute();
    
            $qResultIkan = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // echo "Connection failed: " . $e->getMessage();
            alert("Connection failed: " . $e->getMessage());
        }
        $conn=null;
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['addCart'])) {
            if (!isset($_SESSION['currUser'])) {
                alert('Please Login First');
                redirect("./");
                die;
            }
            $ikan_id = $_POST['ikan_id'];
            $qty = $_POST['qty'];

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
                // update stock
                $sql = "UPDATE `ikan` i SET i.`stock` = (SELECT i2.stock - :qty FROM (SELECT id,stock FROM ikan) i2 WHERE i2.id = :ikan_id) WHERE i.id = :ikan_id";
                $stmt = $conn->prepare($sql);
                $stmt -> bindValue(":qty",$qty); 
                $stmt -> bindValue(":ikan_id",$ikan_id); 
                $succUpdate = $stmt -> execute();

                if ($succUpdate == 1) {
                    // update if exists
                    $sqlUpdate = "UPDATE cart SET qty = qty + :qty WHERE ikan_id = :ikan_id";
                    $stmt = $conn->prepare($sqlUpdate);
                    $stmt -> bindValue(":qty",$qty,PDO::PARAM_INT); 
                    $stmt -> bindValue(":ikan_id",$ikan_id); 
                    $succUpdate = $stmt -> execute();
                    $updateCount = $stmt -> rowCount();

                    if ($updateCount <= 0) {
                        $sqlCart = "INSERT INTO `cart` (`user_id`, `ikan_id`, `qty`) VALUES ( (SELECT id FROM users WHERE username = :currUsername) , :ikan_id, :qty);";
                        $stmt = $conn->prepare($sqlCart);
                        $stmt -> bindValue(":currUsername",$_SESSION['currUsername']); 
                        $stmt -> bindValue(":ikan_id",$ikan_id); 
                        $stmt -> bindValue(":qty",$qty); 
        
                        $succInsert = $stmt -> execute();
                    }
    
                    if ($updateCount > 0 || $succInsert == true) {
                        alert("berhasil add to cart");
                        redirect("./");
                        die;
                    } else {
                        alert("Failed to add to cart");
                        redirect("./");
                        die;
                    }
                }

            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                // alert("Connection failed: " . $e->getMessage());
            }
            $conn=null;
        }
    }
    
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Fontawesome core CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
    <!--GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <!--Slide Show Css -->
    <link href="assets/ItemSlider/css/main-style.css" rel="stylesheet" />
    <!-- custom CSS here -->
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body style="height: 100%;">
<nav style="background-color:#88E0EF;border:none; border-bottom:3px solid gray;" class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="logo" style="width: 10vw;">
                    <a href="./">
                        <img style="margin-top:10px; margin-bottom:10px;" src="./assets/img/Logo/logoweb.png" width="173px" height="70px" alt=""> 
                    </a>
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
                        }
                        else{
                    ?>
                        <!-- <li><button type="submit" style="border: none; background-color:transparent; color:white; display:inline-block;" name="btLogout">Logout</button></li> -->
                        <li><a href=""> Hai, <?=$_SESSION['currUsername']?>!</a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="./cart"><img src="assets/img/icon/cart-2-24.png" alt=""></a></li>
                    <?php
                        }
                    ?>
                </ul>
                <form action="./#ikanSearchDisplay" class="navbar-form navbar-right" role="search" method="get">
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
    <div class="container" style="margin-top:10vh;">
        <div class="row justify-content-md-center" style="margin-left: 5vw;">
            <div class="col col-lg-5">
                <img style="width: 25vw; height:45vh;" src=<?= $qResultIkan['imageLink']?> alt="">
            </div>
            <div class="col col-lg-6" style="margin-left:3vw; margin-top: 7vh;">
                <form action="#" method="post">
                    <p style="font-size: 3em; margin-left:3vw;"><?= $qResultIkan['name']?></p>
                    <p style="font-size: 2em; margin-left:3vw;">Rp. <?= number_format($qResultIkan['price'])."/". $qResultIkan['satuan']?></p>
                    <br>
                    <p style="font-size: 1.5em; margin-left:3vw;" class="fs-3"><?= $qResultIkan['description']?></p>
                    <br>
                    <p style="font-size: 1.5em; margin-left:3vw;" class="fs-3"><?= "Stock : ".$qResultIkan['stock'] . " " . $qResultIkan['satuan']?></p>
                    <br>
                    <p style="font-size: 1.5em; margin-left:3vw;" class="fs-3"><input type="number" name="qty" value="1" min=1 max=<?= $qResultIkan['stock']?>></p>
                    <br>
                    <input type="hidden" name="ikan_id" value=<?= $ikan_id?>>
                    <button type="submit" name="addCart" style="margin-left:3vw;" class="btn btn-success">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>