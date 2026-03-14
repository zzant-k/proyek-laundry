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
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --font: 'Inter', system-ui, sans-serif;
  --bg: #faf5f7;
  --card: #ffffff;
  --border: #f0e4e7;
  --accent: #c67a89;
  --accent-hover: #b06a79;
  --accent-light: #fdf5f8;
  --accent-subtle: rgba(198, 122, 137, 0.08);
  --heading: #1e293b;
  --text: #475569;
  --text-secondary: #94a3b8;
  --radius: 20px;
  --radius-sm: 12px;
  --shadow: 0 1px 3px rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.04);
  --shadow-md: 0 4px 24px rgba(198,122,137,0.08), 0 1px 4px rgba(0,0,0,0.04);
  --transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

html { scroll-behavior: smooth; }
body {
  font-family: var(--font);
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}

/* ── Topbar ── */
.topbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border);
  padding: 0 32px;
  height: 64px;
  display: flex; align-items: center; justify-content: space-between;
}
.topbar__back {
  display: inline-flex; align-items: center; gap: 8px;
  color: var(--accent); text-decoration: none;
  font-size: 14px; font-weight: 600;
  padding: 8px 16px; border-radius: 50px;
  background: var(--accent-light);
  border: 1.5px solid var(--border);
  transition: var(--transition);
}
.topbar__back:hover {
  background: var(--accent); color: #fff;
  border-color: var(--accent);
  transform: translateX(-2px);
}
.topbar__brand {
  display: flex; align-items: center; gap: 8px;
  text-decoration: none; color: var(--heading);
  font-size: 16px; font-weight: 700;
}
.topbar__brand img { height: 32px; width: auto; object-fit: contain; }

/* ── Page ── */
.page-wrapper {
  max-width: 900px;
  margin: 0 auto;
  padding: 32px 20px 80px;
}

/* ── Page Header ── */
.page-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
}
.page-header__left h1 {
  font-size: 24px; font-weight: 800; color: var(--heading);
  letter-spacing: -0.02em;
}
.page-header__left p {
  font-size: 14px; color: var(--text-secondary); margin-top: 4px;
}

/* ── Stats Cards ── */
.stats-row {
  display: flex; gap: 14px; margin-bottom: 24px; flex-wrap: wrap;
}
.stat-card {
  flex: 1; min-width: 140px;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 18px 20px;
  display: flex; align-items: center; gap: 14px;
  box-shadow: var(--shadow);
}
.stat-card__icon {
  width: 42px; height: 42px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
}
.stat-card__icon--total { background: #fdf5f8; color: var(--accent); }
.stat-card__icon--active { background: #dbeafe; color: #2563eb; }
.stat-card__icon--done { background: #d1fae5; color: #059669; }

.stat-card__info span { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); }
.stat-card__info strong { display: block; font-size: 22px; font-weight: 800; color: var(--heading); }

/* ── Flash Alert ── */
.alert {
  padding: 14px 20px; border-radius: var(--radius-sm);
  margin-bottom: 20px; font-size: 14px; font-weight: 500;
  display: flex; align-items: center; gap: 10px;
  animation: slideDown 0.35s ease both;
}
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.alert--success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.alert--danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* ── Table Card ── */
.table-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow-md);
}
.table-card__header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid var(--border);
  flex-wrap: wrap; gap: 12px;
}
.table-card__title {
  font-size: 16px; font-weight: 700; color: var(--heading);
  display: flex; align-items: center; gap: 10px;
}
.table-card__title-icon {
  width: 32px; height: 32px; border-radius: 8px;
  background: var(--accent-light);
  display: flex; align-items: center; justify-content: center;
  color: var(--accent); font-size: 14px;
}

/* Filter tabs */
.filter-tabs {
  display: flex; gap: 4px;
  background: var(--bg);
  border-radius: 10px;
  padding: 4px;
}
.filter-tab {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px; font-weight: 600;
  color: var(--text-secondary);
  background: transparent;
  border: none; cursor: pointer;
  font-family: var(--font);
  transition: var(--transition);
}
.filter-tab.active {
  background: var(--card);
  color: var(--accent);
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.filter-tab:hover:not(.active) { color: var(--text); }

/* ── Table ── */
.table-wrapper { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead th {
  padding: 12px 16px;
  font-size: 11px; font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: var(--text-secondary);
  background: var(--bg);
  text-align: left;
  white-space: nowrap;
}
tbody td {
  padding: 14px 16px;
  font-size: 13px;
  border-bottom: 1px solid var(--border);
  color: var(--text);
}
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover { background: var(--accent-subtle); }

/* ── Status Badges ── */
.badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 12px; border-radius: 100px;
  font-size: 11px; font-weight: 700;
  white-space: nowrap;
}
.badge__dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  display: inline-block;
}
.badge--baru { background: #dbeafe; color: #1d4ed8; }
.badge--baru .badge__dot { background: #1d4ed8; }
.badge--diproses { background: #fef3c7; color: #d97706; }
.badge--diproses .badge__dot { background: #d97706; }
.badge--dikirim { background: #e0e7ff; color: #4f46e5; }
.badge--dikirim .badge__dot { background: #4f46e5; }
.badge--selesai { background: #d1fae5; color: #059669; }
.badge--selesai .badge__dot { background: #059669; }
.badge--dibatalkan { background: #fee2e2; color: #dc2626; }
.badge--dibatalkan .badge__dot { background: #dc2626; }

/* ── Cancel button ── */
.btn-cancel-order {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 8px;
  font-family: var(--font); font-size: 11px; font-weight: 600;
  color: #dc2626; background: #fef2f2;
  border: 1px solid #fecaca;
  cursor: pointer; transition: var(--transition);
  white-space: nowrap;
}
.btn-cancel-order:hover {
  background: #dc2626; color: #fff; border-color: #dc2626;
}

/* ── Empty State ── */
.empty-state {
  text-align: center; padding: 60px 20px;
  color: var(--text-secondary);
}
.empty-state i { font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.3; }
.empty-state h3 { font-size: 16px; font-weight: 700; color: var(--heading); margin-bottom: 6px; }
.empty-state p { font-size: 13px; }

/* ── Responsive ── */
@media (max-width: 600px) {
  .topbar { padding: 0 16px; }
  .page-wrapper { padding: 20px 16px 60px; }
  .page-header { flex-direction: column; align-items: flex-start; }
  .page-header__left h1 { font-size: 20px; }
  .stat-card { min-width: 100%; }
  .table-card__header { flex-direction: column; align-items: flex-start; }
  thead th, tbody td { padding: 10px 12px; }
}

/* ── Cancel confirm overlay ── */
.cancel-overlay {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(30,41,59,0.45);
  backdrop-filter: blur(6px);
  display: none; align-items: center; justify-content: center;
  padding: 20px;
}
.cancel-overlay.active { display: flex; }
.cancel-card {
  background: var(--card); border-radius: var(--radius);
  padding: 32px; max-width: 420px; width: 100%;
  text-align: center;
  box-shadow: 0 25px 80px rgba(0,0,0,0.18);
  animation: modalIn 0.3s cubic-bezier(0.22,1,0.36,1) both;
}
@keyframes modalIn {
  from { opacity: 0; transform: translateY(20px) scale(0.97); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
.cancel-card h3 { font-size: 18px; font-weight: 700; color: var(--heading); margin-bottom: 8px; }
.cancel-card p { font-size: 13px; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.7; }
.cancel-card .btn-row { display: flex; gap: 10px; }
.cancel-card .btn-row button, .cancel-card .btn-row a {
  flex: 1; padding: 12px; border-radius: 10px;
  font-family: var(--font); font-size: 14px; font-weight: 600;
  cursor: pointer; text-decoration: none;
  display: flex; align-items: center; justify-content: center; gap: 6px;
  transition: var(--transition); border: none;
}
.cancel-card .btn-keep { background: var(--bg); color: var(--text); border: 1px solid var(--border); }
.cancel-card .btn-keep:hover { background: #f1f5f9; }
.cancel-card .btn-confirm-cancel { background: #dc2626; color: #fff; }
.cancel-card .btn-confirm-cancel:hover { background: #b91c1c; }
</style>
</head>
<body>

<!-- ── Topbar ── -->
<div class="topbar">
  <a href="index.php" class="topbar__back"><i class="fas fa-arrow-left"></i> Kembali</a>
  <a href="index.php" class="topbar__brand">
    <img src="assets/img/RL.png" alt="Logo"> Rumah Laundry
  </a>
</div>

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

<script>
// Cancel modal
function showCancelModal(id, kode) {
  document.getElementById('cancelId').value = id;
  document.getElementById('cancelKode').textContent = '#' + kode;
  document.getElementById('cancelOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
  document.getElementById('cancelOverlay').classList.remove('active');
  document.body.style.overflow = '';
}
document.getElementById('cancelOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeCancelModal();
});

// Filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('#orderBody tr[data-group]').forEach(row => {
      if (filter === 'all' || row.dataset.group === filter) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
});
</script>
</body>
</html>
