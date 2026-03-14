<?php
require 'function.php';

$data = null;

if(isset($_POST['cek'])){

    // ambil dari form
    $kode_order = $_POST['kode_order'];

    // query ke database
    $query = mysqli_query($conn,
        "SELECT * FROM transaksi WHERE kode_order='$kode_order'"
    );

    $data = mysqli_fetch_assoc($query);

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Cek Status Laundry</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-5">

<div class="col-md-6 mx-auto">

<div class="card shadow">

<div class="card-header bg-primary text-white">
Cek Status Laundry
</div>

<div class="card-body">

<form method="post">

<label>Kode Order</label>

<input type="text"
name="kode_order"
class="form-control"
placeholder="Contoh: RL-6777"
required>

<button type="submit"
name="cek"
class="btn btn-primary mt-3 w-100">

Cek Status

</button>

</form>

</div>

</div>

</div>

</div>

</body>
</html>
<?php if($data){ ?>

<div class="alert alert-success mt-3">

<b>Nama:</b> <?= $data['nama']; ?> <br>

<b>Kode Order:</b> <?= $data['kode_order']; ?> <br>

<b>Status:</b> <?= $data['status']; ?>

</div>

<?php } ?>

<?php if($status){ ?>

<div class="alert alert-danger mt-3">

<?= $status; ?>

</div>

<?php } ?>
