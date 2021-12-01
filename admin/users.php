<?php
    require_once("../proyekpw_lib.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST"){

    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql2 = "SELECT * FROM `users`;";
        $stmt2 =  $conn -> prepare($sql2);
        // $stmt2 -> bindParam(":idUser",$_SESSION['id_user']);
        $stmt2 -> execute();
        $qresult2 = $stmt2 -> fetchAll();

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn=null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserList</title>
    <link rel="icon" href="assets/img/Logo/favicon.ico">
    <!-- <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" /> -->

    <!-- bootstrap css -->
    <link rel="stylesheet" href="../assets/bootstrap5/bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <!-- library jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</head>
<style>
    body{
        padding: 10px;
    }
    td{
        text-align: center;
    }
    th{
        text-align: center;
    }
</style>
<body>
    <h3>Users</h3>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Password</th>
                <th>Email</th>
                <th>Phone</th>
                <th>History</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($qresult2 as $row) {?>
                <tr>
                    <td><?= $row['id']?></td>
                    <td><?= $row['username']?></td>
                    <td><?= "password here" //$row['password'] ?></td>
                    <td><?= $row['email']?></td>
                    <td><?= $row['phone']?></td>
                    <td>
                        <form action="history.php" method="get">
                            <input type="hidden" name="userid" value="<?= $row['id']?>">
                            <input class="btn btn-dark" type="submit" value="History">
                        </form>
                    </td>
                    <td>
                        <button onclick="delUserConf('<?= $row['id']?>','<?= $row['username']?>')" class="btn btn-danger" value="Delete">Delete</button>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>

    <div id="delConfModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure to delete user <span id="delUsername"></span>.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button onclick="delUserReal()" type="button" class="btn btn-danger" data-bs-dismiss="modal">Delete User</button>
            </div>
            </div>
        </div>
    </div>
</body>
<script>
    var delConfModal = new bootstrap.Modal(document.getElementById('delConfModal'));
    var userToDel;
    function delUserConf(id,username) {
        userToDel = id;
        document.getElementById('delUsername').innerHTML = username;
        delConfModal.show();
    }
    function delUserReal() {
        console.log(userToDel);
        $.ajax({
            type:"get",
            url:"admin_ajax.php",
            data:{
                'action':'deleteUser',
                'user_id':userToDel,
            },
            success:function(response){
                location.reload();
                // TODO CHECK
                // deactive?
            },
            error:function(response){
                alert("AJAX ERROR " + response);
            }
        });
    }
</script>
</html>

