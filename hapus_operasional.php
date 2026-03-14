<?php
require 'function.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM transaksi WHERE id_laundry='$id'");

header("Location: table-op.php");
exit;
