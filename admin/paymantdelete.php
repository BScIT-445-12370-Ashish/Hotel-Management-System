<?php
ob_start();
session_start();
include '../config.php';

$id = $_GET['id'];

$deletesql = "DELETE FROM payment WHERE id = $id";

$result = mysqli_query($conn, $deletesql);

header("Location:payment.php");

?>