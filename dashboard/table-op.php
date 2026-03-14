<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Table Operasional (CRUD via PHP)
 *  Semua CRUD ditangani oleh config.php lewat POST/GET
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';

// ── READ: Ambil data pesanan AKTIF saja (dengan filter status opsional) ──
$filterStatus = $_GET['filter_status'] ?? '';
if ($filterStatus !== '') {
    $stFilter = $conn->prepare("SELECT * FROM transaksi WHERE status = ? ORDER BY id_laundry DESC");
    $stFilter->bind_param('s', $filterStatus);
    $stFilter->execute();
    $result = $stFilter->get_result();
} else {
    // Default: hanya tampilkan pesanan aktif (exclude Selesai & Dibatalkan)
    $result = $conn->query("SELECT * FROM transaksi WHERE status IN ('Baru','Diproses','Dikirim') ORDER BY id_laundry DESC");
}
$userNama = e($_SESSION['nama'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Operasional — Rumah Laundry</title>
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
                <li><a href="table-op.php" class="sidebar__link active"><i class="fas fa-receipt"></i><span>Transaksi</span></a></li>
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

    <!-- ═══════════ MAIN ═══════════ -->
    <div class="main-wrapper" id="mainWrapper">
        <header class="topbar" id="topbar">
            <div class="topbar__left">
                <button class="topbar__toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar__title">
                    <h1>Data Operasional</h1>
                    <p>Kelola semua data cucian pelanggan</p>
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

            <?php
            // ── Flash Message ──
            $flash = getFlash();
            if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] == 'success' ? 'success' : 'danger' ?>"
                 style="padding:14px 18px;border-radius:10px;margin-bottom:20px;background:<?= $flash['type']=='success'?'#d1fae5':'#fee2e2' ?>;color:<?= $flash['type']=='success'?'#065f46':'#991b1b' ?>;font-weight:500;">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <?php
            // ── WhatsApp Notifikasi Banner ──
            $waNotify = $_SESSION['wa_notify'] ?? null;
            unset($_SESSION['wa_notify']);
            if ($waNotify): ?>
            <style>
                @keyframes waBannerSlideIn {
                    from { opacity:0; transform:translateY(-16px) scale(.98); }
                    to   { opacity:1; transform:translateY(0)     scale(1);   }
                }
                @keyframes waPulse {
                    0%,100% { box-shadow:0 0 0 0 rgba(255,255,255,.4); }
                    50%     { box-shadow:0 0 0 8px rgba(255,255,255,0); }
                }
                @keyframes waShimmer {
                    0%   { background-position:-200% center; }
                    100% { background-position: 200% center; }
                }
                #waBanner {
                    animation: waBannerSlideIn .4s cubic-bezier(.22,.68,0,1.2) both;
                    position:relative;overflow:hidden;
                    background: linear-gradient(135deg,#1ebe5d 0%,#0fa451 40%,#0d8f47 100%);
                    border-radius:20px;padding:22px 28px;margin-bottom:24px;
                    display:flex;align-items:center;justify-content:space-between;
                    gap:18px;flex-wrap:wrap;
                    box-shadow:0 8px 32px rgba(14,150,70,.3), 0 2px 8px rgba(0,0,0,.08);
                }
                #waBanner::before {
                    content:'';position:absolute;inset:0;
                    background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.08) 50%,transparent 60%);
                    background-size:200% 100%;
                    animation:waShimmer 3.5s linear infinite;
                }
                #waBanner::after {
                    content:'';position:absolute;right:-60px;top:-60px;
                    width:200px;height:200px;border-radius:50%;
                    background:rgba(255,255,255,.06);pointer-events:none;
                }
                .wa-send-btn {
                    display:inline-flex;align-items:center;gap:10px;
                    background:#fff;color:#0a8a3e;font-weight:700;
                    padding:13px 24px;border-radius:12px;
                    text-decoration:none;font-size:.9rem;white-space:nowrap;
                    box-shadow:0 4px 16px rgba(0,0,0,.15);
                    transition:transform .2s,box-shadow .2s;
                    position:relative;z-index:1;
                }
                .wa-send-btn:hover {
                    transform:translateY(-2px);
                    box-shadow:0 6px 20px rgba(0,0,0,.2);
                }
                .wa-close-btn {
                    background:rgba(255,255,255,.15);backdrop-filter:blur(4px);
                    border:1.5px solid rgba(255,255,255,.3);color:#fff;
                    border-radius:10px;padding:10px 16px;cursor:pointer;
                    font-size:.82rem;font-weight:600;letter-spacing:.3px;
                    transition:background .2s,transform .15s;
                    display:inline-flex;align-items:center;gap:6px;
                    position:relative;z-index:1;
                }
                .wa-close-btn:hover {
                    background:rgba(255,255,255,.25);transform:scale(.97);
                }
                .wa-pulse-dot {
                    width:10px;height:10px;border-radius:50%;
                    background:#fff;animation:waPulse 1.8s ease-in-out infinite;
                    flex-shrink:0;
                }
            </style>
            <div id="waBanner">
                <!-- Left: icon + text -->
                <div style="display:flex;align-items:center;gap:16px;position:relative;z-index:1;">
                    <!-- WhatsApp SVG Logo in circle -->
                    <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.18);
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="#fff">
                            <path d="M16 2C8.28 2 2 8.28 2 16c0 2.47.67 4.79 1.84 6.78L2 30l7.43-1.81A13.93 13.93 0 0016 30c7.72 0 14-6.28 14-14S23.72 2 16 2zm0 25.5a11.44 11.44 0 01-5.83-1.6l-.42-.25-4.41 1.07 1.1-4.3-.27-.44A11.5 11.5 0 1116 27.5zm6.32-8.6c-.35-.17-2.06-1.01-2.38-1.13-.32-.11-.55-.17-.78.18-.23.34-.9 1.13-1.1 1.36-.2.23-.4.26-.74.09-.35-.18-1.46-.54-2.78-1.72a10.4 10.4 0 01-1.93-2.4c-.2-.34-.02-.53.15-.7.15-.16.35-.4.52-.6.17-.2.23-.34.34-.57.12-.23.06-.43-.02-.6-.09-.17-.78-1.88-1.07-2.57-.28-.68-.57-.58-.78-.59l-.66-.01c-.23 0-.6.09-.91.43-.32.35-1.2 1.17-1.2 2.86s1.23 3.32 1.4 3.54c.17.23 2.41 3.68 5.84 5.16.82.35 1.45.56 1.95.72.82.26 1.57.22 2.16.13.66-.1 2.06-.84 2.35-1.66.29-.81.29-1.51.2-1.66-.08-.16-.3-.25-.65-.43z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <div class="wa-pulse-dot"></div>
                            <span style="color:rgba(255,255,255,.75);font-size:.72rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;">Pesanan Selesai</span>
                        </div>
                        <div style="color:#fff;font-weight:700;font-size:1.05rem;line-height:1.3;">
                            Kode <span style="background:rgba(255,255,255,.2);padding:2px 8px;border-radius:6px;font-size:.95rem;"><?= e($waNotify['kode']) ?></span> siap diambil
                        </div>
                        <div style="color:rgba(255,255,255,.8);font-size:.83rem;margin-top:4px;">
                            Notifikasi ke pelanggan
                            <strong style="color:#fff;"><?= e($waNotify['nama']) ?></strong> belum terkirim
                        </div>
                    </div>
                </div>
                <!-- Right: action buttons -->
                <div style="display:flex;gap:10px;align-items:center;flex-shrink:0;">
                    <a id="waBtn" href="<?= htmlspecialchars($waNotify['url'] ?? '', ENT_QUOTES) ?>" target="_blank" class="wa-send-btn">
                        <!-- WhatsApp checkmark send icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#0a8a3e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                        </svg>
                        Kirim Pesan WhatsApp
                    </a>
                    <button class="wa-close-btn" onclick="
                        var b=document.getElementById('waBanner');
                        b.style.opacity='0';b.style.transform='translateY(-10px)';
                        setTimeout(()=>b.remove(),300);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Tutup
                    </button>
                </div>
            </div>
            <script>
                (function(){ var b=document.getElementById('waBtn'); if(b) window.open(b.href,'_blank'); })();
            </script>
            <?php endif; ?>

            <!-- ══════════ MODAL OVERLAY ══════════ -->
            <!-- MODAL TAMBAH -->
            <div id="modalTambah" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
                <div style="background:#fff;border-radius:20px;padding:32px 36px;width:100%;max-width:640px;box-shadow:0 24px 60px rgba(0,0,0,.18);position:relative;max-height:90vh;overflow-y:auto;animation:modalIn .28s cubic-bezier(.22,.68,0,1.2) both;">
                    <button onclick="closeModal('modalTambah')" style="position:absolute;top:16px;right:18px;background:none;border:none;font-size:1.3rem;cursor:pointer;color:#9ca3af;line-height:1;">&times;</button>
                    <h2 style="margin:0 0 4px;color:#059669;font-size:1.15rem;"><i class="fas fa-plus-circle"></i> Tambah Transaksi Baru</h2>
                    <p style="margin:0 0 22px;font-size:.85rem;color:#9ca3af;">Isi form untuk menambah data baru</p>
                    <form method="POST" action="config.php" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        <div class="form-group">
                            <label for="tNama" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Nama Pelanggan</label>
                            <input type="text" id="tNama" name="nama" placeholder="Masukkan nama" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
                        </div>
                        <div class="form-group">
                            <label for="tHP" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">No HP</label>
                            <input type="text" id="tHP" name="no_hp" placeholder="08xxxxxxxxxx" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
                        </div>
                        <div class="form-group">
                            <label for="tPencucian" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Jenis Pencucian</label>
                            <select id="tPencucian" name="jenis_pencucian" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;background:#fff;box-sizing:border-box;">
                                <option value="">Pilih jenis</option>
                                <option value="Cuci Kering">Cuci Kering</option>
                                <option value="Cuci Setrika">Cuci Setrika</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tLayanan" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Jenis Layanan</label>
                            <select id="tLayanan" name="jenis_layanan" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;background:#fff;box-sizing:border-box;">
                                <option value="">Pilih layanan</option>
                                <option value="Reguler">Reguler</option>
                                <option value="Express">Express</option>
                                <option value="Antar Jemput">Antar Jemput</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tTanggal" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Tanggal Penjemputan</label>
                            <input type="date" id="tTanggal" name="tanggal_penjemputan" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;">
                        </div>
                        <div class="form-group">
                            <label for="tJam" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Jam Penjemputan</label>
                            <input type="time" id="tJam" name="jam_penjemputan" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;">
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label for="tAlamat" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Alamat Pelanggan</label>
                            <input type="text" id="tAlamat" name="alamat" placeholder="Masukkan alamat lengkap"
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
                        </div>
                        <div style="grid-column:1/-1;display:flex;gap:12px;margin-top:4px;">
                            <button type="submit" name="addDataBtn" class="btn btn--primary" style="flex:1;padding:14px;justify-content:center;">
                                <i class="fas fa-plus"></i> Tambah Data
                            </button>
                            <button type="button" onclick="closeModal('modalTambah')" class="btn btn--outline" style="padding:14px 20px;">Batal</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL EDIT -->
            <div id="modalEdit" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
                <div style="background:#fff;border-radius:20px;padding:32px 36px;width:100%;max-width:640px;box-shadow:0 24px 60px rgba(0,0,0,.18);position:relative;max-height:90vh;overflow-y:auto;animation:modalIn .28s cubic-bezier(.22,.68,0,1.2) both;">
                    <button onclick="closeModal('modalEdit')" style="position:absolute;top:16px;right:18px;background:none;border:none;font-size:1.3rem;cursor:pointer;color:#9ca3af;line-height:1;">&times;</button>
                    <h2 style="margin:0 0 4px;color:#D97706;font-size:1.15rem;"><i class="fas fa-edit"></i> Edit Transaksi <span id="editKode"></span></h2>
                    <p style="margin:0 0 22px;font-size:.85rem;color:#9ca3af;">Ubah data transaksi di bawah ini</p>
                    <form method="POST" action="config.php" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <input type="hidden" id="eId" name="id_laundry">

                        <div class="form-group">
                            <label for="eNama" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Nama Pelanggan</label>
                            <input type="text" id="eNama" name="nama" placeholder="Masukkan nama" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
                        </div>
                        <div class="form-group">
                            <label for="eHP" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">No HP</label>
                            <input type="text" id="eHP" name="no_hp" placeholder="08xxxxxxxxxx" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
                        </div>
                        <div class="form-group">
                            <label for="ePencucian" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Jenis Pencucian</label>
                            <select id="ePencucian" name="jenis_pencucian" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;background:#fff;box-sizing:border-box;">
                                <option value="">Pilih jenis</option>
                                <option value="Cuci Kering">Cuci Kering</option>
                                <option value="Cuci Setrika">Cuci Setrika</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="eLayanan" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Jenis Layanan</label>
                            <select id="eLayanan" name="jenis_layanan" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;background:#fff;box-sizing:border-box;">
                                <option value="">Pilih layanan</option>
                                <option value="Reguler">Reguler</option>
                                <option value="Express">Express</option>
                                <option value="Antar Jemput">Antar Jemput</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="eTanggal" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Tanggal Penjemputan</label>
                            <input type="date" id="eTanggal" name="tanggal_penjemputan" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;">
                        </div>
                        <div class="form-group">
                            <label for="eJam" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Jam Penjemputan</label>
                            <input type="time" id="eJam" name="jam_penjemputan" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;">
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label for="eAlamat" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Alamat Pelanggan</label>
                            <input type="text" id="eAlamat" name="alamat" placeholder="Masukkan alamat lengkap"
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#D8A7B1'" onblur="this.style.borderColor='#f0e4e7'">
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label for="eStatus" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">Status</label>
                            <select id="eStatus" name="status" required
                                style="width:100%;padding:12px 14px;border:2px solid #f0e4e7;border-radius:10px;font-family:'Inter',sans-serif;font-size:.95rem;outline:none;background:#fff;box-sizing:border-box;">
                                <option value="Baru">Baru / Diterima</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Dikirim">Dikirim / Siap Jemput</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <div style="grid-column:1/-1;display:flex;gap:12px;margin-top:4px;">
                            <button type="submit" name="update" class="btn btn--primary" style="flex:1;padding:14px;justify-content:center;">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <button type="button" onclick="closeModal('modalEdit')" class="btn btn--outline" style="padding:14px 20px;">Batal</button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- ══════════ DATA TABLE ══════════ -->
            <div class="table-section" data-animate="fade-up">
                <div class="table-section__header">
                    <div>
                        <h2 class="table-section__title">Daftar Transaksi</h2>
                        <p class="table-section__subtitle">Total <?= $result->num_rows ?> data</p>
                    </div>
                    <!-- Filter Status & Actions -->
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                        <button id="toggleSelectBtn" onclick="toggleSelectMode()"
                            style="padding:10px 14px;border:1px solid transparent;border-radius:50px;
                                   font-family:'Inter',sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;
                                   background:var(--bg);color:var(--text);transition:.35s cubic-bezier(.22,1,.36,1);"
                            onmouseover="if(!this.classList.contains('active')) this.style.borderColor='var(--border)'" 
                            onmouseout="if(!this.classList.contains('active')) this.style.borderColor='transparent'">
                            <i class="fas fa-check-square"></i> Pilih
                        </button>
                        <form method="GET" action="table-op.php" id="filterForm" style="display:flex;gap:10px;align-items:center;">
                            <input type="hidden" name="filter_status" id="filterStatus" value="<?= htmlspecialchars($_GET['filter_status'] ?? '') ?>">
                            <div class="topbar__profile" onclick="this.classList.toggle('open')" style="display:flex;align-items:center;cursor:pointer;user-select:none;background:var(--bg);border:1px solid transparent;" onmouseover="this.style.borderColor='var(--border)'" onmouseout="this.style.borderColor='transparent'">
                                
                                <?php
                                $fs = $_GET['filter_status'] ?? '';
                                $activeLabel = 'Pesanan Aktif';
                                if($fs === 'Baru') $activeLabel = 'Baru';
                                if($fs === 'Diproses') $activeLabel = 'Diproses';
                                if($fs === 'Dikirim') $activeLabel = 'Dikirim';
                                ?>
                                <span class="topbar__name" style="padding: 2px 10px;"><?= $activeLabel ?></span>
                                <i class="fas fa-chevron-down" style="padding-right: 6px;"></i>

                                <!-- Dropdown Menu -->
                                <div class="topbar__dropdown" style="text-align:left;min-width:160px;">
                                    <a href="javascript:void(0)" onclick="event.stopPropagation(); document.getElementById('filterStatus').value=''; document.getElementById('filterForm').submit();" class="dropdown-item">Pesanan Aktif</a>
                                    <a href="javascript:void(0)" onclick="event.stopPropagation(); document.getElementById('filterStatus').value='Baru'; document.getElementById('filterForm').submit();" class="dropdown-item">Baru</a>
                                    <a href="javascript:void(0)" onclick="event.stopPropagation(); document.getElementById('filterStatus').value='Diproses'; document.getElementById('filterForm').submit();" class="dropdown-item">Diproses</a>
                                    <a href="javascript:void(0)" onclick="event.stopPropagation(); document.getElementById('filterStatus').value='Dikirim'; document.getElementById('filterForm').submit();" class="dropdown-item">Dikirim</a>
                                </div>
                            </div>
                            
                            <?php if (!empty($_GET['filter_status'])): ?>
                                <a href="table-op.php" style="font-size:.8rem;color:var(--text-muted);margin-left:8px;">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            <?php endif; ?>
                        </form>
                        <button onclick="openModal('modalTambah')" class="btn btn--primary" style="padding:8px 16px;font-size:.85rem;">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                    </div>
                </div>
                <style>
                    /* Checkbox column — smooth slide in/out */
                    .cb-col {
                        max-width: 0;
                        overflow: hidden;
                        opacity: 0;
                        padding-left: 0 !important;
                        padding-right: 0 !important;
                        white-space: nowrap;
                        transition: max-width .3s ease, opacity .3s ease, padding .3s ease;
                    }
                    #mainTable.select-mode .cb-col {
                        max-width: 50px;
                        opacity: 1;
                        padding-left: 12px !important;
                        padding-right: 12px !important;
                    }
                    /* Bulk toolbar fade+slide */
                    #bulkToolbar {
                        transition: opacity .25s ease, transform .25s ease;
                        transform: translateY(-6px);
                        opacity: 0;
                    }
                    #bulkToolbar.visible {
                        transform: translateY(0);
                        opacity: 1;
                    }
                    /* Pilih button */
                    #toggleSelectBtn {
                        transition: background .2s, color .2s, border-color .2s;
                    }
                    #toggleSelectBtn.active {
                        background: #C67A89 !important;
                        color: #fff !important;
                        border-color: #C67A89 !important;
                    }
                </style>
                <div id="bulkToolbar" style="display:none;background:linear-gradient(135deg,#C67A89,#D8A7B1);
                    border-radius:12px;padding:14px 20px;margin:12px 0;color:#fff;
                    display:none;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <span><i class="fas fa-check-square"></i> <strong id="selectedCount">0</strong> data dipilih</span>
                    <div style="display:flex;gap:8px;">
                        <form method="POST" action="config.php" id="bulkForm">
                            <input type="hidden" name="bulkHapus" value="1">
                            <div id="bulkInputs"></div>
                            <button type="button" onclick="confirmBulkDelete()"
                                style="background:#fff;color:#C67A89;border:none;padding:8px 16px;
                                       border-radius:8px;font-weight:700;cursor:pointer;font-size:.85rem;">
                                <i class="fas fa-trash-alt"></i> Hapus Terpilih
                            </button>
                        </form>
                        <button onclick="clearSelection()"
                            style="background:rgba(255,255,255,.2);color:#fff;border:none;padding:8px 14px;
                                   border-radius:8px;cursor:pointer;font-size:.85rem;">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table" id="mainTable">
                        <thead>
                            <tr>
                                <th class="cb-col" style="width:40px;text-align:center;">
                                    <input type="checkbox" id="selectAll" title="Pilih Semua"
                                        style="width:16px;height:16px;cursor:pointer;accent-color:#C67A89;">
                                </th>
                                <th>No</th>
                                <th>Kode Order</th>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Jenis Pencucian</th>
                                <th>Jenis Layanan</th>
                                <th>Tgl Jemput</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                    <tr class="data-row" data-id="<?= $row['id_laundry'] ?>">
                                        <td class="cb-col" style="text-align:center;">
                                            <input type="checkbox" class="row-cb" value="<?= $row['id_laundry'] ?>"
                                                style="width:16px;height:16px;cursor:pointer;accent-color:#C67A89;">
                                        </td>
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
                                        <td><?= e($row['jam_penjemputan']) ?></td>
                                        <td>
                                            <?php
                                                $s = $row['status'] ?? 'Baru';
                                                $cls = 'badge--waiting';
                                                if ($s == 'Baru') $cls = 'badge--waiting';
                                                if ($s == 'Diproses') $cls = 'badge--process';
                                                if ($s == 'Dikirim') $cls = 'badge--process';
                                                if ($s == 'Selesai') $cls = 'badge--done';
                                                if ($s == 'Dibatalkan') $cls = 'badge--cancelled';
                                            ?>
                                            <span class="badge <?= $cls ?>"><span class="badge__dot"></span><?= $s ?></span>
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:6px;">
                                                <button type="button" class="btn btn--edit btn--sm" title="Edit"
                                                    onclick="openEditModal(
                                                        '<?= $row['id_laundry'] ?>',
                                                        '<?= e($row['kode_order']) ?>',
                                                        '<?= e($row['nama']) ?>',
                                                        '<?= e($row['no_hp']) ?>',
                                                        '<?= e($row['jenis_pencucian']) ?>',
                                                        '<?= e($row['jenis_layanan']) ?>',
                                                        '<?= $row['tanggal_penjemputan'] ?>',
                                                        '<?= e($row['jam_penjemputan']) ?>',
                                                        '<?= e($row['alamat'] ?? '') ?>',
                                                        '<?= e($row['status'] ?? 'Menunggu') ?>'
                                                    )">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <a href="config.php?hapus=<?= $row['id_laundry'] ?>" class="btn btn--danger btn--sm" title="Hapus"
                                                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" style="text-align:center;padding:40px;color:var(--text-muted);">
                                        <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                                        Belum ada data transaksi
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <style>
        @keyframes modalIn {
            from { opacity:0; transform:scale(.94) translateY(10px); }
            to   { opacity:1; transform:scale(1)  translateY(0);     }
        }
    </style>
    <script>
        /* ══ Modal Helpers ══ */
        window.openModal = function(id) {
            var m = document.getElementById(id);
            m.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };
        window.closeModal = function(id) {
            var m = document.getElementById(id);
            m.style.display = 'none';
            document.body.style.overflow = '';
        };
        // Close modal when clicking backdrop
        ['modalTambah','modalEdit'].forEach(function(id){
            document.getElementById(id).addEventListener('click', function(e){
                if(e.target === this) closeModal(id);
            });
        });

        window.openEditModal = function(id, kode, nama, hp, pencucian, layanan, tanggal, jam, alamat, status) {
            document.getElementById('eId').value        = id;
            document.getElementById('editKode').textContent = '#' + kode;
            document.getElementById('eNama').value     = nama;
            document.getElementById('eHP').value       = hp;
            document.getElementById('eTanggal').value  = tanggal;
            document.getElementById('eJam').value      = jam;
            document.getElementById('eAlamat').value   = alamat;
            // selects
            setSelect('ePencucian', pencucian);
            setSelect('eLayanan',   layanan);
            setSelect('eStatus',    status);
            openModal('modalEdit');
        };
        function setSelect(id, val) {
            var s = document.getElementById(id);
            for(var i=0;i<s.options.length;i++){
                if(s.options[i].value === val){ s.selectedIndex=i; break; }
            }
        }

        /* UI Interaktif saja — sidebar toggle, overlay, animations */
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const overlay = document.getElementById('overlay');
            const isMobile = () => window.innerWidth <= 768;

            sidebarToggle.addEventListener('click', () => {
                if (isMobile()) {
                    sidebar.classList.toggle('mobile-open');
                    overlay.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                }
            });
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            });

            /* Scroll animations */
            document.querySelectorAll('[data-animate]').forEach((el, i) => {
                setTimeout(() => el.classList.add('animated'), (parseInt(el.dataset.delay || 0)) + (i * 80));
            });

            /* Profile dropdown */
            const profileToggle = document.getElementById('profileToggle');
            profileToggle.addEventListener('click', e => {
                e.stopPropagation();
                profileToggle.classList.toggle('open');
            });
            document.addEventListener('click', () => profileToggle.classList.remove('open'));

            /* ── Multi-select logic ── */
            const selectAll  = document.getElementById('selectAll');
            const toolbar    = document.getElementById('bulkToolbar');
            const countEl    = document.getElementById('selectedCount');

            function getChecked() {
                return [...document.querySelectorAll('.row-cb:checked')];
            }

            function updateToolbar() {
                const checked = getChecked();
                if (checked.length > 0) {
                    toolbar.style.display = 'flex';
                    requestAnimationFrame(() => toolbar.classList.add('visible'));
                    countEl.textContent = checked.length;
                } else {
                    toolbar.classList.remove('visible');
                    setTimeout(() => toolbar.style.display = 'none', 260);
                }
                selectAll.indeterminate = checked.length > 0 && checked.length < document.querySelectorAll('.row-cb').length;
                selectAll.checked = checked.length === document.querySelectorAll('.row-cb').length && checked.length > 0;
            }

            selectAll.addEventListener('change', () => {
                document.querySelectorAll('.row-cb').forEach(cb => cb.checked = selectAll.checked);
                updateToolbar();
            });

            document.querySelectorAll('.row-cb').forEach(cb => {
                cb.addEventListener('change', updateToolbar);
            });
        });

        window.toggleSelectMode = function() {
            const table = document.getElementById('mainTable');
            const btn   = document.getElementById('toggleSelectBtn');
            const isOn  = table.classList.toggle('select-mode');
            btn.classList.toggle('active', isOn);
            btn.innerHTML = isOn
                ? '<i class="fas fa-times"></i> Batal Pilih'
                : '<i class="fas fa-check-square"></i> Pilih';
            if (!isOn) clearSelection();
        };

        window.clearSelection = function() {
            document.querySelectorAll('.row-cb').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            document.getElementById('bulkToolbar').style.display = 'none';
        };

        window.confirmBulkDelete = function() {
            const checked = [...document.querySelectorAll('.row-cb:checked')];
            if (checked.length === 0) return;
            if (!confirm(`Yakin ingin menghapus ${checked.length} data yang dipilih?`)) return;
            const container = document.getElementById('bulkInputs');
            container.innerHTML = '';
            checked.forEach(cb => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = cb.value;
                container.appendChild(inp);
            });
            document.getElementById('bulkForm').submit();
        };
    </script>
</body>
</html>
