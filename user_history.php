<?php
require_once("proyekpw_lib.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];
        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM `dtrans` dt JOIN ikan i ON dt.ikan_id = i.id WHERE dt.`id_htrans` = :order_id;";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":order_id", $order_id);
            $stmt->execute();
            $histItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn = null;
    }
}

try {
    $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
    // set the PDO error mode to exception
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT ht.id_htrans, ht.mid_order_id, u.username, s.ship_name, ht.trans_time, ht.status, ht.notif_seen FROM `htrans` ht JOIN `users` u ON ht.id_user = u.id JOIN shipping s ON ht.id_shipping = s.shipping_id WHERE `id_user` = (SELECT id FROM users WHERE username = :currUsername);";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":currUsername", $_SESSION['currUsername']);
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
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <title>History User</title>
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
    <style>
        .notifNew {
            background-color: lightgreen;
        }
    </style>
</head>
<style>
    body,
    html {
        margin: 0;
        padding: 0;
    }

    li,
    a {
        color: white;
    }

    td {
        text-align: center;
    }

    .status {
        text-transform: capitalize;
    }

    .showItems {
        width: 99%;
        background-color: gray;
        color: white;
    }
    .filter td{
        text-align: left;
    }
    .filter input{
        margin-left: 1vw;
        width: 100%;
    }
    .filter select{
        margin-left: 1vw;
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
                <form action="./#ikanSearchDisplay" class="navbar-form navbar-right" role="search" method="get">
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
    <br>
    <!-- TODO : bac to home invisible -->
    <a href="index.php"><button class="btn btn-primary" style="margin-left: 5vw; color:white;">Back to Home</button></a>
    <h3 style="margin-left: 5vw; margin-top:2vh;">History</h3>
    <br>
    <div class="filter">
        <table style="margin-left: 5vw;">
            <tr>
                <td><label for="filterStart">Start : </label></td>
                <td><input type="date" name="filterStart" id="filterStart"></td>
            </tr>
            <tr>
                <td><label for="filterEnd">End : </label></td>
                <td><input type="date" name="filterEnd" id="filterEnd"></td>
            </tr>
            <tr>
                <td><label for="">Trans ID :</label>  </td>
                <td><input type="text" name="keyword" id="transkeyword" placeholder="Trans id"></td>
            </tr>
            <tr>
                <td><label for="">Order By : </label> </td>
                <td><select style="width:100%;" name="sort" id="sort">
                        <option value="oldest">Oldest</option>
                        <option value="newest">Newest</option>
                    </select></td>
            </tr>
            <tr>
                <td><label for="">Status : </label> </td>
                <td><select style="width:100%;" name="payStatusFilter" id="payStatusFilter">
                        <option value="">All</option>
                        <option value="success">Success</option>
                        <option value="pending">Pending</option>
                        <option value="fail">Failed</option>
                    </select></td>
            </tr>
            <tr>
                <td></td>
                <td><button style="width:100%;" class="btn btn-primary" onclick="getTrans(1)">Filter</button></td>
            </tr>
        </table>
        <br>
        <br>
    </div>
    <nav style="margin-left: 5vw;" aria-label="Page navigation">
        <ul class="pagination" id="pagination">
            <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li> -->
        </ul>
    </nav>
    <table class="table col-6 col-md-4" style="margin-left: 5vw; margin-right:2vw; width : 50vw;">
        <thead style="background-color: gray; color:white;">
            <tr>
                <th style="text-align: center;">No.</th>
                <th style="text-align: center;">Order Id</th>
                <th style="text-align: center;">Shipping Name</th>
                <th style="text-align: center;">Transaction Timestamp</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Items</th>
            </tr>
        </thead>
        <tbody id="transList">

        </tbody>
    </table>
    <div id="details" style="display: none;">
        <div>
            <h3>Order ID = <span id="order_id"></span></h3>
        </div>
        <button class="btn btn-danger" id="cancel" onclick="cancel()" style="display: none;">Cancel Order</button>
        <br>
        <br>
        <table class="table col-6" style="margin-left: 5vw; width:40vw;">
            <thead style="background-color: gray; color:white;">
                <tr>
                    <th style="text-align: center;">No.</th>
                    <th style="text-align: center;">Nama Ikan</th>
                    <th style="text-align: center;">Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: center;">Subtotal</th>
                </tr>
            </thead>
            <tbody id="details_body">

            </tbody>
        </table>
        <div style="margin-left: 5vw;">
            <h3>Total = Rp. <span id="total"></span></h3>
        </div>
    </div>
    <br>
</body>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="./assets/bootstrap5/bootstrap-5.1.3-dist/js/bootstrap.min.js"></script>
<script>
    var numfmt = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2
    });
    var pageCount = 0;

    getTrans(1);

    function getTrans(page) {
        let filterStart = $("#filterStart").val();
        let filterEnd = $("#filterEnd").val();
        let keyword = $("#transkeyword").val();
        let sort = $("#sort").val();
        let payStatusFilter = $("#payStatusFilter").val();
        $.ajax({
            type: "get",
            url: "index_controller.php",
            data: {
                'action': 'getTrans',
                'page': page,
                'filterStart': filterStart,
                'filterEnd': filterEnd,
                'keyword': keyword,
                'sort': sort,
                'payStatusFilter': payStatusFilter,
            },
            success: function(response) {
                respDecoded = JSON.parse(response);
                console.log(respDecoded);
                pageCount = respDecoded['page'];
                let list = respDecoded['list'];

                $('#transList').empty();
                //build list
                for (let i = 0; i < list.length; i++) {
                    const trans = list[i];
                    $('#transList').append(
                        $('<tr>')
                        .append(
                            $('<td>').text(i + 1)
                        )
                        .append(
                            $('<td>').text(trans['mid_order_id'])
                        )
                        .append(
                            $('<td>').text(trans['ship_name'])
                        )
                        .append(
                            $('<td>').text(trans['trans_time'])
                        )
                        .append(
                            $('<td>').append(
                                $('<span>').text(trans['status']).attr("id", "status_" + trans['mid_order_id']).addClass("status label " + ((trans['status'] == "Pending" || trans['status'] == "pending" || trans['status'] == "attempted" || trans['status'] == "challenge") ? "label-warning" : ((trans['status'] == "Success" || trans['status'] == "Settlement") ? "label-success" : "label-danger")))
                            )
                        )
                        .append(
                            $('<td>').append(
                                $("<button>").text("Show Items").addClass("showItems").click(function() {
                                    getOrderDetails(trans['mid_order_id']);
                                })
                            )
                        ).addClass((trans['notif_seen'] == 0) ? "notifNew" : "")
                    );
                }

                // refresh pagination
                refreshPagination(page);

            },
            error: function(response) {
                alert("AJAX ERROR " + response);
            }
        });
    }

    function refreshPagination(currPage) {
        let minOnPagination = currPage - Math.floor(pageCount / 2);
        let maxOnPagination = currPage + Math.floor(pageCount / 2);

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
                            console.log('TODO : not implemented yet');
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
                                getTrans(i);
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
            getTrans(1);
        } else if (page == 'max') {
            getTrans(pageCount);
        }
    }

    function getOrderDetails(id) {
        console.log(id);
        $.ajax({
            type: "get",
            url: "index_controller.php",
            data: {
                'action': 'getDTrans',
                'order_id': id,
            },
            success: function(response) {
                respDecoded = JSON.parse(response);
                console.log(respDecoded);
                let detail = respDecoded['detail'];
                let items = respDecoded['items'];
                let total = 0;

                $('#details_body').empty();

                $("#order_id").text(detail['mid_order_id']);
                if (detail['status'] == 'pending' || detail['status'] == 'Pending') {
                    $("#cancel").show();
                } else {
                    $("#cancel").hide();
                }
                //build items
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    $('#details_body').append(
                        $('<tr>')
                        .append(
                            $('<td>').text(i + 1)
                        )
                        .append(
                            $('<td>').text(item['name'])
                        )
                        .append(
                            $('<td>').text("Rp. " + numfmt.format(item['price']))
                        )
                        .append(
                            $('<td>').text(item['qty'])
                        )
                        .append(
                            $('<td>').text("Rp. " + numfmt.format(item['price'] * item['qty']))
                        )
                    );
                    total += item['price'] * item['qty'];
                }
                $('#total').text(numfmt.format(total));

                $("#details").show();
            },
            error: function(response) {
                alert("AJAX ERROR " + response);
            }
        });
    }

    var resp;
    function cancel() {
        let order_id = $("#order_id").text();
        $.ajax({
            type: "post",
            url: "index_controller.php",
            data: {
                'action': 'cancel',
                'order_id': order_id,
            },
            success: function(response) {
                resp = response;
                if (response == '"200"') {
                    // $("#status_" + order_id).text("Refund");
                    alert("Refund Success");
                    location.reload();
                } else {
                    alert(response + ": Refund Failed");
                }
            },
            error: function(response) {
                alert("AJAX ERROR " + response);
            }
        });
    }
</script>

</html>