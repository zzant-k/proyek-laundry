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
  --gradient-banner: linear-gradient(135deg, #d4899a 0%, #c67a89 30%, #b56878 60%, #d4899a 100%);
  --heading: #1e293b;
  --text: #475569;
  --text-secondary: #94a3b8;
  --radius: 20px;
  --radius-sm: 12px;
  --shadow: 0 1px 3px rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.04);
  --shadow-md: 0 4px 24px rgba(198,122,137,0.08), 0 1px 4px rgba(0,0,0,0.04);
  --shadow-lg: 0 12px 40px rgba(198,122,137,0.12), 0 2px 8px rgba(0,0,0,0.04);
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

/* ══════════════════════════════
   TOPBAR
   ══════════════════════════════ */
.topbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
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

/* ══════════════════════════════
   PAGE LAYOUT
   ══════════════════════════════ */
.page-wrapper {
  max-width: 620px;
  margin: 0 auto;
  padding: 24px 20px 60px;
}

/* ══════════════════════════════
   HERO BANNER
   ══════════════════════════════ */
.profile-hero {
  position: relative;
  background: var(--gradient-banner);
  border-radius: var(--radius);
  overflow: hidden;
  margin-bottom: 24px;
  box-shadow: var(--shadow-lg);
}

.profile-hero__banner {
  position: relative;
  height: 120px;
  overflow: hidden;
}

/* Decorative SVG illustrations on the banner */
.profile-hero__deco {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.profile-hero__deco svg {
  position: absolute;
  opacity: 0.15;
}

.deco-bubbles { top: 15px; right: 30px; width: 80px; height: 80px; animation: floatSlow 6s ease-in-out infinite; }
.deco-sparkle1 { top: 25px; left: 40px; width: 36px; height: 36px; animation: twinkle 3s ease-in-out infinite; }
.deco-sparkle2 { bottom: 20px; right: 120px; width: 28px; height: 28px; animation: twinkle 3s ease-in-out 1.2s infinite; }
.deco-shirt { bottom: 10px; left: 20px; width: 70px; height: 70px; animation: floatSlow 8s ease-in-out 0.5s infinite; opacity: 0.12; }
.deco-hanger { top: 10px; right: 160px; width: 60px; height: 50px; animation: floatSlow 7s ease-in-out 1s infinite; }
.deco-dots { bottom: 30px; left: 160px; width: 50px; height: 50px; animation: twinkle 4s ease-in-out 0.8s infinite; }

/* Pattern overlay */
.profile-hero__pattern {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.08) 1px, transparent 0);
  background-size: 24px 24px;
}

/* Profile content area below banner */
.profile-hero__content {
  position: relative;
  padding: 0 36px 32px;
  background: var(--card);
  text-align: center;
}

/* Avatar — overlapping the banner */
.profile-avatar-wrap {
  display: flex; justify-content: center;
  margin-top: -44px;
  position: relative;
  z-index: 2;
  margin-bottom: 12px;
}

.profile-avatar {
  width: 80px; height: 80px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 32px; font-weight: 800;
  color: var(--accent);
  background: var(--card);
  border: 4px solid var(--card);
  box-shadow: 0 4px 20px rgba(198,122,137,0.2);
  overflow: hidden;
  transition: var(--transition);
}
.profile-avatar:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 28px rgba(198,122,137,0.28);
}
.profile-avatar img {
  width: 100%; height: 100%;
  object-fit: cover; border-radius: 50%;
}

.profile-name {
  font-size: 22px; font-weight: 800;
  color: var(--heading);
  margin-bottom: 4px;
  letter-spacing: -0.02em;
}

.profile-email {
  font-size: 14px;
  color: var(--text-secondary);
  margin-bottom: 14px;
}

.profile-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 16px;
  font-size: 11px; font-weight: 700;
  color: var(--accent);
  background: var(--accent-light);
  border: 1.5px solid var(--border);
  border-radius: 100px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}
.profile-badge i { font-size: 9px; }

/* Quick stats row */
.profile-stats {
  display: flex;
  justify-content: center;
  gap: 32px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid var(--border);
}

.profile-stat {
  text-align: center;
}

.profile-stat__value {
  font-size: 18px; font-weight: 800;
  color: var(--heading);
  display: block;
}

.profile-stat__label {
  font-size: 11px; font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ══════════════════════════════
   INFO CARD
   ══════════════════════════════ */
.info-card {
  position: relative;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 32px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
}

/* Subtle decorative corner accent */
.info-card::before {
  content: '';
  position: absolute;
  top: -40px; right: -40px;
  width: 120px; height: 120px;
  background: radial-gradient(circle, var(--accent-subtle) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.info-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border);
}

.info-card__title {
  display: flex; align-items: center; gap: 12px;
  font-size: 16px; font-weight: 700; color: var(--heading);
}

.info-card__title-icon {
  width: 36px; height: 36px;
  border-radius: 10px;
  background: var(--accent-light);
  display: flex; align-items: center; justify-content: center;
  color: var(--accent);
  font-size: 14px;
}

.btn-edit {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 22px;
  font-family: var(--font);
  font-size: 13px; font-weight: 600;
  color: var(--accent);
  background: var(--card);
  border: none;
  border-radius: 50px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(198,122,137,0.15);
  transition: var(--transition);
}
.btn-edit:hover {
  background: var(--accent); color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(198,122,137,0.35);
}

/* Info grid */
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
}

.info-item {
  padding: 20px 0;
  border-bottom: 1px solid var(--border);
}
.info-item:last-child,
.info-item:nth-last-child(2):not(.info-item--full) {
  border-bottom: none;
}
.info-item--full {
  grid-column: 1 / -1;
}

.info-item__label {
  display: flex; align-items: center; gap: 8px;
  font-size: 11px; font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.8px;
  color: var(--text-secondary);
  margin-bottom: 8px;
}
.info-item__label i {
  width: 28px; height: 28px;
  border-radius: 8px;
  background: var(--accent-light);
  display: flex; align-items: center; justify-content: center;
  color: var(--accent);
  font-size: 12px;
  flex-shrink: 0;
}

.info-item__value {
  font-size: 15px; font-weight: 600;
  color: var(--heading);
  word-break: break-word;
  padding-left: 36px;
}

/* ══════════════════════════════
   FLASH ALERT
   ══════════════════════════════ */
.alert {
  padding: 14px 20px;
  border-radius: var(--radius-sm);
  margin-bottom: 20px;
  font-size: 14px; font-weight: 500;
  display: flex; align-items: center; gap: 10px;
  animation: slideDown 0.35s ease both;
}
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.alert--success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.alert--danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* ══════════════════════════════
   EDIT POPUP MODAL
   ══════════════════════════════ */
.modal-overlay {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(30, 41, 59, 0.45);
  backdrop-filter: blur(6px);
  display: none; align-items: center; justify-content: center;
  padding: 20px;
}
.modal-overlay.active { display: flex; }

.modal-card {
  width: 100%; max-width: 520px;
  background: var(--card); border-radius: var(--radius);
  box-shadow: 0 25px 80px rgba(0,0,0,0.18);
  animation: modalIn 0.35s cubic-bezier(0.22, 1, 0.36, 1) both;
  overflow: hidden;
}
@keyframes modalIn {
  from { opacity: 0; transform: translateY(20px) scale(0.97); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 24px 28px 0;
}
.modal-header h2 {
  display: flex; align-items: center; gap: 10px;
  font-size: 18px; font-weight: 700; color: var(--heading);
}
.modal-header h2 i { color: var(--accent); font-size: 16px; }
.modal-close {
  width: 32px; height: 32px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: none; border: none; cursor: pointer;
  color: var(--text-secondary); font-size: 18px;
  transition: var(--transition);
}
.modal-close:hover { background: #f1f5f9; color: var(--heading); }

.modal-body { padding: 24px 28px 28px; }

.form-group { margin-bottom: 18px; }
.form-group label {
  display: block; font-size: 12px; font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.6px;
  color: var(--text-secondary); margin-bottom: 7px;
}
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="tel"] {
  width: 100%; padding: 12px 16px;
  font-family: var(--font); font-size: 14px; color: var(--heading);
  background: var(--bg); border: 1.5px solid var(--border);
  border-radius: var(--radius-sm); outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-group input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(198, 122, 137, 0.1);
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

/* Upload */
.upload-area {
  display: flex; align-items: center; gap: 16px;
  padding: 14px; background: var(--bg);
  border: 1.5px dashed var(--border); border-radius: var(--radius-sm);
  cursor: pointer; transition: var(--transition);
}
.upload-area:hover { border-color: var(--accent); background: var(--accent-light); }
.upload-area__icon {
  width: 42px; height: 42px; border-radius: 50%;
  background: var(--accent-light);
  display: flex; align-items: center; justify-content: center;
  color: var(--accent); font-size: 16px; flex-shrink: 0;
}
.upload-area__text { font-size: 13px; color: var(--text-secondary); line-height: 1.5; }
.upload-area__text strong { color: var(--accent); }

.modal-footer {
  display: flex; gap: 10px;
  padding: 0 28px 24px;
}
.btn-cancel {
  flex: 1; padding: 12px;
  font-family: var(--font); font-size: 14px; font-weight: 600;
  color: var(--text-secondary); background: transparent;
  border: 1.5px solid var(--border); border-radius: var(--radius-sm);
  cursor: pointer; transition: var(--transition);
}
.btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
.btn-save {
  flex: 1; padding: 12px;
  font-family: var(--font); font-size: 14px; font-weight: 600;
  color: #fff; background: var(--accent);
  border: none; border-radius: var(--radius-sm);
  cursor: pointer; transition: var(--transition);
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-save:hover {
  background: var(--accent-hover);
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(198,122,137,0.3);
}

/* ══════════════════════════════
   ANIMATIONS
   ══════════════════════════════ */
@keyframes floatSlow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}
@keyframes twinkle {
  0%, 100% { opacity: 0.15; transform: scale(1); }
  50% { opacity: 0.25; transform: scale(1.1); }
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-in {
  animation: fadeInUp 0.5s cubic-bezier(0.22,1,0.36,1) both;
}
.animate-in--delay1 { animation-delay: 0.08s; }
.animate-in--delay2 { animation-delay: 0.16s; }

/* ══════════════════════════════
   RESPONSIVE
   ══════════════════════════════ */
@media (max-width: 600px) {
  .topbar { padding: 0 16px; }
  .page-wrapper { padding: 20px 16px 60px; }
  .profile-hero__banner { height: 140px; }
  .profile-hero__content { padding: 0 20px 24px; }
  .profile-avatar { width: 84px; height: 84px; font-size: 28px; }
  .profile-avatar-wrap { margin-top: -42px; }
  .profile-name { font-size: 18px; }
  .profile-stats { gap: 20px; }
  .profile-stat__value { font-size: 16px; }
  .info-card { padding: 24px 20px; }
  .info-grid { grid-template-columns: 1fr; }
  .info-card__header { flex-direction: column; gap: 14px; align-items: flex-start; }
  .form-row { grid-template-columns: 1fr; }
  .modal-card { margin: 16px; }
  .modal-header, .modal-body, .modal-footer { padding-left: 20px; padding-right: 20px; }
}
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

<!-- ── Page Content ── -->
<div class="page-wrapper">

  <?php if ($flash): ?>
    <div class="alert alert--<?= e($flash['type'] === 'success' ? 'success' : 'danger') ?>">
      <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
      <?= e($flash['message']) ?>
    </div>
  <?php endif; ?>

  <!-- ══════════════════════════════
       COMBINED PROFILE CARD
       ══════════════════════════════ -->
  <div class="profile-hero animate-in" style="background: var(--card); overflow: visible; position: relative;">

    <!-- Action Buttons Top Right (Over Banner) -->
    <div style="position: absolute; right: 20px; top: 20px; z-index: 10;">
      <button type="button" class="btn-edit" id="btnEdit">
        <i class="fas fa-pen"></i> Edit Profil
      </button>
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
    <div class="profile-hero__content" style="padding-top: 0; padding-bottom: 24px; border: 1px solid var(--border); border-top: none; border-radius: 0 0 var(--radius) var(--radius); position: relative;">

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
      
      <div class="profile-stats">
        <a href="riwayat_pesanan.php" class="profile-stat" style="text-decoration:none;color:inherit;cursor:pointer;">
          <span class="profile-stat__value" id="statOrders">—</span>
          <span class="profile-stat__label">Pesanan</span>
        </a>
        <div class="profile-stat">
          <span class="profile-stat__value"><i class="fas fa-check-circle" style="color:#22c55e;font-size:16px;"></i></span>
          <span class="profile-stat__label">Terverifikasi</span>
        </div>
      </div>

      <!-- User Info Grid inside the same card -->
      <div style="margin-top: 24px; padding: 20px 24px; background: linear-gradient(135deg, #fdf4f6 0%, #faeef1 100%); border: 1px solid var(--border); border-radius: var(--radius-sm); text-align: left; position: relative; overflow: hidden; box-shadow: inset 0 2px 8px rgba(198,122,137,0.04);">
        
        <!-- Interesting Background Illustration (Full Right Side) -->
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; right: -20px; bottom: -20px; width: 220px; height: 220px; opacity: 0.4; pointer-events: none;">
            <circle cx="100" cy="100" r="80" fill="#f4dfe5" />
            <circle cx="100" cy="100" r="60" fill="var(--bg-white)" fill-opacity="0.5"/>
            <!-- Wave Abstract -->
            <path d="M20 120 Q 80 80 140 120 T 180 80 L 180 180 L 20 180 Z" fill="#ebcfd6"/>
            <!-- Simple Machine Shape -->
            <rect x="60" y="60" width="80" height="90" rx="10" fill="var(--bg-white)" stroke="#dca8b4" stroke-width="4"/>
            <circle cx="100" cy="110" r="25" fill="#f4dfe5" stroke="#dca8b4" stroke-width="3"/>
            <circle cx="100" cy="110" r="15" fill="var(--bg-white)"/>
            <rect x="75" y="75" width="50" height="10" rx="2" fill="#dca8b4"/>
            <circle cx="20" cy="60" r="10" fill="#f4dfe5"/>
            <circle cx="160" cy="40" r="15" fill="#f4dfe5"/>
            <path d="M 120 20 L 125 35 L 140 38 L 125 41 L 120 56 L 115 41 L 100 38 L 115 35 Z" fill="#fbbf24" opacity="0.8"/>
            <path d="M 40 150 L 42 160 L 52 162 L 42 164 L 40 174 L 38 164 L 28 162 L 38 160 Z" fill="#fbbf24" opacity="0.6"/>
        </svg>

        <h3 style="position: relative; z-index: 2; font-size: 13px; font-weight: 800; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <span style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(198,122,137,0.15); border-radius: 8px;">
                <i class="fas fa-address-book" style="color: var(--accent); font-size: 12px;"></i>
            </span>
            Informasi Kontak
        </h3>

        <div style="position: relative; z-index: 2; display: flex; gap: 24px; align-items: center; justify-content: space-between;">
            <!-- Left Info Area -->
            <div class="info-grid" style="flex: 1; display: grid; grid-template-columns: 1fr; gap: 12px;">
              <div class="info-item" style="padding: 12px; background: rgba(255,255,255,0.7); border: 1px solid rgba(198,122,137,0.15); border-radius: 12px; backdrop-filter: blur(4px);">
                <div class="info-item__label" style="margin-bottom: 4px; color: var(--text-secondary);"><i class="fas fa-envelope" style="color: var(--accent); background: none; width: auto; height: auto;"></i> Email</div>
                <div class="info-item__value" style="font-weight: 600; font-size: 14px; color: var(--heading); padding-left: 24px;"><?= e($profile['email']) ?></div>
              </div>
              <div class="info-item" style="padding: 12px; background: rgba(255,255,255,0.7); border: 1px solid rgba(198,122,137,0.15); border-radius: 12px; backdrop-filter: blur(4px);">
                <div class="info-item__label" style="margin-bottom: 4px; color: var(--text-secondary);"><i class="fas fa-phone" style="color: var(--accent); background: none; width: auto; height: auto;"></i> No. HP</div>
                <div class="info-item__value" style="font-weight: 600; font-size: 14px; color: var(--heading); padding-left: 24px;"><?= e($profile['no_hp']) ?: '-' ?></div>
              </div>
            </div>

            <!-- Empty Right padding for visual balance with the background illustration -->
            <div style="flex-shrink: 0; width: 140px;"></div>
        </div>
      </div>

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

<script>
// Modal functionality
const modal = document.getElementById('editModal');
const btnEdit = document.getElementById('btnEdit');
const btnClose = document.getElementById('modalClose');
const btnCancel = document.getElementById('modalCancel');

function openModal() { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeModal() { modal.classList.remove('active'); document.body.style.overflow = ''; }

btnEdit.addEventListener('click', openModal);
btnClose.addEventListener('click', closeModal);
btnCancel.addEventListener('click', closeModal);
modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

// File name display
document.getElementById('edit_foto').addEventListener('change', function() {
  const name = this.files[0]?.name || '';
  if (name) {
    document.getElementById('uploadText').innerHTML = '<strong>' + name + '</strong><br>Siap diupload';
  }
});

// Fetch order count for stats (optional, graceful fail)
(function() {
  fetch('dashboard/api_count_orders.php')
    .then(r => r.json())
    .then(data => {
      if (data && data.count !== undefined) {
        document.getElementById('statOrders').textContent = data.count;
      }
    })
    .catch(() => {
      document.getElementById('statOrders').textContent = '0';
    });
})();
</script>
</body>
</html>
