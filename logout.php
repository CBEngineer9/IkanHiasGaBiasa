<?php
    require_once("proyekpw_lib.php");
    unset($_SESSION['currUser']);
    unset($_SESSION['currUsername']);
    header("Location: index.php");
?>