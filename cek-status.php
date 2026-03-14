<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Status Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f6f9;">

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5>Cek Status Laundry</h5>
        </div>

        <div class="card-body">

            <form method="GET">
                <div class="mb-3">
                    <label>Kode Laundry</label>
                    <input type="text" name="kode" class="form-control" placeholder="Masukkan kode laundry..." required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Cek Status
                </button>
            </form>

        </div>
    </div>

</div>

</body>
</html>
<?php
include "function.php";

if(isset($_GET['kode']))

    $kode = $_GET['kode'];

    $query = mysqli_query($conn, "SELECT * FROM transaksi WHERE kode_order='$kode'");

    if(mysqli_num_rows($query) > 0)
    {
        $data = mysqli_fetch_assoc($query);
?>

<?php if ($data) { ?>

<div class="container mt-4">
<div class="row justify-content-start">
<div class="col-md-6-mb-8">

<div class="card border-0 shadow">

    <div class="card-header text-white text-center"
         style="background: #2563eb;">
        <h5 class="mb-0">Cek Status Laundry</h5>
    </div>

    <div class="card-body text-center p-4">

        <?php
$status_db = strtolower(trim($data['status']));

// default
$status_text = ucfirst($status_db);
$color = "#6b7280";
$progress = 0;

// mapping fleksibel
switch ($status_db) {

    case 'menunggu':
    case 'masuk':
    case 'waiting':
        $status_text = "Masuk";
        $color = "#f59e0b";
        $progress = 33;
        break;

    case 'proses':
    case 'diproses':
    case 'processing':
        $status_text = "Proses";
        $color = "#3b82f6";
        $progress = 66;
        break;

    case 'selesai':
    case 'done':
    case 'complete':
        $status_text = "Selesai";
        $color = "#22c55e";
        $progress = 100;
        break;
}

$radius = 50;
$keliling = 2 * pi() * $radius;
$offset = $keliling - ($progress/100 * $keliling);
?>


        <!-- SVG Progress -->
        <svg width="140" height="140">

            <circle
                cx="70"
                cy="70"
                r="<?= $radius ?>"
                stroke="#e5e7eb"
                stroke-width="10"
                fill="none"/>

            <circle
                cx="70"
                cy="70"
                r="<?= $radius ?>"
                stroke="<?= $color ?>"
                stroke-width="10"
                fill="none"
                stroke-dasharray="<?= $circumference ?>"
                stroke-dashoffset="<?= $offset ?>"
                stroke-linecap="round"
                transform="rotate(-90 70 70)"/>

            <text x="50%" y="50%"
                dominant-baseline="middle"
                text-anchor="middle"
                font-size="16"
                font-weight="bold"
                fill="<?= $color ?>">
                <?= $progress ?>%
            </text>

        </svg>

        <h4 class="mt-3" style="color:<?= $color ?>">
            <?= $status_text ?>
        </h4>

        <hr>

        <div class="text-start">

            <p class="mb-1">
                <strong>Nama:</strong><br>
                <?= $data['nama'] ?>
            </p>

            <p class="mb-1">
                <strong>No HP:</strong><br>
                <?= $data['no_hp'] ?>
            </p>

            <p class="mb-0">
                <strong>Layanan:</strong><br>
                <?= $data['jenis_layanan'] ?>
            </p>

        </div>

    </div>

</div>

</div>
</div>
</div>

<?php } ?>

<?php
    }
    else
    {
        echo "<div class='alert alert-danger mt-3'>Kode tidak ditemukan</div>";
    }

?>
