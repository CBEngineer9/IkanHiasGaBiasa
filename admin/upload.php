<?php
    require_once("../proyekpw_lib.php");

    //for file Uploads
    $target_dir = "../assets/img/ikan/";
    $target_file = $target_dir . basename($_FILES["gambarIkan"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['addIkan'])) {
            $namaIkan = $_POST['namaIkan'];
            $descIkan = $_POST['descIkan'];
            $category = $_POST['category'];
            $stock = $_POST['stock'];
            $price = $_POST['price'];
            

            $check = getimagesize($_FILES["gambarIkan"]["tmp_name"]);
            if($check !== false) {
                // echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["gambarIkan"]["tmp_name"], $target_file)) {
                    echo "The file ". htmlspecialchars( basename( $_FILES["gambarIkan"]["name"])). " has been uploaded.";

                    // make entry in db
                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                        // set the PDO error mode to exception
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql = "INSERT INTO `ikan` (`name`, `cat_id`, `stock`, `price`, `imageLink`, `description`) 
                                    VALUES (:name, :cat_id, :stock, :price, :imageLink, :descript);";
                        $stmt = $conn->prepare($sql);
                        $stmt -> bindParam(":name",$namaIkan);
                        $stmt -> bindParam(":cat_id",$category);
                        $stmt -> bindParam(":stock",$stock);
                        $stmt -> bindParam(":price",$price);
                        $stmt -> bindParam(":imageLink",$target_file);
                        $stmt -> bindParam(":descript",$descIkan);
                        $qresult = $stmt -> execute();
                    
                        // echo "<pre>";
                        // var_dump($qresult['isAlive']);
                        // echo "</pre>";
                        
                    } catch(PDOException $e) {
                        echo "Connection failed: " . $e->getMessage();
                    }
                    $conn=null;

                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    }
?>