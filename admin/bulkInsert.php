<?php
    require_once("../proyekpw_lib.php");

    // set file target
    $target_dir = "../assets/img/ikan/";
    $db_entry_dir = "assets/img/ikan/";
    $uploadOk = 1;

    //unzip
    $file = $_FILES["imageZip"]["tmp_name"];

    // get the absolute path to $file
    // $path = pathinfo(realpath($file), PATHINFO_DIRNAME);

    //set unzip path
    $path = "../assets/img/temp_bulk_" . rand();
    
    //make temp dir
    if (!mkdir($path, 0777)) {
        die('Failed to create temp directories. Aborting..');
    }
    
    $zip = new ZipArchive;
    $res = $zip->open($file);
    if ($res === TRUE) {
        // extract it to the path we determined above
        $zip->extractTo($path);
        $zip->close();
        echo "$file extracted to $path";
    } else {
        die("couldn't open $file. Aborting..");
    }

    $row = 1;
    if (($handle = fopen($_FILES["fishCSV"]["tmp_name"], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            echo "<p> $num fields in line $row: <br /></p>\n";
            // check problematic row
            if ($num != 8) {
                echo("Number of values doesn't match database. Skipping..");
            } else {
                $row++;
                // handle csv
                // for ($c=0; $c < $num; $c++) {
                //     echo $data[$c] . "<br />\n";
                // }
                echo "Name      : " . $data[0] . "<br />\n";
                echo "cat id    : " . $data[1] . "<br />\n";
                echo "stock     : " . $data[2] . "<br />\n";
                echo "price     : " . $data[3] . "<br />\n";
                echo "satuan    : " . $data[4] . "<br />\n";
                echo "imageName : " . $data[5] ;
                $check = getimagesize($path ."/". $data[5]);
                if($check !== false) {
                    echo " - File is an image - " . $check["mime"] . ". OK <br />\n";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image. Skipping entry..<br />\n";
                    $uploadOk = 0;
                }
                if (check_file_uploaded_name($data[5])) {
                    echo "File contains illegal character. Skipping entry..<br />\n";
                    $uploadOk = 0;
                }
                if (check_file_uploaded_length($data[5])) {
                    echo "File name too long. Skipping entry..<br />\n";
                    $uploadOk = 0;
                }
                echo "desc      : " . $data[6] . "<br />\n";
                echo "isActive  : " . $data[7] . "<br />\n";

                // get Auto Increment value
                $nextAI = -1;
                try {
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                    // set the PDO error mode to exception
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $sql = "SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'ikanhia1_proyekpw' AND TABLE_NAME   = 'ikan';";
                    $stmt = $conn->prepare($sql);
                    $stmt -> execute();
                    $qresult = $stmt -> fetch(PDO::FETCH_ASSOC);
                    $nextAI = $qresult['AUTO_INCREMENT'];
                    
                } catch(PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                }
                $conn=null;

                //for file Uploads
                $imageFileType = strtolower(pathinfo($path ."/". $data[5],PATHINFO_EXTENSION));
                $target_file = $target_dir . "ikan" . $nextAI . "." . $imageFileType;
                $db_target_file = $db_entry_dir . "ikan" . $nextAI . "." . $imageFileType;

                echo "Trying to move" . $path ."/". $data[5] . " to " . $target_file . " <br />\n";

                // if everything is ok, try to upload file
                if ($uploadOk == 1) {
                    if (rename($path ."/". $data[5], $target_file)) {
                        echo "The file ". htmlspecialchars( $data[5]). " has been uploaded. <br />\n";
    
                        // and make entry in db
                        try {
                            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                            // set the PDO error mode to exception
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $sql = "INSERT INTO `ikan` (`name`, `cat_id`, `stock`, `price`, `satuan`, `imageLink`, `description`, isActive) 
                                        VALUES (:name, :cat_id, :stock, :price, :satuan,:imageLink, :descript, :isActive);";
                            $stmt = $conn->prepare($sql);
                            $stmt -> bindValue(":name",$data[0]);
                            $stmt -> bindValue(":cat_id",$data[1]);
                            $stmt -> bindValue(":stock",$data[2]);
                            $stmt -> bindValue(":price",$data[3]);
                            $stmt -> bindValue(":satuan",$data[4]);
                            $stmt -> bindValue(":imageLink",$db_target_file);
                            $stmt -> bindValue(":descript",$data[6]);
                            $stmt -> bindValue(":isActive",$data[7]);
                            $qresult = $stmt -> execute();
                            
                        } catch(PDOException $e) {
                            echo "Connection failed: " . $e->getMessage();
                        }
                        $conn=null;
    
                    } else {
                        echo "Sorry, there was an error uploading your file. Skipping entry..<br />\n";
                    }
                }
            }

        }
        fclose($handle);
    }
    echo ".<br />\n";

    // empty temp folder
    echo "Emptying temp folder..<br />\n";
    $files = glob($path.'/*'); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file)) {
            unlink($file); // delete file
        }
    }
    echo "Temp folder successfully emptied.<br />\n";

    echo "Deleting temp folder...<br />\n";
    if (!rmdir($path)) { // delete folder
        die("[ERROR] Failed to delete temp folder");
    }
    echo "Temp folder successfully deleted.<br />\n";

    echo "Bulk Insert Finished <br />\n";
    
?>