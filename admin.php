<?php
    require_once("proyekpw_lib.php");

    $editStockRow = -1;

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

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['addStock'])) {
            $editStockRow = $_POST['rowKey'];
        }
        if (isset($_POST['confStock'])) {
            
        }
    }
    echo $editStockRow;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" />

</head>
<body>
    <h3>Users</h3>
    <table border="1">
        <thead>
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
        <label for="namaIkan"> Nama Ikan : </label>
        <input type="text" name="namaIkan" id="namaIkan"><br>
        <label for="descIkan">desc : </label><br>
        <textarea name="descIkan" id="descIkan" cols="30" rows="10"></textarea><br>
        <label for="category">category : </label>
        <select name="category" id="category">
            <?php foreach ($qresult3 as $row) {?>
                <option value="<?= $row['cat_id']?>"><?= $row['cat_name']?></option>
            <?php }?>
        </select><br>
        <label for="stock">stock : </label>
        <input type="text" name="stock" id="stock"><br>
        <label for="price">price : </label>
        <input type="text" name="price" id="price"><br>
        <label for="gambarIkan">image : </label>
        <input type="file" name="gambarIkan" id="gambarIkan"><br>
        <input type="submit" name="addIkan" value="Add">
    </form>

    <h3>Ikan</h3>
    <table border="1">
        <thead>
            <tr>
                <th>nama</th>
                <th>desc</th>
                <th>category</th>
                <th>stock</th>
                <th>harga</th>
                <th>gambar</th>
                <th>isActive</th>
                <th>action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($qResult as $key => $row) { ?>
                <tr>
                    <td><?= $row['name']?></td>
                    <td><?= $row['description']?></td>
                    <td><?= $row['cat_name']?></td>
                    <td>
                        <?php 
                            if ($editStockRow == $row['id']) {?>
                                <input type="text" name="editRow<?= $row['id']?>" id="editRow<?= $row['id']?>" value="<?= $row['stock']?>">
                        <?php } else {
                                echo $row['stock'];
                            }
                        ?>
                    </td>
                    <td><?= $row['price']?></td>
                    <td><img src="<?= $row['imageLink']?>" alt=""></td>
                    <td><?= $row['isActive']?></td>
                    <td>
                        <form action="#" method="post">
                            <input type="hidden" name="rowKey" value=<?= $row['id']?>>
                            <?php 
                            if ($editStockRow == $row['id']) {?>
                                <input type="submit" name="confStock" value="Confirm Edit Stock"><br>
                            <?php } else { ?>
                                <input type="submit" name="addStock" value="Add Stock"><br>
                            <?php } ?>
                            <input type="submit" value="Edit"><br>
                            <input type="submit" value="Toggle Status">
                        </form>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>


    <div id="yourPopup" style="padding:0; margin:0; display:none;">

    </div>
</body>
<script>
    function showyourPopup() {
        $("#yourPopup").dialog({
            autoOpen: true,
            resizable: false,
            height: 'auto',
            width: 'auto',
            modal: true,
            //show: { effect: "puff", duration: 300 }, 
            draggable: true
        });

        $(".ui-widget-header").css({"display":"none"}); 
    }

function closeyourPopup() { $("#yourPopup").dialog('close'); }

/* Submit Resources Popup */

function submitResources(id){   

    $("#yourPopup").dialog( "open" );

    $.ajax({
        url:'testPopup.php',
        data:'act=loadResourcesFrm&id='+id,
        type:'POST',
        error:function(){},
        success:function(data){ 
            $('#yourPopup').html(data); 
            showyourPopup();
        }
    });
}

$(function() {
    console.log( "ready!" );
});
</script>
</html>

