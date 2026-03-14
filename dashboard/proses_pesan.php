<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Proses Pesan dari Website
 *  Menerima POST dari form front-end → INSERT ke transaksi
 *  Lalu redirect ke WhatsApp admin
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Require login — cegah fake pelanggan
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

// Get & validate input
$nama      = trim($_POST['nama'] ?? '');
$no_hp     = trim($_POST['no_hp'] ?? '');
$alamat    = trim($_POST['alamat'] ?? '');
$pesan     = trim($_POST['pesan'] ?? '');
$pencucian = trim($_POST['jenis_pencucian'] ?? '');
$layanan   = trim($_POST['jenis_layanan'] ?? '');
$tanggal   = $_POST['tanggal_pengiriman'] ?? '';
$jam       = $_POST['jam_pengiriman'] ?? '';
$latitude  = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
$longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

if (empty($nama) || empty($no_hp) || empty($alamat) || empty($pencucian) || empty($layanan) || empty($tanggal) || empty($jam)) {
    header('Location: ../index.php?status=error');
    exit;
}

// Auto-generate kode order
$kode_order = 'RL-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

// 1. Insert into TRANSAKSI table (with id_user link)
$id_user = (int) $_SESSION['id'];
$stmt1 = $conn->prepare("INSERT INTO transaksi (kode_order, nama, no_hp, alamat, pesan, jenis_pencucian, jenis_layanan, tanggal_penjemputan, jam_penjemputan, latitude, longitude, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt1->bind_param('sssssssssddi', $kode_order, $nama, $no_hp, $alamat, $pesan, $pencucian, $layanan, $tanggal, $jam, $latitude, $longitude, $id_user);

// 2. Insert into PENGIRIMAN table (sertakan kode_order untuk linking)
$stmt2 = $conn->prepare("INSERT INTO pengiriman (kode_order, nama, no_hp, alamat, pesan, jenis_pencucian, jenis_layanan, tanggal_pengiriman, jam_pengiriman, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt2->bind_param('sssssssssdd', $kode_order, $nama, $no_hp, $alamat, $pesan, $pencucian, $layanan, $tanggal, $jam, $latitude, $longitude);

if ($stmt1->execute() && $stmt2->execute()) {
    // Build WhatsApp message
    $waNumber = '6282295333441'; // Nomor WhatsApp admin
    $waMessage = "*Pesanan Baru — Rumah Laundry*\n\n"
        . "*Kode Order:* {$kode_order}\n"
        . "*Nama:* {$nama}\n"
        . "*No HP:* {$no_hp}\n"
        . "*Alamat:* {$alamat}\n"
        . "*Jenis Pencucian:* {$pencucian}\n"
        . "*Jenis Layanan:* {$layanan}\n"
        . "*Tanggal:* {$tanggal}\n"
        . "*Jam:* {$jam}\n";
    if (!empty($pesan)) {
        $waMessage .= "*Pesan:* {$pesan}\n";
    }
    if ($latitude && $longitude) {
        $waMessage .= "*Lokasi:* https://www.google.com/maps?q={$latitude},{$longitude}\n";
    }
    $waMessage .= "\nTerima kasih telah menggunakan Rumah Laundry! ";

    $waUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($waMessage);

    // Redirect to WhatsApp
    header('Location: ' . $waUrl);
    exit;
} else {
    header('Location: ../front-end/index.php?status=error');
    exit;
}
