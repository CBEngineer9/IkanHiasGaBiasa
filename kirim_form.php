<?php
    require_once("proyekpw_lib.php");

// if(isset($_POST['contactFrmSubmit']) && !empty($_POST['nama']) && !empty($_POST['email']) && (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) && !empty($_POST['pesan'])){
//     // data form yang dikirimkan
//     $nama   = $_POST['nama'];
//     $email  = $_POST['email'];
//     $pesan= $_POST['pesan'];
//     /*
//      * Kirim email ke alamat dibawah ini
//      */
//     $ke     = 'demo@codingan.com';
//     $subjek= 'Nyoba Contact Form';
//     $kontenHtml = '
//     <h4>permintaan kontak telah disampaikan pada Codingan, berikut ini rinciannya.</h4>
//     <table cellspacing="0" style="width: 300px; height: 200px;">
//         <tr>
//             <th>Nama:</th><td>'.$nama.'</td>
//         </tr>
//         <tr style="background-color: #e0e0e0;">
//             <th>Email:</th><td>'.$email.'</td>
//         </tr>
//         <tr>
//             <th>Pesan:</th><td>'.$pesan.'</td>
//         </tr>
//     </table>';
//     // Mengatur header content-type untuk mengirim email HTML
//     $headers = "MIME-Version: 1.0" . "\r\n";
//     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//     // header tambahan
//     $headers .= 'From: '.$nama.'<'.$email.'>' . "\r\n";
//     // Kirim email
//     if(mail($ke,$subjek,$kontenHtml,$headers)){
//         $status = 'ok';
//     }else{
//         $status = 'err';
//     }
//     // status output
//     echo $status;die;
// }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $ikanId = $_POST['ikanId'];
        $tambStock = $_POST['tambStock'];
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT stock FROM ikan WHERE `ikan`.`id` = :ikanId;";
            $stmt = $conn->prepare($sql);
            $stmt -> bindParam(":ikanId",$ikanId);
            $stmt -> execute() ;
            $qResult1 = $stmt -> fetch(PDO::FETCH_ASSOC);

            $initialStock = $qResult1['stock'];
            $newStock = $initialStock + $tambStock;
    
            $sql = "UPDATE `ikan` SET `stock` = :newStock WHERE `ikan`.`id` = :ikanId";
            $stmt = $conn->prepare($sql);
            $stmt -> bindParam(":ikanId",$ikanId);
            $stmt -> bindParam(":newStock",$newStock);
            $qResult2 = $stmt -> execute();
    
            // echo "<pre>";
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
            
            $status = $newStock;
        } catch(PDOException $e) {
            // alert("Connection failed: " . $e->getMessage());
            $status = -1;
        }
        $conn=null;

        // status output
        echo $status;die;
    }
?>