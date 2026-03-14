<?php
require 'function.php';

$id = $_GET['id'];
$ambil = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_laundry='$id'");
$row = mysqli_fetch_assoc($ambil);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Transaksi Laundry</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background: linear-gradient(135deg,#eef2f7,#ffffff);
}

.card-custom{
    border-radius:15px;
    border:none;
}

.form-control, .form-select{
    border-radius:10px;
    padding:10px;
}

.label-title{
    font-weight:600;
}

.icon{
    width:22px;
    height:22px;
    vertical-align:middle;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card card-custom shadow-lg">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<div class="d-flex align-items-center gap-2">

<!-- SVG Washing Machine -->
<svg class="icon" fill="white" viewBox="0 0 24 24">
<path d="M3 2h18v20H3V2zm2 4h14V4H5v2zm7 2a6 6 0 100 12 6 6 0 000-12zm0 2a4 4 0 110 8 4 4 0 010-8z"/>
</svg>

<span>Edit Transaksi Laundry</span>

</div>

<span class="badge bg-light text-dark">
<?= $row['kode_order']; ?>
</span>

</div>

<div class="card-body p-4">

<form method="post">

<input type="hidden" name="id_laundry" value="<?= $row['id_laundry']; ?>">

<div class="row g-3">

<div class="col-md-6">

<label class="label-title">Nama Pelanggan</label>

<input type="text"
name="nama"
class="form-control"
value="<?= $row['nama']; ?>"
required>

</div>

<div class="col-md-6">

<label class="label-title">No HP</label>

<input type="text"
name="no_hp"
class="form-control"
value="<?= $row['no_hp']; ?>"
required>

</div>

<div class="col-md-6">

<label class="label-title">Jenis Pencucian</label>

<select name="jenis_pencucian"
class="form-select"
required>

<option value="Cuci Kering"
<?= $row['jenis_pencucian']=="Cuci Kering"?'selected':'' ?>>
Cuci Kering
</option>

<option value="Cuci Setrika"
<?= $row['jenis_pencucian']=="Cuci Setrika"?'selected':'' ?>>
Cuci Setrika
</option>

</select>

</div>

<div class="col-md-6">

<label class="label-title">Jenis Layanan</label>

<select name="jenis_layanan"
class="form-select"
required>

<option value="Reguler"
<?= $row['jenis_layanan']=="Reguler"?'selected':'' ?>>
Reguler
</option>

<option value="Express"
<?= $row['jenis_layanan']=="Express"?'selected':'' ?>>
Express
</option>

</select>

</div>

<div class="col-md-6">

<label class="label-title">Tanggal Penjemputan</label>

<input type="date"
name="tanggal_penjemputan"
class="form-control"
value="<?= $row['tanggal_penjemputan']; ?>"
required>

</div>

<div class="col-md-6">

<label class="label-title">Jam Penjemputan</label>

<input type="time"
name="jam_penjemputan"
class="form-control"
value="<?= $row['jam_penjemputan']; ?>"
required>

</div>

<div class="col-md-12">

<label class="label-title">Status</label>

<select name="status"
class="form-select"
required>

<option value="Menunggu"
<?= $row['status']=="Menunggu"?'selected':'' ?>>
Menunggu
</option>

<option value="Diproses"
<?= $row['status']=="Diproses"?'selected':'' ?>>
Diproses
</option>

<option value="Selesai"
<?= $row['status']=="Selesai"?'selected':'' ?>>
Selesai
</option>

</select>

</div>

</div>

<hr class="my-4">

<div class="d-flex justify-content-between">

<a href="table-op.php"
class="btn btn-outline-secondary d-flex align-items-center gap-2">

<!-- SVG Back Icon -->
<svg class="icon" fill="currentColor" viewBox="0 0 24 24">
<path d="M15 18l-6-6 6-6"/>
</svg>

Kembali

</a>

<button type="submit"
name="update"
class="btn btn-primary d-flex align-items-center gap-2">

<!-- SVG Save Icon -->
<svg class="icon" fill="white" viewBox="0 0 24 24">
<path d="M17 3H5a2 2 0 00-2 2v14h18V7l-4-4zM7 5h8v4H7V5zm5 12a3 3 0 110-6 3 3 0 010 6z"/>
</svg>

Update

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>
