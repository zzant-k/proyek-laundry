<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Profile Page
 * ═══════════════════════════════════════════════════════
 */
require_once 'dashboard/config.php';

// Harus login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['id'];

// Cek apakah profile sudah ada, jika belum auto-create dari data user
$stCheck = $conn->prepare("SELECT * FROM profile WHERE id_user = ? LIMIT 1");
$stCheck->bind_param('i', $userId);
$stCheck->execute();
$profile = $stCheck->get_result()->fetch_assoc();
$stCheck->close();

if (!$profile) {
    $stUser = $conn->prepare("SELECT nama, email, no_hp FROM user WHERE iduser = ? LIMIT 1");
    $stUser->bind_param('i', $userId);
    $stUser->execute();
    $userData = $stUser->get_result()->fetch_assoc();
    $stUser->close();

    if ($userData) {
        $stInsert = $conn->prepare("INSERT INTO profile (id_user, nama, email, no_hp) VALUES (?, ?, ?, ?)");
        $stInsert->bind_param('isss', $userId, $userData['nama'], $userData['email'], $userData['no_hp']);
        $stInsert->execute();
        $stInsert->close();

        $stCheck = $conn->prepare("SELECT * FROM profile WHERE id_user = ? LIMIT 1");
        $stCheck->bind_param('i', $userId);
        $stCheck->execute();
        $profile = $stCheck->get_result()->fetch_assoc();
        $stCheck->close();
    }
}

$flash = getFlash();

// Fetch all orders for this user
$stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_user = ? ORDER BY id_laundry DESC");
$stmt->bind_param('i', $userId);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

// Initials
$initials = '';
$nameParts = explode(' ', $profile['nama'] ?? '');
foreach (array_slice($nameParts, 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Profil Saya — Rumah Laundry</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="css/profile.css?v=<?= time() ?>">
</head>
<body>

<!-- ── Topbar ── -->


<!-- ── Page Content ── -->
<div class="page-wrapper">
  

  <?php if ($flash): ?>
    <div class="alert alert--<?= e($flash['type'] === 'success' ? 'success' : 'danger') ?>">
      <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
      <?= e($flash['message']) ?>
    </div>
  <?php endif; ?>

  <!-- ══════════════════════════════
       PROFILE CARDS GRID
       ══════════════════════════════ -->
  <div class="profile-grid">
  
    <!-- Left Card: Profile & Stats -->
    <div class="profile-hero animate-in" style="background: var(--card); overflow: visible; position: relative; height: 100%;">

      <!-- Action Buttons Top Right (Fixed) -->
      <div style="position: fixed; right: 20px; top: 20px; z-index: 10;">
        <button type="button" class="btn-edit" id="btnEdit">
          <i class="fas fa-pen"></i> Edit Profil
        </button>
      </div>
      <div style="position: fixed; left: 20px; top: 20px; z-index: 10;">
        <a href="index.php" class="topbar__back"><i class="fas fa-arrow-left"></i> Kembali</a>
      </div>

      <!-- Gradient Banner -->
      <div class="profile-hero__banner" style="border-radius: var(--radius) var(--radius) 0 0; overflow: hidden;">
        <div class="profile-hero__pattern"></div>
        <div class="profile-hero__deco">
          <!-- Bubbles group -->
          <svg class="deco-bubbles" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="55" cy="25" r="18" stroke="white" stroke-width="2" fill="none"/>
            <circle cx="25" cy="55" r="12" stroke="white" stroke-width="1.5" fill="none"/>
            <circle cx="65" cy="60" r="8" stroke="white" stroke-width="1.5" fill="none"/>
            <circle cx="20" cy="20" r="5" fill="white" fill-opacity="0.3"/>
          </svg>
          <svg class="deco-sparkle1" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 2 L20 16 L34 18 L20 20 L18 34 L16 20 L2 18 L16 16 Z" fill="white"/>
          </svg>
          <svg class="deco-sparkle2" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2 L15.5 12.5 L26 14 L15.5 15.5 L14 26 L12.5 15.5 L2 14 L12.5 12.5 Z" fill="white"/>
          </svg>
          <svg class="deco-shirt" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M25 8 C25 8 20 5 14 10 L4 22 L16 26 L16 62 L54 62 L54 26 L66 22 L56 10 C50 5 45 8 45 8 C43 14 37 17 35 17 C33 17 27 14 25 8Z" fill="white" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
          </svg>
          <svg class="deco-hanger" viewBox="0 0 60 45" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M30 5 C30 5 35 5 35 10 C35 13 32 14.5 30 16 L8 32 C5 33.5 5 37.5 8 39 L52 39 C55 37.5 55 33.5 52 32 Z" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="30" cy="5" r="3" stroke="white" stroke-width="1.5" fill="none"/>
          </svg>
          <svg class="deco-dots" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="3" fill="white" fill-opacity="0.4"/>
            <circle cx="25" cy="8" r="2" fill="white" fill-opacity="0.3"/>
            <circle cx="40" cy="15" r="3.5" fill="white" fill-opacity="0.35"/>
            <circle cx="15" cy="30" r="2.5" fill="white" fill-opacity="0.25"/>
            <circle cx="35" cy="35" r="2" fill="white" fill-opacity="0.3"/>
            <circle cx="45" cy="40" r="1.5" fill="white" fill-opacity="0.2"/>
          </svg>
        </div>
      </div>

      <!-- Content Area -->
      <div class="profile-hero__content" style="padding-top: 0; padding-bottom: 32px; border: 1px solid var(--border); border-top: none; border-radius: 0 0 var(--radius) var(--radius); position: relative; height: calc(100% - 120px); display: flex; flex-direction: column;">

        <div class="profile-avatar-wrap">
          <div class="profile-avatar">
            <?php if (!empty($profile['foto_profile']) && file_exists('assets/uploads/' . $profile['foto_profile'])): ?>
              <img src="assets/uploads/<?= e($profile['foto_profile']) ?>" alt="Foto Profil">
            <?php else: ?>
              <?= $initials ?>
            <?php endif; ?>
          </div>
        </div>

        <h1 class="profile-name"><?= e($profile['nama']) ?></h1>
        
        <div class="profile-stats" style="margin-top: auto; padding-top: 24px;">
          <div class="profile-stat">
            <span class="profile-stat__value"><?= $orders->num_rows ?></span>
            <span class="profile-stat__label">Pesanan</span>
          </div>
          <div class="profile-stat">
            <span class="profile-stat__value"><i class="fas fa-check-circle" style="color:#22c55e;font-size:16px;"></i></span>
            <span class="profile-stat__label">Terverifikasi</span>
          </div>
        </div>
      </div>
    </div> <!-- /Left Card -->

    <!-- Right Card: Contact Info -->
    <div class="profile-info-card animate-in animate-in--delay1" style="background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; position: relative; display: flex; flex-direction: column; box-shadow: var(--shadow-lg);">
       
        <!-- Interesting Background Illustration (Full Right Side) -->
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; right: -40px; bottom: -40px; width: 280px; height: 280px; opacity: 0.15; pointer-events: none; z-index: 0;">
            <circle cx="100" cy="100" r="80" fill="#c67a89" />
            <circle cx="100" cy="100" r="60" fill="var(--bg-white)" fill-opacity="0.5"/>
            <!-- Wave Abstract -->
            <path d="M20 120 Q 80 80 140 120 T 180 80 L 180 180 L 20 180 Z" fill="#b06a79"/>
            <!-- Simple Machine Shape -->
            <rect x="60" y="60" width="80" height="90" rx="10" fill="var(--bg-white)" stroke="#b06a79" stroke-width="4"/>
            <circle cx="100" cy="110" r="25" fill="#c67a89" stroke="#b06a79" stroke-width="3"/>
            <circle cx="100" cy="110" r="15" fill="var(--bg-white)"/>
            <rect x="75" y="75" width="50" height="10" rx="2" fill="#b06a79"/>
            <circle cx="20" cy="60" r="10" fill="#c67a89"/>
            <circle cx="160" cy="40" r="15" fill="#c67a89"/>
            <path d="M 120 20 L 125 35 L 140 38 L 125 41 L 120 56 L 115 41 L 100 38 L 115 35 Z" fill="#fbbf24" opacity="0.8"/>
            <path d="M 40 150 L 42 160 L 52 162 L 42 164 L 40 174 L 38 164 L 28 162 L 38 160 Z" fill="#fbbf24" opacity="0.6"/>
        </svg>

        <div style="padding: 32px 32px 20px 32px; border-bottom: 1px solid var(--border); position: relative; z-index: 2;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--heading); display: flex; align-items: center; gap: 12px; margin: 0;">
                <span style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: var(--accent-light); border-radius: 10px;">
                    <i class="fas fa-address-book" style="color: var(--accent); font-size: 14px;"></i>
                </span>
                Informasi Kontak
            </h3>
        </div>

        <div style="padding: 32px; flex: 1; position: relative; z-index: 2; display: flex; flex-direction: column; gap: 24px;">
            <div class="info-item" style="padding: 16px; background: rgba(255,255,255,0.7); border: 1px solid rgba(198,122,137,0.15); border-radius: 12px; backdrop-filter: blur(4px);">
              <div class="info-item__label" style="margin-bottom: 8px; color: var(--text-secondary);"><i class="fas fa-envelope" style="color: var(--accent); background: none; width: auto; height: auto;"></i> Email</div>
              <div class="info-item__value" style="font-weight: 600; font-size: 16px; color: var(--heading); padding-left: 28px;"><?= e($profile['email']) ?></div>
            </div>
            
            <div class="info-item" style="padding: 16px; background: rgba(255,255,255,0.7); border: 1px solid rgba(198,122,137,0.15); border-radius: 12px; backdrop-filter: blur(4px);">
              <div class="info-item__label" style="margin-bottom: 8px; color: var(--text-secondary);"><i class="fas fa-phone" style="color: var(--accent); background: none; width: auto; height: auto;"></i> No. HP</div>
              <div class="info-item__value" style="font-weight: 600; font-size: 16px; color: var(--heading); padding-left: 28px;"><?= e($profile['no_hp']) ?: '-' ?></div>
            </div>
        </div>
    </div> <!-- /Right Card -->
    
  </div> <!-- /Profile Grid -->

  <!-- ══════════════════════════════
       ORDER HISTORY (RIWAYAT PESANAN)
       ══════════════════════════════ -->
  <div class="table-card" style="margin-top: 32px;">
    <div class="table-card__header">
      <h2 class="table-card__title">
        <span class="table-card__title-icon"><i class="fas fa-list"></i></span>
        Daftar Pesanan
      </h2>
      <div class="filter-tabs" id="filterTabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="active">Aktif</button>
        <button class="filter-tab" data-filter="done">Selesai</button>
        <button class="filter-tab" data-filter="cancelled">Dibatalkan</button>
      </div>
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Kode Order</th>
            <th>Tanggal</th>
            <th>Jenis Pencucian</th>
            <th>Jenis Layanan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="orderBody">
          <?php if ($orders->num_rows > 0): ?>
            <?php while($row = $orders->fetch_assoc()):
              $s = $row['status'] ?? 'Baru';
              $badgeClass = 'badge--baru';
              if ($s === 'Diproses')   $badgeClass = 'badge--diproses';
              if ($s === 'Dikirim')    $badgeClass = 'badge--dikirim';
              if ($s === 'Selesai')    $badgeClass = 'badge--selesai';
              if ($s === 'Dibatalkan') $badgeClass = 'badge--dibatalkan';

              $filterGroup = 'active';
              if ($s === 'Selesai') $filterGroup = 'done';
              if ($s === 'Dibatalkan') $filterGroup = 'cancelled';
            ?>
              <tr data-group="<?= $filterGroup ?>">
                <td><code style="background:var(--bg);padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;color:var(--heading);"><?= e($row['kode_order']) ?></code></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal_penjemputan'])) ?></td>
                <td><?= e($row['jenis_pencucian']) ?></td>
                <td><?= e($row['jenis_layanan']) ?></td>
                <td><span class="badge <?= $badgeClass ?>"><span class="badge__dot"></span><?= $s ?></span></td>
                <td>
                  <?php if ($s === 'Baru'): ?>
                    <button type="button" class="btn-cancel-order"
                      onclick="showCancelModal('<?= $row['id_laundry'] ?>', '<?= e($row['kode_order']) ?>')">
                      <i class="fas fa-times-circle"></i> Batalkan
                    </button>
                  <?php elseif ($s === 'Dibatalkan'): ?>
                    <span style="font-size:11px;color:var(--text-secondary);">—</span>
                  <?php else: ?>
                    <span style="font-size:11px;color:var(--text-secondary);">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">
                <div class="empty-state">
                  <i class="fas fa-inbox"></i>
                  <h3>Belum ada pesanan</h3>
                  <p>Pesanan Anda akan muncul di sini setelah melakukan pemesanan</p>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- ══════════════════════════════
     EDIT PROFILE POPUP
     ══════════════════════════════ -->
<div class="modal-overlay" id="editModal">
  <div class="modal-card">
    <div class="modal-header">
      <h2><i class="fas fa-pen-to-square"></i> Edit Profil</h2>
      <button type="button" class="modal-close" id="modalClose">×</button>
    </div>
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label for="edit_nama">Nama Lengkap</label>
            <input type="text" id="edit_nama" name="nama" value="<?= e($profile['nama']) ?>" required>
          </div>
          <div class="form-group">
            <label for="edit_hp">No. HP</label>
            <input type="tel" id="edit_hp" name="no_hp" value="<?= e($profile['no_hp']) ?>" placeholder="08xxxxxxxxxx">
          </div>
        </div>
        <div class="form-group">
          <label for="edit_email">Email</label>
          <input type="email" id="edit_email" name="email" value="<?= e($profile['email']) ?>" required>
        </div>
        <div class="form-group">
          <label>Foto Profil</label>
          <label class="upload-area" for="edit_foto">
            <div class="upload-area__icon"><i class="fas fa-camera"></i></div>
            <div class="upload-area__text" id="uploadText">
              <strong>Klik untuk upload foto</strong><br>
              JPG, PNG (Maks. 2MB)
            </div>
          </label>
          <input type="file" id="edit_foto" name="foto_profile" accept="image/jpeg,image/png" style="display:none">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" id="modalCancel">Batal</button>
        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Cancel Confirmation Overlay -->
<div class="cancel-overlay" id="cancelOverlay">
  <div class="cancel-card">
    <div style="width:56px;height:56px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:24px;color:#dc2626;">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h3>Batalkan Pesanan?</h3>
    <p>Pesanan <strong id="cancelKode"></strong> akan dibatalkan. Tindakan ini tidak dapat dikembalikan.</p>
    <div class="btn-row">
      <button type="button" class="btn-keep" onclick="closeCancelModal()">Tidak, Simpan</button>
      <form method="POST" action="batalkan_pesanan.php" style="flex:1;display:flex;">
        <input type="hidden" name="id_laundry" id="cancelId">
        <button type="submit" class="btn-confirm-cancel" style="width:100%;">
          <i class="fas fa-times"></i> Ya, Batalkan
        </button>
      </form>
    </div>
  </div>
</div>

<script src="script/profile.js?v=<?= time() ?>"></script>
<script src="script/riwayat_pesanan.js?v=<?= time() ?>"></script>
</body>
</html>
