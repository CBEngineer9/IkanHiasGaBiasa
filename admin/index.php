<?php
    require_once("../proyekpw_lib.php");

    // unset($_SESSION['currUser']);

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $user = $_POST['user'];
        $pass = $_POST['pass'];

        if ($user == 'admin' && $pass == 'admin') {
            # admin
            $_SESSION['admin'] = true;
            header("Location:control.php");
        } else {
            echo "Wrong";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h1>Login</h1>
    <form action="#" method="post">
        Username :<input type="text" name="user" id="user" placeholder="Username"><br>
        Password:<input type="text" name="pass" id="pass" placeholder="Password"><br>
        <input type="submit" value="Login" name="login">
    </form>
</body>
</html>