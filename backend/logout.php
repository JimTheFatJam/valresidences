<?php
require_once '../backend/db_connect.php';
session_start();
session_destroy();
header("location: ../pages/index.php");
exit();
?>