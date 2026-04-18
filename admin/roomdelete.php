<?php
ob_start();
session_start();
include '../config.php';

$id = $_GET['id'];

$roomdeletesql = "DELETE FROM room WHERE id = $id";

$result = mysqli_query($conn, $roomdeletesql);

header("Location:room.php");

?>