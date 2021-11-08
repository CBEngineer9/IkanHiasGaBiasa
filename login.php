<?php
    require_once("proyekpw_lib.php");

    $usersTable;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $conn->prepare("SELECT id, firstname, lastname FROM myGuest;");
        // $stmt -> execute();

        $sql = "Select username,password From users;";
        $result = $conn->query($sql);
        //echo "<pre>";
        // print_r($result);
        foreach ($result as $baris) {
            // print_r($baris);
            //print $baris["username"] . "\t";
            //print $baris["password"] . "\t";

            $usersTable[$baris["username"]] = [
                "password" => $baris["password"],
            ];
        }
        //echo "</pre>";

        //echo "Users fetched successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn=null;

    // if(isset($_GET['usersTable'])){
    //     $usersTable=json_decode($_GET['usersTable'], true);
    // }else{
    //     $usersTable=[];
    // }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['register'])) {
            header("Location: register.php?usersTable=".json_encode($usersTable));
        }
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // make conn here

            if ($username === "admin" && $password==="admin") {
                //hal admin
                header("Location:admin.php");
            } else {
                if (empty($username) || empty($password)) {
                    echo '<script>alert("Field tidak boleh kosong")</script>';
                } else {
                    //echo "<pre>";
                    //var_dump($usersTable);
                    //echo "</pre>";
                    if (!isset($usersTable[$username])) {
                        echo '<script>alert("Username tidak ditemukan")</script>';
                    } else {
                        if ($usersTable[$username]['password'] == $password) {
                            // hal user
                            // header("Location:user.php?usersTable=".json_encode($usersTable)."&curr=".$username);
                            // set session
                            $_SESSION["currUser"] = $username;
                            //echo "yay";
                        } else {
                            echo '<script>alert("Password salah")</script>';
                        }
                    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <form action="" method="post" style="height: 80vh;">
    <div class="container px-4 py-5 mx-auto" style="margin-top:8vh">
    <div class="card card0">
        <div class="d-flex flex-lg-row flex-column-reverse">
            <div class="card card1">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-10 my-5">
                        <div class="row justify-content-center px-3 mb-3"> <img id="logo" src="../IkanHiasGaBiasa/assets/img/Logo/tumblr_myvpf71CVu1spjmmdo1_1280.png"> </div>
                        <h3 class="mb-5 text-center heading">Sign In</h3>
                        <h6 class="msg-info">Please login to your account</h6>
                        <div class="form-group"> <label for="username" class="form-control-label text-muted">Username</label> <input type="text" name="username" id="username" placeholder="Username" class="form-control"> </div>
                        <div class="form-group"> <label for="password" class="form-control-label text-muted">Password</label> <input type="password" name="password" id="password" placeholder="Password" class="form-control"> </div>
                        <div class="row justify-content-center my-3 px-3"> <button type="submit" name="login"  class="btn-block btn-color">Sign In</button> </div>
                    </div>
                </div>
                <div class="bottom text-center mb-5">
                    <p href="#" class="sm-text mx-auto mb-3">Don't have an account?<button style="margin-left: 1vw; border-radius:20px;" type="submit" name="register" class="btn btn-white ml-2">Register</button></p>
                </div>
            </div>
            <div class="card card2">
                <div class="my-auto mx-md-5 px-md-5 right">
                    <h3 class="text-black">We are more than just a company</h3> <big class="text-black">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</small>
                </div>
            </div>
        </div>
    </div>
</div>
    </form>
</body>
</html>