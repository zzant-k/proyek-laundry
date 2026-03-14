<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Edit Transaksi
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';
requireAdmin();

// Validasi ID
if (!isset($_GET['id'])) {
    setFlash('danger', 'ID transaksi tidak valid');
    header('Location: table-op.php');
    exit;
}

$id = (int)$_GET['id'];

// Ambil data transaksi berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_laundry = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlash('danger', 'Data tidak ditemukan');
    header('Location: table-op.php');
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

$userNama = e($_SESSION['nama'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi — Rumah Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body style="background:#FAF2F4;">
    <div style="max-width:600px;margin:50px auto;background:#fff;padding:36px;border-radius:20px;box-shadow:0 10px 40px rgba(198,122,137,.12);border:1px solid #f0e4e7;">
        <h2 style="margin-bottom:24px;color:#C67A89;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-edit"></i> Edit Transaksi #<?= e($data['kode_laundry']) ?>
        </h2>

        <form action="proses_edit.php" method="POST" style="display:grid;gap:16px;">
            <input type="hidden" name="id_laundry" value="<?= $data['id_laundry'] ?>">

            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">Nama Pelanggan</label>
                <input type="text" name="nama" value="<?= e($data['nama']) ?>" required
                    style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;transition:0.3s;"
                    onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">No HP</label>
                <input type="text" name="no_hp" value="<?= e($data['no_hp']) ?>" required
                    style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;transition:0.3s;"
                    onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">Jenis Pencucian</label>
                    <select name="jenis_pencucian" required
                        style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;background:#fff;">
                        <option value="Cuci Kering" <?= $data['jenis_pencucian']=='Cuci Kering'?'selected':'' ?>>Cuci Kering</option>
                        <option value="Cuci Setrika" <?= $data['jenis_pencucian']=='Cuci Setrika'?'selected':'' ?>>Cuci Setrika</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">Jenis Layanan</label>
                    <select name="jenis_layanan" required
                        style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;background:#fff;">
                        <option value="Reguler" <?= $data['jenis_layanan']=='Reguler'?'selected':'' ?>>Reguler</option>
                        <option value="Express" <?= $data['jenis_layanan']=='Express'?'selected':'' ?>>Express</option>
                        <option value="Antar Jemput" <?= $data['jenis_layanan']=='Antar Jemput'?'selected':'' ?>>Antar Jemput</option>
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">Tanggal Jemput</label>
                    <input type="date" name="tanggal" value="<?= $data['tanggal_penjemputan'] ?>" required
                        style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;">
                </div>
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">Jam Jemput</label>
                    <input type="time" name="jam" value="<?= $data['jam_penjemputan'] ?>" required
                        style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;">
                </div>
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">Pesan (Opsional)</label>
                <textarea name="pesan" rows="3"
                    style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;transition:0.3s;resize:vertical;"
                    onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'"><?= e($data['pesan']) ?></textarea>
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:#1F2937;">Status Pesanan</label>
                <select name="status" required
                    style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:0.95rem;outline:none;background:#fff;">
                    <option value="Baru" <?= $data['status']=='Baru'?'selected':'' ?>>Baru / Diterima</option>
                    <option value="Dicuci" <?= $data['status']=='Dicuci'?'selected':'' ?>>Sedang Dicuci</option>
                    <option value="Dijemput" <?= $data['status']=='Dijemput'?'selected':'' ?>>Siap Diantar/Jemput</option>
                    <option value="Selesai" <?= $data['status']=='Selesai'?'selected':'' ?>>Selesai</option>
                </select>
            </div>
            <div style="margin-top:8px;display:flex;gap:12px;">
                <button type="submit" class="btn btn--primary" style="flex:1;padding:14px;justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="table-op.php" class="btn btn--outline" style="padding:14px 24px;">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
