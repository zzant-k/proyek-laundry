<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Riwayat Pesanan (User)
 * ═══════════════════════════════════════════════════════
 */
require_once 'dashboard/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['id'];
$flash = getFlash();

// Fetch all orders for this user
$stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_user = ? ORDER BY id_laundry DESC");
$stmt->bind_param('i', $userId);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

// Count stats
$stTotal = $conn->prepare("SELECT COUNT(*) as c FROM transaksi WHERE id_user = ?");
$stTotal->bind_param('i', $userId);
$stTotal->execute();
$totalOrders = $stTotal->get_result()->fetch_assoc()['c'];
$stTotal->close();

$stActive = $conn->prepare("SELECT COUNT(*) as c FROM transaksi WHERE id_user = ? AND status IN ('Baru','Diproses','Dikirim')");
$stActive->bind_param('i', $userId);
$stActive->execute();
$activeOrders = $stActive->get_result()->fetch_assoc()['c'];
$stActive->close();

$userName = e($_SESSION['nama'] ?? 'User');
$initials = '';
$parts = explode(' ', $_SESSION['nama'] ?? '');
foreach (array_slice($parts, 0, 2) as $p) $initials .= strtoupper(substr($p, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Riwayat Pesanan — Rumah Laundry</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="css/riwayat_pesanan.css?v=<?= time() ?>">
</head>
<body>



<div class="page-wrapper">

  <?php if ($flash): ?>
    <div class="alert alert--<?= e($flash['type'] === 'success' ? 'success' : 'danger') ?>">
      <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
      <?= e($flash['message']) ?>
    </div>
  <?php endif; ?>

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header__left">
      <h1>Riwayat Pesanan</h1>
      <p>Semua pesanan Anda di Rumah Laundry</p>
    </div>
    <a href="index.php" class="topbar__back"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>


  <!-- Orders Table -->
  <div class="table-card">
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
                <td><code style="background:var(--bg);padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;"><?= e($row['kode_order']) ?></code></td>
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

<script src="script/riwayat_pesanan.js?v=<?= time() ?>"></script>
</body>
</html>
