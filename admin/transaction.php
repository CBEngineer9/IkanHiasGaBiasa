<?php
    require_once("../proyekpw_lib.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            try {
                $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                // set the PDO error mode to exception
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
                $sql = "SELECT * FROM `dtrans` dt JOIN ikan i ON dt.ikan_id = i.id WHERE dt.`id_htrans` = :order_id;";
                $stmt = $conn -> prepare($sql);
                $stmt -> bindValue(":order_id",$order_id);
                $stmt->execute();
                $histItems = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        
            } catch(\PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            $conn=null;
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
</head>
<body>
    <h3>Search Transaction</h3>
    <!-- <form action="transdetail.php" method="get">
        <input type="text" name="keyword" id="transkeyword" placeholder="Trans id/customer id">
        <input class="btn btn-dark" type="submit" value="Search">
    </form> -->
    <label for="filterStart">Start : </label>
    <input type="date" name="filterStart" id="filterStart">
    <label for="filterEnd">End : </label>
    <input type="date" name="filterEnd" id="filterEnd">
    <input type="text" name="keyword" id="transkeyword" placeholder="Trans id/customer id">
    <select name="sort" id="sort">
        <option value="oldest">Oldest</option>
        <option value="newest">Newest</option>
    </select>
    <select name="payStatusFilter" id="payStatusFilter">
        <option value="">All</option>
        <option value="success">Success</option>
        <option value="pending">Pending</option>
        <option value="fail">Failed</option>
    </select>
    <button onclick="getTrans(1)">Filter</button>
    <br>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order Id</th>
                <th>User</th>
                <th>Shipping</th>
                <th>Timestamp</th>
                <th>Payment Status</th>
                <th>Delivery Status</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody id="trans_body">

        </tbody>
    </table>
    <div id="pagination">

    </div>

    <div id="details" style="display: none;">
        Order ID = <span id="order_id"></span>
        <button id="refund" data-bs-toggle="modal" data-bs-target="#refundPopup">Refund Order</button>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>nama ikan</th>
                    <th>price</th>
                    <th>qty</th>
                    <th>subtotal</th>
                </tr>
            </thead>
            <tbody id="itemList">

            </tbody>
        </table>
        Total = Rp. <span id="total"></span>
    </div>

    <div class="modal fade" tabindex="-1" id="refundPopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="refundReason">Reason : </label>
                    <input type="text" name="refundReason" id="refundReason">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="refund()">Refund</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../assets/bootstrap5/bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
<script>

    getTrans(1);

    function getTrans(page) {
        let filterStart = $("#filterStart").val();
        let filterEnd = $("#filterEnd").val();
        let keyword = $("#transkeyword").val();
        let sort = $("#sort").val();
        let payStatusFilter = $("#payStatusFilter").val();
        $.ajax({
            type:"get",
            url:"admin_ajax.php",
            data:{
                'action':'getTrans',
                'page':page,
                'filterStart':filterStart,
                'filterEnd':filterEnd,
                'keyword':keyword,
                'sort':sort,
                'payStatusFilter':payStatusFilter,
            },
            success:function(response){
                let respDecoded = JSON.parse(response);
                console.log(respDecoded);
                let page = respDecoded['page'];
                let list = respDecoded['list'];

                $('#trans_body').empty();
                $('#pagination').empty();
                //build list
                for (let i = 0; i < list.length; i++) {
                    const trans = list[i];
                    $('#trans_body').append(
                        $('<tr>')
                        .append(
                            $('<td>').text(trans['mid_order_id'])
                        )
                        .append(
                            $('<td>').text(trans['username'])
                        )
                        .append(
                            $('<td>').text(trans['ship_name'])
                        )
                        .append(
                            $('<td>').text(trans['trans_time'])
                        )
                        .append(
                            $('<td>').attr("id","status_"+trans['mid_order_id']).text(trans['status'])
                        )
                        .append(
                            $('<td>').text(trans['delivery_status'])
                        )
                        .append(
                            $('<td>').append(
                                $("<button>").text("Show Items").click(function() {
                                    getOrderDetails(trans['mid_order_id']);
                                })
                            )
                        )
                    );
                }

                // `<td>
                //         <!-- <button class="showItems" transid='<= $histRow['id_htrans']?>'>Show Items</button> -->
                //         <form action="#" method="post">
                //             <input type="hidden" name="order_id" value="<= $histRow['mid_order_id']?>">
                //             <input type="submit" value="Show Items">
                //         </form>
                //     </td>`

                for (let i = 0; i < page; i++) {
                    $('#pagination').append(
                        $('<span>').text(i+1 + " ")
                        .click(function() {
                            getTrans(i+1)
                        })
                    );
                }
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }

    function getOrderDetails(id) {
        console.log(id);
        $.ajax({
            type:"get",
            url:"admin_ajax.php",
            data:{
                'action':'getDTrans',
                'order_id':id,
            },
            success:function(response){
                let respDecoded = JSON.parse(response);
                let detail = respDecoded['detail'];
                let items = respDecoded['items'];
                let total = 0;

                $('#itemList').empty();

                $("#order_id").text(detail['mid_order_id'])
                //build items
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    $('#itemList').append(
                        $('<tr>')
                        .append(
                            $('<td>').text(item['name'])
                        )
                        .append(
                            $('<td>').text(item['price'])
                        )
                        .append(
                            $('<td>').text(item['qty'])
                        )
                        .append(
                            $('<td>').text(item['price'] * item['qty'])
                        )
                    );
                    total += item['price'] * item['qty'];
                }
                $('#total').text(total);

                $("#details").show();
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }

    var refundPopup = document.getElementById('refundPopup');
    refundPopup.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget
        // Extract info from data-bs-* attributes
        var recipient = button.getAttribute('data-bs-whatever')
        // If necessary, you could initiate an AJAX request here
        // and then do the updating in a callback.
        //
        // Update the modal's content.
        var modalTitle = refundPopup.querySelector('.modal-title')

        let order_id = $("#order_id").text();
        modalTitle.textContent = 'Refund ' + order_id;
    })

    function refund() {
        let order_id = $("#order_id").text();
        let reason = $('#refundReason').val();

        $.ajax({
            type:"post",
            url:"admin_ajax.php",
            data:{
                'action':'refund',
                'order_id':order_id,
                'reason':reason,
            },
            success:function(response){
                let respDecoded = JSON.parse(response);
                if (respDecoded["status_code"] == 200) {
                    $("#status_"+order_id).text("Refund");
                    alert("Refund Success");
                } else {
                    alert(respDecoded["status_code"] + ": Refund Failed");
                }
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }
</script>
</html>