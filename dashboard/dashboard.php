<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Dashboard Admin
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';

requireAdmin();

$recentPesan = $conn->query("
    SELECT nama, pesan, tanggal_pengiriman 
    FROM pengiriman 
    ORDER BY tanggal_pengiriman DESC 
    LIMIT 5
");


// Summary counts
$totalTransaksi = $conn->query("SELECT COUNT(*) AS c FROM transaksi WHERE status IN ('Baru','Diproses','Dikirim')")->fetch_assoc()['c'];
$totalPesan     = $conn->query("SELECT COUNT(*) AS c FROM pengiriman")->fetch_assoc()['c'];
$totalDiproses  = $conn->query("SELECT COUNT(*) AS c FROM transaksi WHERE status = 'Diproses'")->fetch_assoc()['c'];
$totalSelesai   = $conn->query("SELECT COUNT(*) AS c FROM transaksi WHERE status = 'Selesai'")->fetch_assoc()['c'];

// Recent Transactions (5 data - active only)
$recentTransaksi = $conn->query("SELECT * FROM transaksi WHERE status IN ('Baru','Diproses','Dikirim') ORDER BY id_laundry DESC LIMIT 5");

$userNama = e($_SESSION['nama'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Rumah Laundry Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
      #logout-overlay {
        position: fixed;
        inset: 0;
        background: rgba(45, 26, 36, 0.45);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
        animation: fadeIn 0.25s ease;
      }
      @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
      }
      #logout-overlay .card {
        width: 100%;
        max-width: 480px;
        background: #ffffff;
        border-radius: 20px;
        padding: 36px 40px 36px;
        box-shadow: 0 4px 40px rgba(180, 140, 80, 0.1);
        position: relative;
        animation: rise 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
      }
      @keyframes rise {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
      }
      #logout-overlay .card::before {
        content: '';
        position: absolute;
        top: 0; left: 40px; right: 40px;
        height: 1.5px;
        border-radius: 0 0 2px 2px;
        background: linear-gradient(90deg, transparent, #ddb97a, transparent);
      }
      #logout-overlay .illus-area {
        width: 100%; height: 140px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 28px; background: #fdf8f2;
        position: relative; overflow: hidden;
      }
      #logout-overlay .illus-area::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(circle, #e0c49a 1px, transparent 1px);
        background-size: 18px 18px; opacity: 0.35;
      }
      #logout-overlay .illus-svg { position: relative; z-index: 1; }
      #logout-overlay .eyebrow {
        font-family: 'Jost', sans-serif;
        font-size: 10px; letter-spacing: 2.5px; text-transform: uppercase;
        color: #c49a6c; font-weight: 500; margin-bottom: 8px;
      }
      #logout-overlay .title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 25px; font-weight: 300; color: #2d1a24;
        line-height: 1.3; margin-bottom: 10px;
      }
      #logout-overlay .desc {
        font-family: 'Jost', sans-serif;
        font-size: 13px; color: #a08060; line-height: 1.7;
        font-weight: 300; margin-bottom: 26px;
      }
      #logout-overlay .session {
        font-family: 'Jost', sans-serif;
        display: flex; align-items: center; gap: 10px;
        padding: 11px 14px; background: #fdf9f5;
        border-radius: 10px; margin-bottom: 24px;
        border: 1px solid #ede0d0;
      }
      #logout-overlay .session-avatar {
        width: 30px; height: 30px; border-radius: 50%;
        background: linear-gradient(135deg, #e8c49a, #c49a6c);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; color: #fff; font-weight: 500; flex-shrink: 0;
      }
      #logout-overlay .session-name { font-size: 13px; color: #5c3a48; font-weight: 400; }
      #logout-overlay .session-role { font-size: 11px; color: #c49a6c; margin-top: 1px; }
      #logout-overlay .divider { height: 1px; background: #f0e8d8; margin-bottom: 24px; }
      #logout-overlay .btn-row { display: flex; gap: 10px; }
      #logout-overlay .btn-modal {
        flex: 1; height: 44px; border-radius: 10px;
        font-family: 'Jost', sans-serif; font-size: 13px; font-weight: 400;
        letter-spacing: 0.5px; cursor: pointer; border: none;
        transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;
      }
      #logout-overlay .btn-ghost {
        background: transparent; color: #b09070; border: 1px solid #e5d5c0;
      }
      #logout-overlay .btn-ghost:hover { background: #fdf5ec; border-color: #c4a070; color: #806040; }
      #logout-overlay .btn-confirm { background: #c49a6c; color: #fff; letter-spacing: 0.8px; }
      #logout-overlay .btn-confirm:hover { background: #b08a5c; }
      #logout-overlay .close {
        position: absolute; top: 18px; right: 20px;
        width: 28px; height: 28px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: #d0b898; font-size: 18px;
        border-radius: 50%; transition: all 0.2s;
        border: none; background: none;
      }
      #logout-overlay .close:hover { color: #806040; background: #fdf5ec; }

      @keyframes shimmer {
        0%, 100% { opacity: 0.6; }
        50%       { opacity: 1; }
      }
      #logout-overlay .shimmer { animation: shimmer 2.5s ease-in-out infinite; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar__header">
            <div class="sidebar__logo">
                <div class="logo-icon"><img src="../assets/img/RL.png" alt="Logo" style="width:40px;height:40px;background-color:#1F2937;padding:4px;border-radius:8px;"></div>
                <div class="sidebar__logo-text"><span>Rumah Laundry</span><small>Admin Panel</small></div>
            </div>
            <button class="sidebar__close" id="sidebarClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="sidebar__nav">
            <span class="sidebar__label">MENU UTAMA</span>
            <ul class="sidebar__menu">
                <li><a href="dashboard.php" class="sidebar__link active"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
                <li><a href="table-op.php" class="sidebar__link"><i class="fas fa-receipt"></i><span>Transaksi</span></a></li>
                <!-- <li><a href="riwayat_admin.php" class="sidebar__link"><i class="fas fa-clock-rotate-left"></i><span>Riwayat Transaksi</span></a></li>
                <li><a href="pesan.php" class="sidebar__link"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li> -->
            </ul>
            <!-- <span class="sidebar__label">LAINNYA</span> -->
            <ul class="sidebar__menu">
                <!-- <li><a href="../index.php" class="sidebar__link"><i class="fas fa-globe"></i><span>Lihat Website</span></a></li>
                <li><a href="logout.php" class="sidebar__link sidebar__link--logout logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li> -->
            </ul>
        </nav>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="main-wrapper" id="mainWrapper">
        <header class="topbar">
            <div class="topbar__left">
                <button class="topbar__toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar__title">
                    <h1>Dashboard</h1>
                    <p>Selamat datang kembali, <?= $userNama ?></p>
                </div>
            </div>
            <div class="topbar__right">
                <div class="topbar__profile" id="profileToggle">
                    <div class="topbar__avatar"><?= strtoupper(substr($userNama, 0, 1)) ?></div>
                    <span class="topbar__name"><?= $userNama ?></span>
                    <i class="fas fa-chevron-down"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="topbar__dropdown">
                        <a href="../index.php"><i class="fas fa-globe"></i> Lihat Website</a>
                        <div class="topbar__dropdown-divider"></div>
                        <a href="logout.php" class="logout-btn topbar__dropdown--danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <!-- Summary Cards -->
            <div class="cards-grid">
                <div class="summary-card" data-animate="fade-up">
                    <div class="summary-card__icon summary-card__icon--total"><i class="fas fa-layer-group"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Total Transaksi</span>
                        <h2 class="summary-card__value"><?= $totalTransaksi ?></h2>
                    </div>
                </div>
                <div class="summary-card" data-animate="fade-up" data-delay="100">
                    <div class="summary-card__icon summary-card__icon--process"><i class="fas fa-envelope-open-text"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Pesan Masuk</span>
                        <h2 class="summary-card__value"><?= $totalPesan ?></h2>
                    </div>
                </div>
                <div class="summary-card" data-animate="fade-up" data-delay="300">
                    <div class="summary-card__icon summary-card__icon--wait"><i class="fas fa-clock"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Status Di Proses</span>
                        <h2 class="summary-card__value"><?= $totalDiproses ?></h2>
                    </div>
                </div>

                <div class="summary-card" data-animate="fade-up" data-delay="400">
                    <div class="summary-card__icon summary-card__icon--done"><i class="fas fa-check-circle"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Status Selesai</span>
                        <h2 class="summary-card__value"><?= $totalSelesai ?></h2>
                    </div>
                </div>
            </div>

            

            <!-- Recent Transactions -->
            <div class="table-section" style="margin-top: 24px;" data-animate="fade-up">
                <div class="table-section__header">
                    <div>
                        <h2 class="table-section__title">Transaksi Terbaru</h2>
                        <p class="table-section__subtitle">5 data terakhir</p>
                    </div>
                    <a href="table-op.php" class="btn btn--outline">Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Pesan</th>
                                <th>Cuci</th>
                                <th>Layanan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentTransaksi->num_rows > 0): ?>
                                <?php while($row = $recentTransaksi->fetch_assoc()): ?>
                                    <tr>
                                        <td><code><?= e($row['kode_order']) ?></code></td>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($row['pesan']) ?></td>
                                        <td><?= e($row['jenis_pencucian']) ?></td>
                                        <td><?= e($row['jenis_layanan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_penjemputan'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center;padding:40px;">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Messages -->
            <!-- <div class="table-section" style="margin-top: 24px;" data-animate="fade-up">
                <div class="table-section__header">
                    <div>
                        <h2 class="table-section__title">Pesan Pelanggan</h2>
                        <p class="table-section__subtitle">Pesan terbaru dari website</p>
                    </div>
                    <a href="pesan.php" class="btn btn--outline">Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Pesan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentPesan->num_rows > 0): ?>
                                <?php while($row = $recentPesan->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($row['pesan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_pengiriman'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align:center;padding:40px;">Belum ada pesan</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div> -->

        </main>
    </div>

    <script src="dashboard.js"></script>

    <!-- LOGOUT OVERLAY -->
    <div id="logout-overlay">
        <div class="card">
            <button class="close">×</button>

            <div class="illus-area">
                <svg class="illus-svg" width="220" height="110" viewBox="0 0 220 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line x1="30" y1="90" x2="190" y2="90" stroke="#e8d0b0" stroke-width="1" stroke-linecap="round"/>
                    <rect x="72" y="28" width="10" height="62" rx="3" fill="#f0e0c8" stroke="#d4b890" stroke-width="1.2"/>
                    <rect x="138" y="28" width="10" height="62" rx="3" fill="#f0e0c8" stroke="#d4b890" stroke-width="1.2"/>
                    <path d="M72 28 Q110 8 148 28" fill="none" stroke="#d4b890" stroke-width="1.5"/>
                    <line x1="90"  y1="28" x2="90"  y2="90" stroke="#d4b890" stroke-width="1.2"/>
                    <line x1="103" y1="24" x2="103" y2="90" stroke="#d4b890" stroke-width="1.2"/>
                    <line x1="117" y1="24" x2="117" y2="90" stroke="#d4b890" stroke-width="1.2"/>
                    <line x1="130" y1="28" x2="130" y2="90" stroke="#d4b890" stroke-width="1.2"/>
                    <line x1="72" y1="52" x2="148" y2="52" stroke="#d4b890" stroke-width="1"/>
                    <line x1="72" y1="70" x2="148" y2="70" stroke="#d4b890" stroke-width="1"/>
                    <polygon points="90,22 93,28 87,28" fill="#c4a870"/>
                    <polygon points="103,18 106,24 100,24" fill="#c4a870"/>
                    <polygon points="117,18 120,24 114,24" fill="#c4a870"/>
                    <polygon points="130,22 133,28 127,28" fill="#c4a870"/>
                    <g transform="translate(95, 2)">
                        <rect x="0" y="14" width="30" height="8" rx="2" fill="#d4a870"/>
                        <polygon points="0,14 5,4 10,14" fill="#d4a870"/>
                        <polygon points="10,14 15,0 20,14" fill="#c49060"/>
                        <polygon points="20,14 25,4 30,14" fill="#d4a870"/>
                        <circle cx="5"  cy="10" r="2" fill="#e8a0b8"/>
                        <circle cx="15" cy="6"  r="2.5" fill="#c8485c"/>
                        <circle cx="25" cy="10" r="2" fill="#e8a0b8"/>
                        <circle cx="8"  cy="17" r="1.2" fill="#f0e0c0"/>
                        <circle cx="15" cy="17" r="1.2" fill="#f0e0c0"/>
                        <circle cx="22" cy="17" r="1.2" fill="#f0e0c0"/>
                    </g>
                    <rect x="103" y="56" width="14" height="11" rx="2.5" fill="#c49a6c" stroke="#b08850" stroke-width="1"/>
                    <path d="M106 56 Q106 51 110 51 Q114 51 114 56" stroke="#b08850" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    <circle cx="110" cy="61" r="2" fill="#8b6030"/>
                    <g transform="translate(150, 48)">
                        <line x1="0" y1="0" x2="18" y2="18" stroke="#d4a060" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="0" cy="0" r="7" fill="none" stroke="#d4a060" stroke-width="2"/>
                        <circle cx="0" cy="0" r="3" fill="none" stroke="#d4a060" stroke-width="1.5"/>
                        <line x1="12" y1="12" x2="15" y2="9"  stroke="#d4a060" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="15" y1="15" x2="18" y2="12" stroke="#d4a060" stroke-width="1.5" stroke-linecap="round"/>
                    </g>
                    <circle cx="50" cy="72" r="7" fill="#e8b890" opacity="0.55"/>
                    <circle cx="48" cy="70" r="4.5" fill="#d4985c" opacity="0.7"/>
                    <path d="M50 79 L50 90" stroke="#90b870" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M50 85 L46 82" stroke="#90b870" stroke-width="1.2" stroke-linecap="round"/>
                    <path d="M50 83 L54 80" stroke="#90b870" stroke-width="1.2" stroke-linecap="round"/>
                    <circle cx="60" cy="80" r="5" fill="#f0c8a0" opacity="0.5"/>
                    <circle cx="58" cy="78" r="3" fill="#d4a070" opacity="0.65"/>
                    <path d="M60 85 L60 90" stroke="#90b870" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="170" cy="75" r="6" fill="#e8b890" opacity="0.5"/>
                    <circle cx="168" cy="73" r="4" fill="#d4985c" opacity="0.65"/>
                    <path d="M170 81 L170 90" stroke="#90b870" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="161" cy="80" r="5" fill="#f0c8a0" opacity="0.45"/>
                    <circle cx="159" cy="78" r="3" fill="#d4a070" opacity="0.6"/>
                    <path d="M161 85 L161 90" stroke="#90b870" stroke-width="1.5" stroke-linecap="round"/>
                    <g class="shimmer">
                        <path d="M42 52 L43 55 L46 56 L43 57 L42 60 L41 57 L38 56 L41 55 Z" fill="#d4a060" opacity="0.55" transform="scale(0.65) translate(20, 18)"/>
                    </g>
                    <g class="shimmer" style="animation-delay:0.8s">
                        <path d="M178 38 L179 41 L182 42 L179 43 L178 46 L177 43 L174 42 L177 41 Z" fill="#c49a6c" opacity="0.5" transform="scale(0.6)"/>
                    </g>
                    <g class="shimmer" style="animation-delay:1.6s">
                        <path d="M60 35 L61 37 L63 38 L61 39 L60 41 L59 39 L57 38 L59 37 Z" fill="#e8c490" opacity="0.5" transform="scale(0.55)"/>
                    </g>
                </svg>
            </div>

            <p class="eyebrow">Keluar Panel Admin</p>
            <h2 class="title">Akhiri sesi<br><em>Administrator?</em></h2>
            <p class="desc">Pastikan seluruh perubahan telah disimpan sebelum keluar. Sesi administrator akan berakhir sepenuhnya.</p>

            <div class="session">
                <div class="session-avatar"><?= strtoupper(substr($userNama, 0, 1)) ?></div>
                <div>
                    <div class="session-name"><?= $userNama ?></div>
                    <div class="session-role">Administrator</div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="btn-row">
                <button class="btn-modal btn-ghost">Batalkan</button>
                <button class="btn-modal btn-confirm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <script>
        // Buka popup ketika tombol logout diklik
        document.querySelectorAll('.logout-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('logout-overlay').style.display = 'flex';
            });
        });

        // Tutup popup: tombol batal & tombol ×
        document.querySelectorAll('#logout-overlay .btn-ghost, #logout-overlay .close').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('logout-overlay').style.display = 'none';
            });
        });

        // Tutup popup: klik di luar card
        document.getElementById('logout-overlay').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });

        // Konfirmasi logout: tombol Ya, Keluar
        document.querySelector('#logout-overlay .btn-confirm').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });
    </script>
</body>
</html>
