<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Tambah Transaksi
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode    = 'RL-' . rand(1000, 9999);
    $nama    = trim($_POST['nama']);
    $hp      = trim($_POST['no_hp']);
    $pesan   = trim($_POST['pesan']);
    $cuci    = $_POST['jenis_pencucian'];
    $layanan = $_POST['jenis_layanan'];
    $tgl     = $_POST['tanggal'];
    $jam     = $_POST['jam'];

    $stmt = $conn->prepare("INSERT INTO transaksi (kode_order, nama, no_hp, pesan, jenis_pencucian, jenis_layanan, tanggal_penjemputan, jam_penjemputan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssss', $kode, $nama, $hp, $pesan, $cuci, $layanan, $tgl, $jam);
    
    if ($stmt->execute()) {
        setFlash('success', 'Transaksi berhasil ditambahkan.');
        header('Location: transaksi.php');
        exit;
    } else {
        $error = "Terjadi kesalahan database.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Transaksi — Rumah Laundry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body style="background:#FFF0F3;">
    <div style="max-width:600px;margin:50px auto;background:#fff;padding:30px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom:20px;color:#FF385C;"><i class="fas fa-plus-circle"></i> Tambah Transaksi</h2>
        
        <?php if(isset($error)): ?>
            <div style="background:#fee2e2;color:#dc2626;padding:10px;border-radius:8px;margin-bottom:15px;"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" style="display:grid;gap:15px;">
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Nama Pelanggan</label>
                <input type="text" name="nama" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">No HP</label>
                <input type="text" name="no_hp" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Jenis Pencucian</label>
                    <select name="jenis_pencucian" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                        <option>Cuci Kering</option>
                        <option>Cuci Setrika</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Jenis Layanan</label>
                    <select name="jenis_layanan" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                        <option>Reguler</option>
                        <option>Express</option>
                        <option>Antar Jemput</option>
                    </select>
                </div>
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Pesan / Catatan</label>
                <textarea name="pesan" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;resize:vertical;" rows="3"></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Tanggal Jemput</label>
                    <input type="date" name="tanggal" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                </div>
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Jam Jemput</label>
                    <input type="time" name="jam" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                </div>
            </div>
            <div style="margin-top:10px;display:flex;gap:10px;">
                <button type="submit" class="btn btn--primary" style="flex:1;padding:12px;">Simpan Transaksi</button>
                <a href="transaksi.php" class="btn btn--outline" style="padding:12px;">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
