<!DOCTYPE html>
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
                    <img style="margin-top:10px; margin-bottom:10px;" src="../IkanHiasGaBiasa/assets/img/Logo/tumblr_myvpf71CVu1spjmmdo1_1280.png" width="50px" height="50px" alt=""> <strong>NAMA WEBSITE</strong>
                </div>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div style="margin-top: 5px;" class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
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
                    <li><a href="cart.php"><img src="../IkanHiasGaBiasa/assets/img/icon/cart-2-24.png" alt=""></a></li>
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
    <div class="container" style="margin-top:10vh;">
        <div class="row justify-content-md-center" style="margin-left: 5vw;">
            <div class="col col-lg-5">
                <img style="width: 200pxvw; height:45vh;" src="../IkanHiasGaBiasa/assets/img/ikan/ikan1.png" alt="">
            </div>
            <div class="col col-lg-6" style="margin-left:3vw; margin-top: 7vh;">
                <p style="font-size: 3em; margin-left:3vw;">Nama Ikan</p>
                <p style="font-size: 2em; margin-left:3vw;">Rp. Harga</p>
                <br>
                <p style="font-size: 1.5em; margin-left:3vw;" class="fs-3">Detail Ikan</p>
                <br>
                <p style="font-size: 1.5em; margin-left:3vw;" class="fs-3"><input type="number"></p>
                <br>
                <button type="button" style="margin-left:3vw;" class="btn btn-success">Add to Cart</button>
            </div>
        </div>
    </div>
</body>
</html>