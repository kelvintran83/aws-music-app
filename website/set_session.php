<?php
session_start();
$_SESSION["username"] = $_POST["username"];
echo "success";
?>