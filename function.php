<?php
session_start();

$conn = new mysqli("localhost", "root", "", "proyek_laundry");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// ── Auto-migrate: perluas no_hp & tambah kolom baru ──
try {
    $conn->query("UPDATE transaksi SET no_hp = '' WHERE no_hp IS NULL");
    $conn->query("ALTER TABLE transaksi MODIFY COLUMN no_hp VARCHAR(50) NOT NULL DEFAULT ''");
} catch (Exception $e) { /* abaikan */ }
$_mig = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='proyek_laundry' AND TABLE_NAME='transaksi' AND COLUMN_NAME='alamat'");
if ($_mig && $_mig->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN alamat VARCHAR(255) NOT NULL DEFAULT ''");
}
$_mig = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='proyek_laundry' AND TABLE_NAME='transaksi' AND COLUMN_NAME='whatsapp_sent'");
if ($_mig && $_mig->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN whatsapp_sent TINYINT(1) NOT NULL DEFAULT 0");
}

/*
====================================
CEK STATUS BERDASARKAN KODE ORDER
====================================
*/
if(isset($_POST['kode_order'])){

    $kode = $_POST['kode_order'];

    $query = mysqli_query($conn, 
        "SELECT status FROM transaksi WHERE kode_order='$kode'"
    );

    if(mysqli_num_rows($query) > 0){

        $data = mysqli_fetch_assoc($query);
        echo $data['status'];

    } else {

        echo "Kode tidak ditemukan";

    }

    exit;
}


/*
====================================
CREATE / SIMPAN DATA
====================================
*/
if (isset($_POST['simpan'])) {

    // generate kode order unik
    $kode_order = "RL-" . rand(1000, 9999);

    // ambil data dari form
    $nama                = $_POST['nama'];
    $no_hp               = substr(trim($_POST['no_hp'] ?? ''), 0, 50); // aman dari data too long
    $jenis_pencucian     = $_POST['jenis_pencucian'];
    $jenis_layanan       = $_POST['jenis_layanan'];
    $tanggal_penjemputan = $_POST['tanggal_penjemputan'];
    $jam_penjemputan     = $_POST['jam_penjemputan'];

    // status default
    $status = "Menunggu";

    $query = "INSERT INTO transaksi
        (kode_order, nama, no_hp, jenis_pencucian, jenis_layanan, tanggal_penjemputan, jam_penjemputan, status)
        VALUES
        ('$kode_order', '$nama', '$no_hp', '$jenis_pencucian', '$jenis_layanan', '$tanggal_penjemputan', '$jam_penjemputan', '$status')
    ";

    if(mysqli_query($conn, $query)){

        // simpan kode_order ke session supaya bisa ditampilkan
        $_SESSION['kode_order_terakhir'] = $kode_order;

    }else{

        echo "Gagal simpan data";
        exit;

    }

    header("Location: table-op.php");
    exit;
}


/*
====================================
UPDATE DATA
====================================
*/
if (isset($_POST['update'])) {

    $id                  = $_POST['id_laundry'];
    $nama                = $_POST['nama'];
    $no_hp               = substr(trim($_POST['no_hp'] ?? ''), 0, 50); // aman dari data too long
    $jenis_pencucian     = $_POST['jenis_pencucian'];
    $jenis_layanan       = $_POST['jenis_layanan'];
    $tanggal_penjemputan = $_POST['tanggal_penjemputan'];
    $jam_penjemputan     = $_POST['jam_penjemputan'];
    $status              = $_POST['status'];

    $query = "UPDATE transaksi SET
        nama='$nama',
        no_hp='$no_hp',
        jenis_pencucian='$jenis_pencucian',
        jenis_layanan='$jenis_layanan',
        tanggal_penjemputan='$tanggal_penjemputan',
        jam_penjemputan='$jam_penjemputan',
        status='$status'
        WHERE id_laundry='$id'
    ";

    mysqli_query($conn, $query);

    header("Location: table-op.php");
    exit;
}


/*
====================================
DELETE DATA
====================================
*/
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM transaksi WHERE id_laundry='$id'");

    header("Location: table-op.php");
    exit;
}
?>
<?php


if(isset($_SESSION['kode_order_terakhir'])){
    echo "<div class='alert alert-success'>
            Kode Order berhasil dibuat: <b>" . $_SESSION['kode_order_terakhir'] . "</b>
          </div>";

    unset($_SESSION['kode_order_terakhir']);
}
?>
