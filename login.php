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
        echo "<pre>";
        // print_r($result);
        foreach ($result as $baris) {
            // print_r($baris);
            print $baris["username"] . "\t";
            print $baris["password"] . "\t";

            $usersTable[$baris["username"]] = [
                "password" => $baris["password"],
            ];
        }
        echo "</pre>";

        echo "Users fetched successfully";
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
                    echo "<pre>";
                    var_dump($usersTable);
                    echo "</pre>";
                    if (!isset($usersTable[$username])) {
                        echo '<script>alert("Username tidak ditemukan")</script>';
                    } else {
                        if ($usersTable[$username]['password'] == $password) {
                            // hal user
                            // header("Location:user.php?usersTable=".json_encode($usersTable)."&curr=".$username);
                            // set session
                            $_SESSION["currUser"] = $username;
                            echo "yay";
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
    <title>Document</title>
</head>
<body>
    <h1>Login</h1>
    <form action="#" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username"><br>
        <label for="password">Password:</label>
        <input type="text" name="password" id="password"><br>
        <input type="submit" name="login" value="Login">
        <!-- <input type="submit" name="register" value="Menuju Halaman Register"> -->
    </form>
</body>
</html>