<?php
session_start();

if (!isset($_SESSION["authsuper"]) || $_SESSION["authsuper"] != "1") {
    header("location: login-super.php");
    exit();
}
?>