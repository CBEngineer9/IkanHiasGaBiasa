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
                        <!-- <li><button type="submit" style="border: none; background-color:transparent; color:white; display:inline-block;" name="btLogout">Logout</button></li> -->
                        <li><a href="about.php">About Us</a></li>
                        <li><a href=""> Hai, <?= $_SESSION['currUsername'] ?>!</a></li>
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
        .jumbotron{
            background-image: url('./assets/img/banner/thumb-1920-121336.jpg');
            background-repeat: no-repeat;
            height: 60vh;
            opacity: 0.8;
            color: white;
        }
    </style>
    <div class="jumbotron" style="margin-top: -2.2vh;">
        <h1 class="display-4">About Us</h1>
        <p class="lead">SussyFishy is an E-Commerce that is dedicated for fish. We want to ensure our customer <br> get the best experience  by using our platform.  We use secure payment system and the <br> best data encryption system to protect our customers</p>
    </div>
</body>

</html>