<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Riwayat Transaksi (Admin)
 *  Menampilkan pesanan Selesai dan Dibatalkan
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';
requireAdmin();

$flash = getFlash();
$adminNama = e($_SESSION['nama'] ?? 'Admin');

// Filter: default semua riwayat (Selesai + Dibatalkan)
$filterStatus = $_GET['filter_status'] ?? '';
if ($filterStatus === 'Selesai') {
    $data = $conn->query("SELECT * FROM transaksi WHERE status = 'Selesai' ORDER BY id_laundry DESC");
} elseif ($filterStatus === 'Dibatalkan') {
    $data = $conn->query("SELECT * FROM transaksi WHERE status = 'Dibatalkan' ORDER BY id_laundry DESC");
} else {
    $data = $conn->query("SELECT * FROM transaksi WHERE status IN ('Selesai','Dibatalkan') ORDER BY id_laundry DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi — Rumah Laundry Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <!-- ═══════════ SIDEBAR ═══════════ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar__header">
            <div class="sidebar__logo">
                <div class="sidebar__logo-icon"><img src="../assets/img/RL.png" alt="Logo" style="width:40px;height:40px;background-color:#1F2937;padding:4px;border-radius:8px;"></div>
                <div class="sidebar__logo-text"><span>Rumah Laundry</span><small>Admin Panel</small></div>
            </div>
            <button class="sidebar__close" id="sidebarClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="sidebar__nav">
            <span class="sidebar__label">MENU UTAMA</span>
            <ul class="sidebar__menu">
                <li><a href="dashboard.php" class="sidebar__link"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
                <li><a href="table-op.php" class="sidebar__link"><i class="fas fa-receipt"></i><span>Transaksi</span></a></li>
                <li><a href="riwayat_admin.php" class="sidebar__link active"><i class="fas fa-clock-rotate-left"></i><span>Riwayat Transaksi</span></a></li>
                <li><a href="pesan.php" class="sidebar__link"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li>
            </ul>
            <span class="sidebar__label">LAINNYA</span>
            <ul class="sidebar__menu">
                <li><a href="../index.php" class="sidebar__link"><i class="fas fa-globe"></i><span>Lihat Website</span></a></li>
                <li><a href="logout.php" class="sidebar__link sidebar__link--logout" onclick="return confirm('Anda yakin ingin keluar?')"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="main-wrapper" id="mainWrapper">
        <header class="topbar" id="topbar">
            <div class="topbar__left">
                <button class="topbar__toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar__title">
                    <h1>Riwayat Transaksi</h1>
                    <p>Pesanan selesai dan dibatalkan</p>
                </div>
            </div>
            <div class="topbar__right">
                <div class="topbar__profile" id="profileToggle">
                    <div class="topbar__avatar"><?= strtoupper(substr($adminNama, 0, 1)) ?></div>
                    <span class="topbar__name"><?= $adminNama ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </header>

        <main class="content">

            <?php if ($flash): ?>
            <div style="padding:14px 18px;border-radius:10px;margin-bottom:20px;background:<?= $flash['type']=='success'?'#d1fae5':'#fee2e2' ?>;color:<?= $flash['type']=='success'?'#065f46':'#991b1b' ?>;font-weight:500;">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <div class="table-section" data-animate="fade-up">
                <div class="table-section__header">
                    <div>
                        <h2 class="table-section__title">Riwayat Pesanan</h2>
                        <p class="table-section__subtitle">Total <?= $data->num_rows ?> data</p>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                        <form method="GET" action="riwayat_admin.php" style="display:flex;gap:8px;align-items:center;">
                            <select name="filter_status" onchange="this.form.submit()"
                                style="padding:8px 14px;border:2px solid #f0e4e7;border-radius:8px;font-family:'Inter',sans-serif;font-size:.85rem;outline:none;background:#fff;cursor:pointer;">
                                <option value="">Semua Riwayat</option>
                                <option value="Selesai" <?= $filterStatus==='Selesai'?'selected':'' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= $filterStatus==='Dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                            </select>
                            <?php if (!empty($filterStatus)): ?>
                                <a href="riwayat_admin.php" style="font-size:.8rem;color:var(--text-muted);">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Order</th>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Jenis Pencucian</th>
                                <th>Jenis Layanan</th>
                                <th>Tgl Jemput</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($data->num_rows > 0): ?>
                                <?php $no = 1; while($row = $data->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <code style="background:var(--bg);padding:3px 8px;border-radius:4px;font-size:.8rem;font-weight:600;">
                                                <?= e($row['kode_order']) ?>
                                            </code>
                                        </td>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td><?= e($row['no_hp']) ?></td>
                                        <td><?= e($row['jenis_pencucian']) ?></td>
                                        <td><?= e($row['jenis_layanan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_penjemputan'])) ?></td>
                                        <td>
                                            <?php
                                                $s = $row['status'] ?? 'Selesai';
                                                $cls = 'badge--done';
                                                if ($s == 'Dibatalkan') $cls = 'badge--cancelled';
                                            ?>
                                            <span class="badge <?= $cls ?>"><span class="badge__dot"></span><?= $s ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                                        <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                                        Belum ada riwayat transaksi
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="dashboard.js"></script>

    <style>
    /* Badge styles for riwayat */
    .badge { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:100px;font-size:.75rem;font-weight:600;white-space:nowrap; }
    .badge__dot { width:6px;height:6px;border-radius:50%;display:inline-block; }
    .badge--done { background:#d1fae5;color:#059669; }
    .badge--done .badge__dot { background:#059669; }
    .badge--cancelled { background:#fee2e2;color:#dc2626; }
    .badge--cancelled .badge__dot { background:#dc2626; }
    </style>
</body>
</html>
