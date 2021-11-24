<?php
    require_once("../proyekpw_lib.php");
    require_once dirname(__FILE__) . '/..//Midtrans.php';

    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        if (!isset($_SESSION['currUser'])) {
            header("Location:index.php");
        }

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $sqlCart = "SELECT * FROM cart c JOIN ikan i ON c.ikan_id = i.id JOIN category cat ON cat.cat_id = i.cat_id WHERE `user_id` = :userid;";
            $stmt = $conn->prepare($sqlCart);
            $stmt -> bindValue(":userid",$_SESSION['currUser']); 
            $stmt -> execute();
    
            $qResultCart = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            // alert("Connection failed: " . $e->getMessage());
        }
        $conn=null;
    }

?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../assets/css/cart.css">
    <title>Cart</title>
    <link rel="icon" href="assets/img/Logo/favicon.ico">
</head>

<body>
    
    <div class="card">
        <div class="row">
            <div class="col-md-8 cart">
                <div class="title">
                    <div class="row">
                        <div class="col">
                            <h4>Shopping Cart</h4>
                        </div>
                        <div class="col align-self-center text-right text-muted" style="text-align:right;"> <span id="cartCount1" class="cartCount"><?= count($qResultCart)?></span>  items</div>
                    </div>
                </div>
                <div id="cartItems">
                    <?php foreach ($qResultCart as $cartRow) {?>
                        <div class="row border-top border-bottom">
                            <div class="row main align-items-center">
                                <div class="col-2"><img class="img-fluid" src=<?= "../".$cartRow['imageLink']?>></div>
                                <div class="col">
                                    <div class="row text-muted"><?= $cartRow['cat_name']?></div>
                                    <div class="row"><?= $cartRow['name']?></div>
                                </div>
                                <div class="col"> <a onclick="addQty('<?= $cartRow['cart_id'] ?>',-1)">-</a><a href="#" id="qty<?= $cartRow['cart_id'] ?>" class="border"><?= $cartRow['qty'] ?></a><a onclick="addQty('<?= $cartRow['cart_id'] ?>',1)">+</a> </div>
                                <div class="col"><span id="price<?= $cartRow['cart_id'] ?>"> <?= "Rp.". number_format($cartRow['price'] * $cartRow['qty'], 2)  ?> </span> <span onclick="removeItem('<?= $cartRow['cart_id'] ?>')" class="close">&#10005;</span></div>
                            </div>
                        </div>
                    <?php }?>
                    <!-- <div class="row">
                        <div class="row main align-items-center">
                            <div class="col-2"><img class="img-fluid" src="https://i.imgur.com/ba3tvGm.jpg"></div>
                            <div class="col">
                                <div class="row text-muted">Shirt</div>
                                <div class="row">Cotton T-shirt</div>
                            </div>
                            <div class="col"> <a  href="#">-</a><a href="#" class="border">1</a><a href="#">+</a> </div>
                            <div class="col">&euro; 44.00 <span class="close">&#10005;</span></div>
                        </div>
                    </div> -->
                    <!-- <div class="row border-top border-bottom">
                        <div class="row main align-items-center">
                            <div class="col-2"><img class="img-fluid" src="https://i.imgur.com/pHQ3xT3.jpg"></div>
                            <div class="col">
                                <div class="row text-muted">Shirt</div>
                                <div class="row">Cotton T-shirt</div>
                            </div>
                            <div class="col"> <a href="#">-</a><a href="#" class="border">1</a><a href="#">+</a> </div>
                            <div class="col">&euro; 44.00 <span class="close">&#10005;</span></div>
                        </div>
                    </div> -->
                </div>
                <div class="back-to-shop"><a style="text-decoration: none;" href="../"><span class="text-muted"> Back to shop</span></a></div>
            </div>
            <div class="col-md-4 summary">
                <div>
                    <h5><b>Summary</b></h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col" style="padding-left:0;">ITEMS <span id="cartCount2" class="cartCount"><?= count($qResultCart)?></span></div>
                    <div id="cartTotal" class="col text-right">Rp.0</div>
                </div>
                <form>
                    <p>SHIPPING</p> 
                    <select id="shipOpt" onchange="calcGrandTot()">
                        <option class="text-muted" value="standart">Standard-Delivery- Rp. 15,000</option>
                    </select>
                    <p>GIVE CODE</p> <input id="code" placeholder="Enter your code">
                </form>
                <div class="row" style="border-top: 1px solid rgba(0,0,0,.1); padding: 2vh 0;">
                    <div class="col">TOTAL PRICE</div>
                    <div id="grandTotal" class="col text-right">&euro; 137.00</div>
                </div> <button onclick="checkout()" class="btn">CHECKOUT</button>
            </div>
        </div>
    </div>
</body>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Midtrans\Config::$clientKey;?>"></script>
<script>
    var numfmt = new Intl.NumberFormat('id-ID',{ minimumFractionDigits: 2 });
    var total = 0;
    var shipOpt;
    var grandTotal = 0;
    var shippingDetail = Array();

    getPriceCount();

    function getPriceCount() {
        $.ajax({
            type:"get",
            url:"cart_controller.php",
            data:{
                'action':'getPriceCount',
            },
            success:function(response){
                const priceCountData = JSON.parse(response);
                $('.cartCount').text(priceCountData['itemCount']);
                $('#cartTotal').text("Rp." + numfmt.format(priceCountData['total']));
                total = priceCountData['total'];
                calcGrandTot();
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }

    function addQty(cart_id,amount) {
        $.ajax({
            type:"get",
            url:"cart_controller.php",
            data:{
                'action':'addQty',
                'cart_id':cart_id,
                'amount':amount,
            },
            success:function(response){
                const newUpdate = JSON.parse(response);
                console.log(newUpdate);
                $('#qty'+cart_id).text(newUpdate['qty']);
                $("#price"+cart_id).text("Rp." + numfmt.format(newUpdate['price'] * newUpdate['qty']));
                getPriceCount();
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }
    function removeItem(cart_id) {
        $.ajax({
            type:"get",
            url:"cart_controller.php",
            data:{
                'action':'removeItem',
                'cart_id':cart_id,
            },
            success:function(response){
                $('#cartItems').empty();
                $('#cartItems').append(response);
                getPriceCount();
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }

    function calcGrandTot() {
        shipOpt = $('#shipOpt').val();
        if (shipOpt == 'standart') {
            grandTotal = parseInt(total) + 15000;
            shippingDetail = {
                id : "1",
                name : 'Standart Shipping',
                price : 15000,
                quantity : 1,
            };
        }
        $('#grandTotal').text("Rp." + numfmt.format(grandTotal));
    }

    function checkout() {
        let cart = '';
        let user = '';
        $.ajax({
            type:"get",
            url:"cart_controller.php",
            data:{
                'action':'getCart',
            },
            success:function(response){
                console.log(response);
                cart = response;

                $.ajax({
                    type:"get",
                    url:"cart_controller.php",
                    data:{
                        'action':'getUserData',
                    },
                    success:function(response){
                        console.log(response);
                        user = response;

                        $.ajax({
                            type:"post",
                            url:"snap_controller.php",
                            data:{
                                'cart':cart,
                                'user':user,
                                'total':grandTotal,
                                'shipping' : JSON.stringify(shippingDetail),
                            },
                            success:function(response){
                                console.log(response);
                                snap.pay(response, {
                                    onSuccess: function(result){
                                        // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                                        console.log(result);
                                        $.ajax({
                                            type:"post",
                                            url:"cart_controller.php",
                                            data:{
                                                'action':'clearCart',
                                                'order_id':result['order_id'],
                                            },
                                            success:function(response){
                                                console.log('cart cleared');
                                            },
                                            error:function(response){
                                                alert("AJAX ERROR " + response);
                                            }
                                        });
                                        // TODO : redirect to thankyou
                                    },
                                    onPending: function(result){
                                        // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                                        $.ajax({
                                            type:"post",
                                            url:"cart_controller.php",
                                            data:{
                                                'action':'clearCart',
                                                'order_id':result['order_id'],
                                            },
                                            success:function(response){
                                                console.log('cart cleared');
                                            },
                                            error:function(response){
                                                alert("AJAX ERROR " + response);
                                            }
                                        });
                                    },
                                    onError: function(result){
                                        // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                                        console.log("error");
                                    }
                                });
                            },
                            error:function(response){
                                alert("AJAX ERROR " + response);
                            }
                        });
                    },
                    error:function(response){
                        alert("AJAX ERROR " + response);
                    }
                });
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }
</script>
</html>