<?php
    require_once("../proyekpw_lib.php");

    if (!isset($_SESSION['admin'])) {
        header("Location:../login.php");
    }

    $editIkanId = -1;

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['toHome'])) {
            unset($_SESSION['admin']);
            header("Location:../index.php");
        }
        if (isset($_POST['edit'])) {
            $editIkanId = $_POST['rowKey'];
        }
        if (isset($_POST['confEdit'])) {
            $rowKey = $_POST['rowKey'];
            $editNameRow = $_POST['editNameRow'.$rowKey];
            $editDescRow = $_POST['editDescRow'.$rowKey];
            $editCatRow = $_POST['editCatRow'.$rowKey];
            $editStockRow = $_POST['editStockRow'.$rowKey];
            $editPriceRow = $_POST['editPriceRow'.$rowKey];
            $editSatuanRow = $_POST['editSatuanRow'.$rowKey];

            //update 
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "UPDATE `ikan` 
                        SET 
                            `name` = :ikanName ,
                            `cat_id` = :cat_id ,
                            `stock` = :stock ,
                            `price` = :price ,
                            `satuan` = :satuan ,
                            `description` = :descript 
                        WHERE `ikan`.`id` = :idIkan;";
                $stmt = $conn->prepare($sql);
                $stmt -> bindParam(":idIkan",$rowKey);
                $stmt -> bindParam(":ikanName",$editNameRow);
                $stmt -> bindParam(":cat_id",$editCatRow);
                $stmt -> bindParam(":stock",$editStockRow);
                $stmt -> bindParam(":price",$editPriceRow);
                $stmt -> bindParam(":satuan",$editSatuanRow);
                $stmt -> bindParam(":descript",$editDescRow);
                $qresultEdit = $stmt -> execute();
            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            $conn=null;
            
            $editIkanId = -1;
        }
        if (isset($_POST['toggleStat'])) {
            $rowKey = $_POST['rowKey'];
            $isActive = $_POST['isActive'];

            $newStat = $isActive ^ 1;

            //update status
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "UPDATE `ikan` 
                        SET 
                            `isActive` = :newStat
                        WHERE `ikan`.`id` = :idIkan;";
                $stmt = $conn->prepare($sql);
                $stmt -> bindParam(":idIkan",$rowKey);
                $stmt -> bindParam(":newStat",$newStat);
                $qresultStat = $stmt -> execute();
            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            $conn=null;
        }
    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $conn->prepare("SELECT id, firstname, lastname FROM myGuest;");
        // $stmt -> execute();

        $sql = "SELECT ikan.* , category.cat_name 
                FROM `ikan` 
                JOIN category ON ikan.cat_id = category.cat_id;";
        $qResult = $conn->query($sql)->fetchAll();
        // echo "<pre>";
        // print_r($qResult);
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

        $sql2 = "SELECT * FROM `users`;";
        $stmt2 =  $conn -> prepare($sql2);
        // $stmt2 -> bindParam(":idUser",$_SESSION['id_user']);
        $stmt2 -> execute();
        $qresult2 = $stmt2 -> fetchAll();

        $sql3 = "SELECT * FROM `category`;";
        $stmt3 =  $conn -> prepare($sql3);
        $stmt3 -> execute();
        $qresult3 = $stmt3 -> fetchAll();

        $sql = "SELECT * FROM htrans;";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute();
        $history = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        
        $sql = "SELECT MONTHNAME(ht.trans_time) AS `month`, YEAR(ht.trans_time) AS `year`,  SUM((i.price * dt.qty) + s.ship_price) AS income FROM htrans ht JOIN shipping s ON ht.id_shipping = s.shipping_id RIGHT JOIN dtrans dt ON ht.mid_order_id = dt.id_htrans JOIN ikan i on dt.ikan_id = i.id WHERE `status` = 'Capture' OR `status` = 'Settlement' OR `status` = 'Success' GROUP BY MONTH(ht.trans_time), YEAR(ht.trans_time);";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute();
        $monthlyIncome = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn=null;

    // echo "<pre>";
    // var_dump($qResult);
    // echo "</pre>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="icon" href="assets/img/Logo/favicon.ico">
    <!-- <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" /> -->

    <!-- bootstrap css -->
    <link rel="stylesheet" href="../assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <!-- library jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <style>
        body{
            padding: 10px;
        }
        img{
            width: 100px;
            height: 100px;
        }
    </style>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

        // Load the Visualization API and the corechart package.
        google.charts.load('current', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {

            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Period');
            data.addColumn('number', 'income');
            data.addRows([

            <?php foreach ($monthlyIncome as $incomeRow ) {
                echo "['" . $incomeRow['month'].",".$incomeRow['year']."',".$incomeRow['income'] . "]," ;
            }?>

            // ['Mushrooms', 3],
            // ['Onions', 1],
            // ['Olives', 1],
            // ['Zucchini', 1],
            // ['Pepperoni', 2]
            ]);

            // Set chart options
            var options = {'title':'Profit Charts',
                        'width':900,
                        'height':300};

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <form action="#" method="post">
        <input class="btn btn-dark" type="submit" name="toHome" value="Back to home">
    </form>

    <h3><a href="users.php">Users</a> <a href="transaction.php">Transactions</a> </h3>

    <h3>Monthly Report</h3>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>

    <table class="table">
        <thead class="table-dark">
            <tr>
                <th>Month</th>
                <th>year</th>
                <th>Income</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($monthlyIncome as $incomeRow ) {?>
                <tr>
                    <td><?= $incomeRow['month']?></td>
                    <td><?= $incomeRow['year']?></td>
                    <td><?= "Rp. ".number_format($incomeRow['income']) ?></td>
                </tr>
            <?php }?>
        </tbody>
    </table>

    <h3>Add Ikan</h3>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="namaIkan"> Nama Ikan : </label>
            <input type="text" style="padding-right: 1vw" name="namaIkan" id="namaIkan"><br><br>
            <label for="descIkan">desc : </label><br>
            <textarea name="descIkan" id="descIkan" cols="36" rows="8"></textarea><br><br>
            <table>
                <tr>
                    <td><label for="stock">stock : </label></td>
                    <td><input style="padding-right: 1vw; margin-left:0.5vw;"  type="text" name="stock" id="stock"><br></td>
                </tr>
                <tr style="margin-top: 5vh;">
                    <td><label style="margin-top:1vh;" for="price">price :</label></td>
                    <td><input style="padding-right: 1vw; margin-top:1vh; margin-left:0.5vw;" type="text" name="price" id="price"></td>
                </tr>
                <tr>
                    <td><label style="margin-top:1vh;" for="category">category : </label></td>
                    <td><select style="padding-right: 5.5vw; margin-left:1vh; margin-top:1vh; height:30px;" name="category" id="category">
                        <?php foreach ($qresult3 as $row) {?>
                            <option value="<?= $row['cat_id']?>"><?= $row['cat_name']?></option>
                        <?php }?></td>
                        </select>
                </tr>
                <tr>
                    <td><label style="margin-top:1vh;" for="gambarIkan">image : </label></td>
                    <td><input style="padding-right: 1vw; margin-top:1vh; margin-left:0.5vw;" type="file" name="gambarIkan" id="gambarIkan"></td>
                </tr>
                <tr>
                    <td><label for=""></label></td>
                    <td><input style="margin-top:1vh; margin-left:0.5vw; padding-right:8.5vw;" type="submit" name="addIkan" value="Add"></td>
                </tr>
            </table>
        </div>
    </form>

    <h3>Add Ikan Bulk</h3>
    <form action="bulkInsert.php" method="post" enctype="multipart/form-data">
        <label for="fishCSV">File CSV : </label><input type="file" name="fishCSV" id="fishCSV"><br>
        <br>
        <label for="imageZip">Image ZIP : </label><input type="file" name="imageZip" id="imageZip"><br>
        <br>
        <input class="btn btn-dark" type="submit" value="Add Ikan" name="addIkanBulk">
    </form>

    <h3>Ikan</h3>
    <table class="table col-12">
        <thead class="thead-dark">
            <tr>
                <th scope="col">nama</th>
                <th scope="col">desc</th>
                <th scope="col">category</th>
                <th scope="col">stock</th>
                <th scope="col">harga</th>
                <th scope="col">satuan</th>
                <th style="width: 100px;" scope="col">gambar</th>
                <th scope="col">isActive</th>
                <th scope="col">action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($qResult as $key => $row) { ?>
                <form action="#" method="post">
                    <tr>
                        <?php if ($editIkanId == $row['id']) {?>
                            <td>
                                <input type="text" name="editNameRow<?= $row['id']?>" id="editNameRow<?= $row['id']?>" value="<?= $row['name']?>">
                            </td>
                            <td>
                                <textarea name="editDescRow<?= $row['id']?>" id="editDescRow<?= $row['id']?>" cols="30" rows="10"><?= $row['description']?></textarea>
                            </td>
                            <td>
                                <select name="editCatRow<?= $row['id']?>" id="editCatRow<?= $row['id']?>">
                                    <?php foreach ($qresult3 as $rowResult3) {?>
                                        <option value="<?= $rowResult3['cat_id']?>"><?= $rowResult3['cat_name']?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="editStockRow<?= $row['id']?>" id="editStockRow<?= $row['id']?>" value="<?= $row['stock']?>">
                            </td>
                            <td>
                                <input type="text" name="editPriceRow<?= $row['id']?>" id="editPriceRow<?= $row['id']?>" value="<?= $row['price']?>">
                            </td>
                            <td>
                                <input type="text" name="editSatuanRow<?= $row['id']?>" id="editSatuanRow<?= $row['id']?>" value="<?= $row['satuan']?>">
                            </td>
                            <td>
                                <img style="width: 100px;" src="../<?= $row['imageLink']?>" alt="">
                            </td>
                            <td>
                                <?= $row['isActive']?>
                            </td>
                        <?php } else {?>
                            <td><?= $row['name']?></td>
                            <td><?= $row['description']?></td>
                            <td><?= $row['cat_name']?></td>
                            <td id="stock<?= $row['id']?>"><?= $row['stock']?></td>
                            <td><?= $row['price']?></td>
                            <td><?= $row['satuan']?></td>
                            <td><img src="../<?= $row['imageLink']?>" alt=""></td>
                            <td><?= $row['isActive']?></td>
                        <?php }?>


                        <td>
                            <input type="hidden" name="rowKey" value=<?= $row['id']?>>
                            <input type="hidden" name="isActive" value=<?= $row['isActive']?>>
                            <!-- <input type="submit" name="addStock" value="Add Stock"><br> -->
                            <button class="btn btn-dark" type="button" data-bs-toggle="modal" data-bs-target="#modalForm" onclick="registerIkanId(<?= $row['id']?>)">
                                Add Stock
                            </button>
                            <?php 
                            if ($editIkanId == $row['id']) {?>
                                <input class="btn btn-dark" type="submit" name="confEdit" value="Confirm Edit"><br>
                            <?php } else { ?>
                                <input class="btn btn-dark" type="submit" name="edit" value="Edit"><br>
                            <?php } ?>
                            <br>
                            <input class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#edPicModal" data-bs-ikanid="<?= $row['id']?>" type="button" name="editPic" value="Edit Picture">
                            <input class="btn btn-dark" type="submit" name="toggleStat" value="Toggle Status">
                        </td>
                    </tr>
                </form>
            <?php }?>
        </tbody>
    </table>

    <!-- Tombol untuk memicu modal -->
    <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#modalForm" onclick="registerIkanId(param)">
        Buka Contact Form
    </button> -->
    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="labelModalKu">Contact Form</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <!-- <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Tutup</span> -->
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <p class="statusMsg"></p>
                    <form role="form">
                        <!-- <div class="form-group">
                            <label for="masukkanNama">Nama</label>
                            <input type="text" class="form-control" id="masukkanNama" placeholder="Masukkan nama Anda"/>
                        </div>
                        <div class="form-group">
                            <label for="masukkanEmail">Email</label>
                            <input type="email" class="form-control" id="masukkanEmail" placeholder="Masukkan email Anda"/>
                        </div>
                        <div class="form-group">
                            <label for="masukkanPesan">Pesan</label>
                            <textarea class="form-control" id="masukkanPesan" placeholder="Masukkan pesan Anda"></textarea>
                        </div> -->
                        <div class="form-group">
                            <label for="tambStock">Tambahan Stock</label>
                            <input type="text" class="form-control" id="tambStock"/>
                        </div>
                    </form>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary submitBtn" data-bs-dismiss="modal" onclick="sendAddStock()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Picture Modal -->
    <div class="modal" id="edPicModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="newPic" id="newPic">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confEditPic">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    var addStockIkanId;
    function registerIkanId(id){
        addStockIkanId = id;
    }

    function sendAddStock(){
        var reg = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        var oldStock = $('#stock'+addStockIkanId).text();
        var stock = $('#tambStock').val();
        if(stock.trim() == '' ){
            alert('Masukkan tambahan stock.');
            $('#tambStock').focus();
            return false;
        }else if(parseInt(stock.trim()) + parseInt(oldStock.trim()) < 0 ){ // CHECKING
            alert('Stock tidak boleh negatif.');
            $('#tambStock').focus();
            return false;
        }else{
            $.ajax({
                type:'POST',
                url:'kirim_form.php',
                data:'contactFrmSubmit=1&ikanId='+addStockIkanId+'&tambStock='+stock,
                beforeSend: function () {
                    $('.submitBtn').attr("disabled","disabled");
                    $('.modal-body').css('opacity', '.5');
                },
                success:function(newStock){
                    if(newStock == '-1'){
                        $('.statusMsg').html('<span style="color:red;">Ada sedikit masalah, silakan coba lagi.</span>');
                    }else{
                        $('#tambStock').val('');
                    }
                    $('.submitBtn').removeAttr("disabled");
                    $('.modal-body').css('opacity', '');
                    // REFREASH
                    $('#stock'+addStockIkanId).text(newStock);
                    // alert($('#stock'+ikanId).text());
                }
            });
        }
    }

    var edPicModal = document.getElementById('edPicModal')
    edPicModal.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget
        // Extract info from data-bs-* attributes
        var ikan_id = button.getAttribute('data-bs-ikanid')
        // If necessary, you could initiate an AJAX request here
        // and then do the updating in a callback.
        //
        // Update the modal's content.
        var modalTitle = edPicModal.querySelector('.modal-title')
        modalTitle.textContent = 'Edit picture ' + ikan_id
        $('#confEditPic').on('click', function() {
            editPicture(ikan_id);
        });
    })

    function editPicture(id) {
        var file_data = $('#newPic').prop('files')[0];
        var form_data = new FormData();
        form_data.append('newPic', file_data);
        form_data.append('ikan_id', id);
        form_data.append('action', 'editPicture');
        $.ajax({
            url: 'admin_ajax.php', // <-- point to server-side PHP script 
            dataType: 'text',  // <-- what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(php_script_response){
                alert(php_script_response); // <-- display response from the PHP script, if any
                location.reload();
                //TODO bug not realod if esc
            }
        });
    }

</script>
</html>

