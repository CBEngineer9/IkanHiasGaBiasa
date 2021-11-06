<?php
    require_once("proyekpw_lib.php");

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $conn->prepare("SELECT id, firstname, lastname FROM myGuest;");
        // $stmt -> execute();

        // $sql = "Select * From ikan;";
        $sql = "SELECT ikan.id, ikan.name, ikan.stock, ikan.price, ikan.imageLink, ikan.description, category.cat_name 
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

        echo "fetched successfully";
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($qResult as $key => $row) { ?>
                <tr>
                    <td><?= $row['name']?></td>
                    <td><?= $row['description']?></td>
                    <td><?= $row['cat_name']?></td>
                    <td><?= $row['stock']?></td>
                    <td><?= $row['price']?></td>
                    <td><img src="<?= $row['imageLink']?>" alt=""></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</body>
</html>

