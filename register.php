<?php
    require_once("proyekpw_lib.php");

    $usersList = [];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $conn->prepare("SELECT id, firstname, lastname FROM myGuest;");
        // $stmt -> execute();

        $sql = "Select username From users;";
        $result = $conn->query($sql);
        foreach ($result as $baris) {
            array_push($usersList,$baris['username']);
        }

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn=null;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['halamanlogin'])) {
            header("Location: login.php");
        }
        if (isset($_POST['btregis'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirmpass = $_POST["confirmpass"];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];

            // make conn here

            
            if (empty($username) || empty($password) || empty($confirmpass) || empty($email)) {
                echo '<script>alert("Field tidak boleh kosong")</script>';
            } else if ($password != $confirmpass) {
                echo '<script>alert("Confirm password harus sama")</script>';
            } else {
                $foundUser = false;
                foreach ($usersList as $user) {
                    if (strtolower($user) == strtolower($username)) {
                        $foundUser = true;
                        break;
                    }
                }
                if ($foundUser) {
                    echo '<script>alert("Username telah terpakai ")</script>';
                } else  {
                    // insert new user
                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
                        // set the PDO error mode to exception
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                        $sql = "INSERT INTO `users` (`username`, `password`, `email`, `phone`, `firstname`, `lastname`, `isAdmin`) VALUES (:user, :pass, :email, :phone, :fname, :lname, '0')";
                        $stmt = $conn -> prepare($sql);
                        $stmt -> bindParam(":user",$username);
                        $stmt -> bindParam(":pass",$password);
                        $stmt -> bindParam(":email",$email);
                        $stmt -> bindParam(":phone",$phone);
                        $stmt -> bindParam(":fname",$fname);
                        $stmt -> bindParam(":lname",$lname);

                        $insertResult = $stmt -> execute();

                        if ($insertResult) {
                            echo '<script>alert("Register Berhasil ")</script>';
                        } else {
                            echo '<script>alert("Register Gagal")</script>';
                        }
                    } catch(PDOException $e) {
                        echo "Connection failed: " . $e->getMessage();
                    }
                    $conn=null;
                }
            }
            
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/login.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <title>Register</title>
    <link rel="icon" href="assets/img/Logo/favicon.ico">
</head>
<body>
    <form action="" method="post" style="height: 50vh;">
    <div class="container px-4 py-4 mx-auto" style="margin-top: 1.5vh;">
    <div class="card card0">
        <div class="d-flex flex-lg-row flex-column-reverse">
            <div class="card card1">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-10 my-5">
                        <div class="row justify-content-center px-3 mb-3"> <img id="logo" src="assets/img/Logo/tumblr_myvpf71CVu1spjmmdo1_1280.png"> </div>
                        <h3 class="mb-5 text-center heading">Sign In</h3>
                        <h6 class="msg-info">Please Fill Up All the Blanks</h6>
                        <div class="form-group"> <label for="username" class="form-control-label text-muted">Username</label> <input type="text" name="username" id="username" placeholder="Username" class="form-control"> </div>
                        <div class="form-group"> <label for="fname" class="form-control-label text-muted">First Name</label> <input type="text" name="fname" placeholder="First Name" class="form-control"> </div>
                        <div class="form-group"> <label for="lname" class="form-control-label text-muted">Last Name</label> <input type="text" name="lname" placeholder="Last Name" class="form-control"> </div>
                        <div class="form-group"> <label for="email" class="form-control-label text-muted">Email</label> <input type="text" name="email" placeholder="Email" class="form-control"> </div>
                        <div class="form-group"> <label for="phone" class="form-control-label text-muted">Phone</label> <input type="text" name="phone" placeholder="Phone" class="form-control"> </div>
                        <div class="form-group"> <label for="password" class="form-control-label text-muted">Password</label> <input type="password" name="password" id="password" placeholder="Password" class="form-control"> </div>
                        <div class="form-group"> <label for="confirmpass" class="form-control-label text-muted">Confirm Password</label> <input type="password" name="confirmpass" placeholder="Confirm Password" class="form-control"> </div>
                        <div class="row justify-content-center my-3 px-3"> <button type="submit" name="btregis"  class="btn-block btn-color">Sign Up</button> </div>
                    </div>
                </div>
                <div class="bottom text-center mb-5">
                    <p href="#" class="sm-text mx-auto mb-3">Already have an account?<button style="margin-left: 1vw; border-radius:20px;" type="submit" name="halamanlogin" class="btn btn-white ml-2">Sign In</button></p>
                </div>
            </div>
            <div class="card card2">
                <div class="my-auto mx-md-5 px-md-5 right">
                </div>
            </div>
        </div>
    </div>
</div>
    </form>
</body>
<script src="./assets/bootstrap5/bootstrap-5.1.3-dist/js/bootstrap.min.js"></script>
</html>