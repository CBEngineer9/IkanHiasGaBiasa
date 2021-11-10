<?php
    require_once("proyekpw_lib.php");

    $editIkanId = -1;

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
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

            //update 
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "UPDATE `ikan` 
                        SET 
                            `name` = :ikanName ,
                            `cat_id` = :cat_id ,
                            `stock` = :stock ,
                            `price` = :price ,
                            `description` = :descript 
                        WHERE `ikan`.`id` = :idIkan;";
                $stmt = $conn->prepare($sql);
                $stmt -> bindParam(":idIkan",$rowKey);
                $stmt -> bindParam(":ikanName",$editNameRow);
                $stmt -> bindParam(":cat_id",$editCatRow);
                $stmt -> bindParam(":stock",$editStockRow);
                $stmt -> bindParam(":price",$editPriceRow);
                $stmt -> bindParam(":descript",$editDescRow);
                $qresultEdit = $stmt -> execute();
            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            $conn=null;
            
            $editIkanId = -1;
        }
    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $conn->prepare("SELECT id, firstname, lastname FROM myGuest;");
        // $stmt -> execute();

        // $sql = "Select * From ikan;";
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

        // echo "fetched successfully";
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
    <!-- <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" /> -->

    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- library jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <!-- bootstrap js -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        body{
            padding: 10px;
        }
        img{
            width: 100px;
            height: 100px;
        }
    </style>
</head>
<body>
    <h3>Users</h3>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th>id</th>
                <th>username</th>
                <th>password</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($qresult2 as $row ) {?>
                <tr>
                    <td><?= $row['id']?></td>
                    <td><?= $row['username']?></td>
                    <td><?= $row['password']?></td>
                </tr>
            <?php }?>
        </tbody>
    </table>

    <h3>add Ikan</h3>
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


    <h3>Ikan</h3>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">nama</th>
                <th scope="col">desc</th>
                <th scope="col">category</th>
                <th scope="col">stock</th>
                <th scope="col">harga</th>
                <th style="width: 100px; scope="col">gambar</th>
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
                                <!-- <?= $row['name']?> -->
                            </td>
                            <td>
                                <textarea name="editDescRow<?= $row['id']?>" id="editDescRow<?= $row['id']?>" cols="30" rows="10"><?= $row['description']?></textarea>
                                <!-- <?= $row['description']?> -->
                            </td>
                            <td>
                                <select name="editCatRow<?= $row['id']?>" id="editCatRow<?= $row['id']?>">
                                    <?php foreach ($qresult3 as $rowResult3) {?>
                                        <option value="<?= $rowResult3['cat_id']?>"><?= $rowResult3['cat_name']?></option>
                                    <?php }?>
                                </select>
                                <!-- <?= $row['cat_name']?> -->
                            </td>
                            <td>
                                <input type="text" name="editStockRow<?= $row['id']?>" id="editStockRow<?= $row['id']?>" value="<?= $row['stock']?>">
                                <!-- <?= $row['stock']?> -->
                            </td>
                            <td>
                                <input type="text" name="editPriceRow<?= $row['id']?>" id="editPriceRow<?= $row['id']?>" value="<?= $row['price']?>">
                                <!-- <?= $row['price']?> -->
                            </td>
                            <td>
                                <img style="width: 100px;" src="<?= $row['imageLink']?>" alt="">
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
                            <td><img src="<?= $row['imageLink']?>" alt=""></td>
                            <td><?= $row['isActive']?></td>
                        <?php }?>


                        <td>
                        
                            <input type="hidden" name="rowKey" value=<?= $row['id']?>>
                            <!-- <input type="submit" name="addStock" value="Add Stock"><br> -->
                            <button type="button" data-toggle="modal" data-target="#modalForm" onclick="registerIkanId(<?= $row['id']?>)">
                                Add Stock
                            </button>
                            <?php 
                            if ($editIkanId == $row['id']) {?>
                                <input type="submit" name="confEdit" value="Confirm Edit"><br>
                            <?php } else { ?>
                                <input type="submit" name="edit" value="Edit"><br>
                            <?php } ?>
                            <input type="submit" value="Toggle Status">
                        </td>
                    </tr>
                </form>
            <?php }?>
        </tbody>
    </table>
    

    <!-- Tombol untuk memicu modal -->
    <!-- <button data-toggle="modal" data-target="#modalForm" onclick="registerIkanId(param)">
        Buka Contact Form
    </button> -->
    <!-- Modal -->
    <div class="modal fade" id="modalForm" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Tutup</span>
                    </button>
                    <h4 class="modal-title" id="labelModalKu">Contact Form</h4>
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
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary submitBtn" onclick="sendAddStock()">KIRIM</button>
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
</script>
</html>

