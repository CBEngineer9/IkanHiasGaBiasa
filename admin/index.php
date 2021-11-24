<?php
require_once("../proyekpw_lib.php");

// unset($_SESSION['currUser']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    <link rel="icon" href="assets/img/Logo/favicon.ico">
    <link rel="stylesheet" href="../assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
</head>

<body>
    <br><br><br>
    <div class="container py-3" style="border:1px solid lightgray; border-radius:10px">
        <h1>Login</h1>
        <form action="#" method="post">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Username</label>
                <input type="text" class="form-control" name="user" id="user" placeholder="Username"><br>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="text" class="form-control" name="pass" id="pass" placeholder="Password"><br>
            </div>
            <button type="submit" value="Login" name="login" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>

</html>