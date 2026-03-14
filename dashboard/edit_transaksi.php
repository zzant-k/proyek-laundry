<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Edit Transaksi
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: transaksi.php'); exit; }

// Fetch data
$stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_laundry = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) { header('Location: transaksi.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama']);
    $hp      = substr(trim($_POST['no_hp'] ?? ''), 0, 50);
    $pesan   = trim($_POST['pesan']);
    $cuci    = $_POST['jenis_pencucian'];
    $layanan = $_POST['jenis_layanan'];
    $tgl     = $_POST['tanggal'];
    $jam     = $_POST['jam'];
    $status  = $_POST['status'];

    // Cek status lama & whatsapp_sent sebelum update
    $stOld = $conn->prepare("SELECT status, whatsapp_sent, kode_order, alamat FROM transaksi WHERE id_laundry = ? LIMIT 1");
    $stOld->bind_param('i', $id);
    $stOld->execute();
    $oldRow = $stOld->get_result()->fetch_assoc();
    $stOld->close();

    $upd = $conn->prepare("UPDATE transaksi SET nama=?, no_hp=?, pesan=?, jenis_pencucian=?, jenis_layanan=?, tanggal_penjemputan=?, jam_penjemputan=?, status=? WHERE id_laundry=?");
    $upd->bind_param('ssssssssi', $nama, $hp, $pesan, $cuci, $layanan, $tgl, $jam, $status, $id);
    
    if ($upd->execute()) {
        // ── Notifikasi WhatsApp otomatis saat status berubah jadi Selesai ──
        if ($status === 'Selesai' && ($oldRow['whatsapp_sent'] ?? 0) == 0) {
            // Format nomor HP ke internasional (62xxx)
            $noHp = preg_replace('/[^0-9]/', '', $hp);
            if (substr($noHp, 0, 1) === '0') {
                $noHp = '62' . substr($noHp, 1);
            }
            $kodeOrder = $oldRow['kode_order'] ?? '';
            $alamat    = $oldRow['alamat'] ?? '-';
            $pesanWA   = "Halo $nama, pesanan laundry Anda dengan kode order $kodeOrder "
                       . "($layanan - $cuci) telah *Selesai*. "
                       . "Silakan datang untuk mengambil pesanan Anda. "
                       . "Alamat: $alamat. "
                       . "Terima kasih telah menggunakan Rumah Laundry! 🧺";
            $waUrl = 'https://wa.me/' . $noHp . '?text=' . rawurlencode($pesanWA);

            // Simpan ke session untuk ditampilkan di transaksi.php
            $_SESSION['wa_notify'] = [
                'nama'      => $nama,
                'url'       => $waUrl,
                'kode'      => $kodeOrder,
            ];

            // Tandai sudah dikirim agar tidak berulang
            $conn->query("UPDATE transaksi SET whatsapp_sent = 1 WHERE id_laundry = $id");
        }

        setFlash('success', 'Transaksi berhasil diperbarui.');
        header('Location: transaksi.php');
        exit;
    }
}

$hasCoords = !empty($row['latitude']) && !empty($row['longitude']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi — Rumah Laundry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <?php if ($hasCoords): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php endif; ?>
</head>
<body style="background:#FFF0F3;">
    <div style="max-width:600px;margin:50px auto;background:#fff;padding:30px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom:20px;color:#FF385C;"><i class="fas fa-edit"></i> Edit Transaksi #<?= e($row['kode_order']) ?></h2>
        
        <form method="POST" style="display:grid;gap:15px;">
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Nama Pelanggan</label>
                <input type="text" name="nama" value="<?= e($row['nama']) ?>" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">No HP</label>
                <input type="text" name="no_hp" value="<?= e($row['no_hp']) ?>" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Jenis Pencucian</label>
                    <select name="jenis_pencucian" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                        <option <?= $row['jenis_pencucian']=='Cuci Kering'?'selected':'' ?>>Cuci Kering</option>
                        <option <?= $row['jenis_pencucian']=='Cuci Setrika'?'selected':'' ?>>Cuci Setrika</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Jenis Layanan</label>
                    <select name="jenis_layanan" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                        <option <?= $row['jenis_layanan']=='Reguler'?'selected':'' ?>>Reguler</option>
                        <option <?= $row['jenis_layanan']=='Express'?'selected':'' ?>>Express</option>
                        <option <?= $row['jenis_layanan']=='Antar Jemput'?'selected':'' ?>>Antar Jemput</option>
                    </select>
                </div>
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Pesan / Catatan</label>
                <textarea name="pesan" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;resize:vertical;" rows="3"><?= e($row['pesan']) ?></textarea>
            </div>

            <!-- ═══ LOKASI PELANGGAN (read-only map) ═══ -->
            <?php if ($hasCoords): ?>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">
                    <i class="fas fa-map-marker-alt" style="color:#C67A89;"></i> Lokasi Penjemputan
                </label>
                <div style="border-radius:12px;overflow:hidden;border:2px solid #f0e4e7;box-shadow:0 2px 12px rgba(198,122,137,.08);">
                    <div id="adminMap" style="width:100%;height:220px;"></div>
                </div>
                <div style="margin-top:8px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#C67A89;color:#fff;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none;transition:all .2s;">
                        <i class="fas fa-external-link-alt"></i> Buka di Google Maps
                    </a>
                    <span style="font-size:.75rem;color:#9ca3af;">
                        <?= number_format($row['latitude'], 6) ?>, <?= number_format($row['longitude'], 6) ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($row['alamat'])): ?>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Alamat</label>
                <p style="padding:10px;background:#faf2f4;border-radius:8px;font-size:.88rem;color:#374151;line-height:1.6;"><?= e($row['alamat']) ?></p>
            </div>
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Tanggal Jemput</label>
                    <input type="date" name="tanggal" value="<?= $row['tanggal_penjemputan'] ?>" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                </div>
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Jam Jemput</label>
                    <input type="time" name="jam" value="<?= $row['jam_penjemputan'] ?>" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                </div>
            </div>
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:5px;">Status Pesanan</label>
                <select name="status" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                    <option value="Baru" <?= $row['status']=='Baru'?'selected':'' ?>>Baru / Diterima</option>
                    <option value="Diproses" <?= $row['status']=='Diproses'?'selected':'' ?>>Diproses</option>
                    <option value="Dikirim" <?= $row['status']=='Dikirim'?'selected':'' ?>>Dikirim / Siap Jemput</option>
                    <option value="Selesai" <?= $row['status']=='Selesai'?'selected':'' ?>>Selesai</option>
                    <option value="Dibatalkan" <?= $row['status']=='Dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                </select>
            </div>
            <div style="margin-top:10px;display:flex;gap:10px;">
                <button type="submit" class="btn btn--primary" style="flex:1;padding:12px;">Simpan Perubahan</button>
                <a href="transaksi.php" class="btn btn--outline" style="padding:12px;">Batal</a>
            </div>
        </form>
    </div>

<?php if ($hasCoords): ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    var lat = <?= $row['latitude'] ?>, lng = <?= $row['longitude'] ?>;
    var map = L.map('adminMap', { scrollWheelZoom: false, dragging: true, zoomControl: true }).setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap', maxZoom: 19
    }).addTo(map);
    L.marker([lat, lng]).addTo(map)
        .bindPopup('<b><?= e($row['nama']) ?></b><br><?= e($row['alamat'] ?? '') ?>').openPopup();
    setTimeout(function(){ map.invalidateSize(); }, 200);
})();
</script>
<?php endif; ?>
</body>
</html>

