<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Transaksi (List Data)
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';
requireAdmin();

$flash = getFlash();
$adminNama = e($_SESSION['nama'] ?? 'Admin');

// Search
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM transaksi WHERE nama LIKE ? OR kode_order LIKE ? OR no_hp LIKE ? OR pesan LIKE ? ORDER BY id_laundry DESC");
    $lk = "%$search%";
    $stmt->bind_param('ssss', $lk, $lk, $lk, $lk);
    $stmt->execute();
    $data = $stmt->get_result();
} else {
    $data = $conn->query("SELECT * FROM transaksi ORDER BY id_laundry DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi — Rumah Laundry Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar__header">
            <div class="sidebar__logo">
                <div class="sidebar__logo-icon"><img src="../assets/img/RL.png" alt="Logo" style="width:40px;height:40px;object-fit:contain;border-radius:8px;"></div>
                <div class="sidebar__logo-text"><span>Rumah Laundry</span><small>Admin Panel</small></div>
            </div>
            <button class="sidebar__close" id="sidebarClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="sidebar__nav">
            <span class="sidebar__label">MENU UTAMA</span>
            <ul class="sidebar__menu">
                <li><a href="dashboard.php" class="sidebar__link"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
                <li><a href="transaksi.php" class="sidebar__link active"><i class="fas fa-receipt"></i><span>Transaksi</span></a></li>
                <li><a href="pesan.php" class="sidebar__link"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li>
            </ul>
        </nav>
        <div class="sidebar__footer">
            <div class="sidebar__user">
                <div class="sidebar__user-avatar"><?= strtoupper(substr($adminNama, 0, 1)) ?></div>
                <div class="sidebar__user-info"><strong><?= $adminNama ?></strong><span>Super Admin</span></div>
            </div>
        </div>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar__left">
                <button class="topbar__toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar__title"><h1>Data Transaksi</h1><p>Riwayat operasional laundry</p></div>
            </div>
        </header>

        <main class="content">
            <?php if ($flash): ?>
                <div style="padding:15px; border-radius:12px; margin-bottom:20px; background:<?= $flash['type']=='success'?'#d1fae5':'#fee2e2' ?>; color:<?= $flash['type']=='success'?'#059669':'#dc2626' ?>;">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php
            // ── WhatsApp Notifikasi Banner ──
            $waNotify = $_SESSION['wa_notify'] ?? null;
            unset($_SESSION['wa_notify']);
            if ($waNotify):
            ?>
            <div id="waBanner" style="
                background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
                border-radius: 16px;
                padding: 20px 24px;
                margin-bottom: 22px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                box-shadow: 0 4px 20px rgba(37,211,102,0.25);
                flex-wrap: wrap;
            ">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="font-size:2.2rem;">📱</div>
                    <div>
                        <div style="color:#fff; font-weight:700; font-size:1rem;">
                            Pesanan <strong><?= e($waNotify['kode']) ?></strong> sudah Selesai!
                        </div>
                        <div style="color:rgba(255,255,255,0.85); font-size:0.85rem; margin-top:3px;">
                            Klik tombol berikut untuk notifikasi WhatsApp ke <strong><?= e($waNotify['nama']) ?></strong>
                        </div>
                    </div>
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <a id="waBtn" href="<?= htmlspecialchars($waNotify['url'], ENT_QUOTES) ?>" target="_blank"
                       style="
                           background:#fff;
                           color:#128C7E;
                           font-weight:700;
                           padding:12px 22px;
                           border-radius:10px;
                           text-decoration:none;
                           font-size:0.9rem;
                           display:inline-flex;
                           align-items:center;
                           gap:8px;
                           white-space:nowrap;
                           box-shadow:0 2px 8px rgba(0,0,0,0.12);
                        ">
                        <i class="fab fa-whatsapp" style="font-size:1.1rem;"></i>
                        Kirim WhatsApp ke Pelanggan
                    </a>
                    <button onclick="document.getElementById('waBanner').remove()" style="
                        background:rgba(255,255,255,0.2);
                        border:none;
                        color:#fff;
                        border-radius:8px;
                        padding:10px 14px;
                        cursor:pointer;
                        font-size:0.85rem;
                    ">&#x2715; Tutup</button>
                </div>
            </div>
            <script>
                // Auto-buka tab WhatsApp saat halaman load
                (function(){
                    var btn = document.getElementById('waBtn');
                    if(btn){ window.open(btn.href, '_blank'); }
                })();
            </script>
            <?php endif; ?>


            <div class="table-section">
                <div class="table-section__header">
                    <div><h2 class="table-section__title">Daftar Transaksi</h2><p>Total <?= $data->num_rows ?> data</p></div>
                    <div style="display:flex;gap:10px;">
                        <form method="GET" style="position:relative;">
                            <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                            <input type="text" name="q" placeholder="Cari..." value="<?= e($search) ?>" style="padding:10px 10px 10px 35px; border:1px solid #ddd; border-radius:8px; outline:none;">
                        </form>
                        <a href="tambah_transaksi.php" class="btn btn--primary"><i class="fas fa-plus"></i> Tambah</a>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>HP</th>
                                <th>Alamat</th>
                                <th>Pesan</th>
                                <th>Cuci</th>
                                <th>Layanan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($data->num_rows > 0): ?>
                                <?php $n=1; while($row = $data->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $n++ ?></td>
                                        <td><code><?= e($row['kode_order']) ?></code></td>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td><?= e($row['no_hp']) ?></td>
                                        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($row['alamat']) ?>"><?= e($row['alamat']) ?></td>
                                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($row['pesan']) ?>"><?= e($row['pesan']) ?></td>
                                        <td><?= e($row['jenis_pencucian']) ?></td>
                                        <td><?= e($row['jenis_layanan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_penjemputan'])) ?></td>
                                        <td>
                                            <?php 
                                                $s = $row['status'] ?? 'Baru';
                                                $bg = '#E5E7EB'; $cl = '#4B5563';
                                                if($s == 'Baru') { $bg = '#DBEAFE'; $cl = '#2563EB'; }
                                                if($s == 'Dicuci' || $s == 'Diproses') { $bg = '#FEF3C7'; $cl = '#D97706'; }
                                                if($s == 'Dijemput' || $s == 'Dikirim') { $bg = '#E0E7FF'; $cl = '#4338CA'; }
                                                if($s == 'Selesai') { $bg = '#D1FAE5'; $cl = '#059669'; }
                                                if($s == 'Dibatalkan') { $bg = '#FEE2E2'; $cl = '#DC2626'; }
                                            ?>
                                            <span style="padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:600; background:<?= $bg ?>; color:<?= $cl ?>;">
                                                <?= $s ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:5px;">
                                                <a href="edit_transaksi.php?id=<?= $row['id_laundry'] ?>" class="btn btn--sm" style="background:#fef3c7;color:#d97706;"><i class="fas fa-edit"></i></a>
                                                <a href="hapus_transaksi.php?id=<?= $row['id_laundry'] ?>" class="btn btn--sm" style="background:#fee2e2;color:#dc2626;" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" style="text-align:center;padding:40px;">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="dashboard.js"></script>
</body>
</html>
