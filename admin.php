<?php
    require_once("proyekpw_lib.php");

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $conn->prepare("SELECT id, firstname, lastname FROM myGuest;");
        // $stmt -> execute();

        $sql = "Select * From ikan;";
        $qResult = $conn->query($sql)->fetchAll();
        echo "<pre>";
        // print_r($qResult);
        foreach ($qResult as $baris) {
            // print_r($baris);
            print $baris["id"] . "\t";
            print $baris["name"] . "\t";
            print $baris["category"] . "\t";
            print $baris["stock"] . "\t";
            print $baris["price"] . "\t";
            print $baris["imageLink"] . "\t";
            print $baris["description"] . "\t";
        }
        echo "</pre>";

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
                    <td><?= $row['category']?></td>
                    <td><?= $row['stock']?></td>
                    <td><?= $row['price']?></td>
                    <td><img src="<?= $row['imageLink']?>" alt=""></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</body>
</html>