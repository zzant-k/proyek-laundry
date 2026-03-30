<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Consolidated Login & Register
 *  Based on rumah_laundry_v3.html UI
 * ═══════════════════════════════════════════════════════
 */

require_once 'dashboard/config.php';

// If already logged in, redirect based on role
if (isset($_SESSION['id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';
$success = '';

// Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = 'Email dan password wajib diisi';
        } else {
            $stmt = $conn->prepare("SELECT iduser, nama, no_hp, email, password, role FROM user WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['id'] = $user['iduser'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['no_hp'] = $user['no_hp'] ?? '';
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    if ($user['role'] === 'admin') {
                        header('Location: dashboard/dashboard.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit;
                } else {
                    $error = 'Password salah!';
                }
            } else {
                $error = 'Email tidak ditemukan!';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['register'])) {
        $nama = trim($_POST['nama']);
        $no_hp = trim($_POST['no_hp']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($nama) || empty($email) || empty($password)) {
            $error = 'Semua field wajib diisi';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter';
        } else {
            // Check duplicate email
            $stmt = $conn->prepare("SELECT iduser FROM user WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Email sudah terdaftar. Silakan login.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt_reg = $conn->prepare("INSERT INTO user (nama, no_hp, email, password, role) VALUES (?, ?, ?, ?, 'user')");
                $stmt_reg->bind_param('ssss', $nama, $no_hp, $email, $hashedPassword);
                
                if ($stmt_reg->execute()) {
                    $success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $error = 'Gagal mendaftar. Coba lagi.';
                }
                $stmt_reg->close();
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Rumah Laundry — Login & Register</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<style>
/* CSS copied from rumah_laundry_v3.html */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --p1: #4C1208; --p2: #7B1D26; --ac: #893A49; --sf: #CA99AB;
  --bg: #EDD8E5; --bg2: #F5ECF2; --white: #FFFCFE;
  --text-h: #2E0A08; --text-b: #5A2535; --text-s: #9B687A; --text-m: #C4A0AF;
  --inp-bg: rgba(255,255,255,0.72); --inp-bd: rgba(137,58,73,0.18);
  --radius: 32px; --radius-sm: 16px; --radius-xs: 12px;
  --f-serif: 'DM Serif Display', Georgia, serif;
  --f-sans: 'Plus Jakarta Sans', system-ui, sans-serif;
  --ease: cubic-bezier(0.22,1,0.36,1);
  --shadow: 0 40px 100px rgba(76,18,8,0.16), 0 8px 32px rgba(76,18,8,0.08);
  --shadow-btn: 0 5px 15px rgba(76,18,8,0.25);
}
html, body { min-height: 100%; font-family: var(--f-sans); background: var(--bg); overflow-x: hidden; }
body::before {
  content: ''; position: fixed; inset: 0; z-index: 0;
  background: radial-gradient(ellipse 80% 70% at 5% 0%, rgba(202,153,171,0.5) 0%, transparent 60%),
              radial-gradient(ellipse 60% 60% at 95% 100%, rgba(123,29,38,0.14) 0%, transparent 60%),
              radial-gradient(ellipse 40% 40% at 50% 50%, rgba(237,216,229,0.6) 0%, transparent 70%), var(--bg);
}
.scene { position: relative; z-index: 1; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 28px 20px; }
.card {
  width: 100%; max-width: 880px; min-height: 563px; display: grid; grid-template-columns: 50% 50%;
  background: rgba(255,252,254,0.62); backdrop-filter: blur(32px) saturate(150%); -webkit-backdrop-filter: blur(32px) saturate(150%);
  border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid rgba(255,255,255,0.8); overflow: hidden;
  animation: rise 0.7s var(--ease) both;
}
@keyframes rise { from { opacity: 0; transform: translateY(22px) scale(0.975); } to { opacity: 1; transform: translateY(0) scale(1); } }
.form-section { display: flex; flex-direction: column; padding: 40px 44px 36px; background: rgba(255,252,254,0.55); border-right: 1px solid rgba(202,153,171,0.2); position: relative; }
.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; margin-bottom: 32px; }
.logo-mark { width: 44px; height: 44px; background: linear-gradient(150deg, var(--p2) 0%, var(--p1) 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 18px rgba(76,18,8,0.28), inset 0 1px 0 rgba(255,255,255,0.15); flex-shrink: 0; }
.logo-mark svg { width: 22px; height: 22px; }
.logo-label { display: flex; flex-direction: column; gap: 1px; }
.logo-name { font-family: var(--f-serif); font-size: 18px; color: var(--p1); line-height: 1; letter-spacing: 0.01em; }
.logo-sub { font-family: var(--f-sans); font-size: 9px; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase; color: var(--text-s); }
.forms-wrap { position: relative; flex: 1; overflow: hidden; }
.form-panel { position: absolute; inset: 0; display: flex; flex-direction: column; transition: opacity 0.42s var(--ease), transform 0.42s var(--ease); will-change: transform, opacity; overflow-y: auto; padding-right: 2px; }
.form-panel::-webkit-scrollbar { display: none; }
#panelLogin  { opacity: 1; transform: translateX(0); pointer-events: all; }
#panelRegister { opacity: 0; transform: translateX(36px); pointer-events: none; }
.card.reg #panelLogin { opacity: 0; transform: translateX(-36px); pointer-events: none; }
.card.reg #panelRegister { opacity: 1; transform: translateX(0); pointer-events: all; }
.form-eyebrow { font-family: var(--f-sans); font-size: 10.5px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sf); margin-bottom: 10px; display: flex; align-items: center; gap: 7px; }
.form-eyebrow::before { content: ''; display: block; width: 18px; height: 1.5px; background: var(--sf); border-radius: 2px; }
.form-heading { font-family: var(--f-serif); font-size: 38px; color: var(--text-h); line-height: 1.08; letter-spacing: -0.01em; margin-bottom: 8px; }
.form-heading em { font-style: italic; color: var(--ac); }
.form-sub { font-size: 13.5px; font-weight: 400; color: var(--text-s); line-height: 1.6; margin-bottom: 30px; }
.field { margin-bottom: 14px; }
.field label { display: block; font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-b); margin-bottom: 7px; opacity: 0.75; }
.inp-wrap { position: relative; }
.inp-wrap .ic { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--text-m); pointer-events: none; transition: color 0.22s; }
.inp-wrap input { width: 100%; background: var(--inp-bg); border: 1.5px solid var(--inp-bd); border-radius: var(--radius-xs); padding: 13.5px 14px 13.5px 42px; font-family: var(--f-sans); font-size: 13.5px; font-weight: 400; color: var(--text-h); outline: none; transition: border-color 0.22s, box-shadow 0.22s, background 0.22s; -webkit-appearance: none; }
.inp-wrap input::placeholder { color: var(--text-m); font-size: 13px; }
.inp-wrap input:focus { border-color: var(--ac); background: rgba(255,255,255,0.92); box-shadow: 0 0 0 4px rgba(137,58,73,0.1); }
.inp-wrap:focus-within .ic { color: var(--ac); }
.pw-btn { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-m); padding: 4px; display: flex; align-items: center; transition: color 0.2s; }
.pw-btn:hover { color: var(--ac); }
.pw-btn svg { width: 15px; height: 15px; }
.btn { width: 100%; height: 52px; background: var(--p1); color: #fff; border: none; border-radius: var(--radius-xs); font-family: var(--f-serif); font-size: 18px; letter-spacing: 0.02em; cursor: pointer; position: relative; overflow: hidden; transition: transform 0.22s var(--ease), box-shadow 0.22s; box-shadow: var(--shadow-btn); margin-bottom: 16px; margin-top: 10px; }
.btn::before { content: ''; position: absolute; inset: 0; background: none; pointer-events: none; }
.btn:hover { transform: translateY(-1.5px); box-shadow: 0 8px 20px rgba(76,18,8,0.3); }
.btn:active { transform: translateY(0); }
.sw { text-align: center; font-size: 13px; color: var(--text-s); margin-top: auto; padding-top: 6px; }
.sw button { background: none; border: none; cursor: pointer; font-family: var(--f-sans); font-size: 13px; font-weight: 700; color: var(--p2); margin-left: 4px; text-decoration: underline; text-underline-offset: 3px; text-decoration-color: rgba(123,29,38,0.3); transition: color 0.2s; }
.sw button:hover { color: var(--p1); }
.img-section { position: relative; overflow: hidden; }
.illus-wrap { width: 100%; height: 100%; display: block; position: absolute; inset: 0; }
.illus-wrap svg { width: 100%; height: 100%; }
.img-section::after { content: ''; position: absolute; inset: 0; background: linear-gradient(185deg, rgba(76,18,8,0.62) 0%, rgba(123,29,38,0.28) 45%, rgba(228,205,221,0.18) 100% ); pointer-events: none; }
.img-body { position: absolute; inset: 0; z-index: 2; display: flex; flex-direction: column; padding: 36px 34px 40px; }
.img-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.1); backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,0.2); border-radius: 100px; padding: 5px 14px; font-size: 10.5px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(255,255,255,0.85); width: fit-content; }
.pulse { width: 6px; height: 6px; border-radius: 50%; background: #fda4af; box-shadow: 0 0 8px #fda4af; animation: pulse 2.8s ease-in-out infinite; flex-shrink: 0; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(0.75)} }
.alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
.alert-error { background: rgba(220, 38, 38, 0.1); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.2); }
.alert-success { background: rgba(22, 163, 74, 0.1); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.2); }
@media (max-width: 800px) { 
  .card { grid-template-columns: 1fr; min-height: auto; } 
  .img-section { height: 180px; order: -1; } 
  .form-section { padding: 24px 28px 40px; border-right: none; } 
  .img-body { padding: 18px 22px; } 
  .forms-wrap { height: auto; min-height: 400px; display: block; overflow: visible; }
  .form-panel { 
    position: relative; 
    opacity: 1 !important; 
    transform: none !important; 
    pointer-events: all !important;
    display: none;
    padding-right: 0;
  }
  #panelLogin { display: flex; }
  .card.reg #panelLogin { display: none; }
  .card.reg #panelRegister { display: flex; }
  .form-heading { font-size: 32px; }
}
@media (max-width: 500px) { 
  .scene { padding: 12px; } 
  .form-section { padding: 24px 20px 36px; } 
  .logo { margin-bottom: 24px; } 
  .form-heading { font-size: 28px; } 
  .img-section { height: 160px; }
  .forms-wrap { min-height: 380px; }
}
</style>
</head>
<body>
<div class="scene">
<div class="card <?php echo isset($_POST['register']) ? 'reg' : ''; ?>" id="card">
  <div class="form-section">

    <div class="forms-wrap">
      <!-- Alerts -->
      <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php endif; ?>

      <!-- LOGIN -->
      <div class="form-panel" id="panelLogin">
        <p class="form-eyebrow">Selamat datang</p>
        <h1 class="form-heading">Masuk ke <br>Akun Anda</h1>
        <p class="form-sub">Senang bertemu lagi. Silakan masuk untuk melanjutkan.</p>

        <form method="POST" action="">
          <div class="field">
            <label>Email</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="2" y="5" width="16" height="12" rx="2.5"/>
                <path d="M2 8l8 5 8-5"/>
              </svg>
              <input type="email" name="email" placeholder="nama@email.com" required autocomplete="email" value="<?php echo isset($_POST['email']) && isset($_POST['login']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
            </div>
          </div>
          <div class="field">
            <label>Password</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="5" y="9" width="10" height="8" rx="2"/>
                <path d="M7 9V7a3 3 0 016 0v2"/>
                <circle cx="10" cy="13.5" r="1.2" fill="currentColor" stroke="none"/>
              </svg>
              <input type="password" name="password" id="lPw" placeholder="Masukkan password" required/>
              <button type="button" class="pw-btn" onclick="tPw('lPw')">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                  <path d="M2 10s3-5.5 8-5.5S18 10 18 10s-3 5.5-8 5.5S2 10 2 10z"/>
                  <circle cx="10" cy="10" r="2.2"/>
                </svg>
              </button>
            </div>
          </div>
          <button type="submit" name="login" class="btn">Masuk</button>
          <div class="sw">
            Belum punya akun?
            <button type="button" onclick="go('register')">Daftar sekarang</button>
          </div>
        </form>
      </div>

      <!-- REGISTER -->
      <div class="form-panel" id="panelRegister">
        <h1 class="form-heading">Buat Akun Baru</h1>
        <p class="form-sub">Daftar gratis dan nikmati layanan laundry premium.</p>

        <form method="POST" action="">
          <div class="field">
              <label>Nama Lengkap</label>
              <div class="inp-wrap">
                <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                  <circle cx="10" cy="6.5" r="3.5"/>
                  <path d="M3 18c0-3.87 3.13-7 7-7s7 3.13 7 7"/>
                </svg>
                <input type="text" name="nama" placeholder="Nama lengkap Anda" required value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"/>
              </div>
            </div>
          <div class="field">
            <label>Nomor HP</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="5" y="2" width="10" height="16" rx="2.5"/>
                <circle cx="10" cy="15.5" r="0.8" fill="currentColor" stroke="none"/>
              </svg>
              <input type="tel" name="no_hp" placeholder="+62 812-3456-7890" value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>"/>
            </div>
          </div>
          <div class="field">
            <label>Email</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="2" y="5" width="16" height="12" rx="2.5"/>
                <path d="M2 8l8 5 8-5"/>
              </svg>
              <input type="email" name="email" placeholder="nama@email.com" required value="<?php echo isset($_POST['email']) && isset($_POST['register']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
            </div>
          </div>
          <div class="field">
            <label>Password</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="5" y="9" width="10" height="8" rx="2"/>
                <path d="M7 9V7a3 3 0 016 0v2"/>
                <circle cx="10" cy="13.5" r="1.2" fill="currentColor" stroke="none"/>
              </svg>
              <input type="password" name="password" id="rPw" placeholder="Min. 6 karakter" required/>
              <button type="button" class="pw-btn" onclick="tPw('rPw')">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                  <path d="M2 10s3-5.5 8-5.5S18 10 18 10s-3 5.5-8 5.5S2 10 2 10z"/>
                  <circle cx="10" cy="10" r="2.2"/>
                </svg>
              </button>
            </div>
          </div>
          <button type="submit" name="register" class="btn">Daftar Sekarang</button>
          <div class="sw">
            Sudah punya akun?
            <button type="button" onclick="go('login')">Masuk di sini</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="img-section">
    <div class="illus-wrap">
      <svg viewBox="0 0 480 640" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
        <defs>
          <!-- Room background gradient -->
          <linearGradient id="roomBg" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#7B2535"/>
            <stop offset="100%" stop-color="#4C1208"/>
          </linearGradient>
          <!-- Floor gradient -->
          <linearGradient id="floorG" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#3a0d07"/>
            <stop offset="100%" stop-color="#2a0805"/>
          </linearGradient>
          <!-- Machine body gradient -->
          <linearGradient id="machineG" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f9f0f4"/>
            <stop offset="100%" stop-color="#e8d5de"/>
          </linearGradient>
          <!-- Machine front -->
          <linearGradient id="machineFront" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#f5e8ee"/>
            <stop offset="100%" stop-color="#dfc5d0"/>
          </linearGradient>
          <!-- Drum gradient -->
          <linearGradient id="drumG" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#c9a0b0"/>
            <stop offset="100%" stop-color="#8a4a5a"/>
          </linearGradient>
          <!-- Drum glass -->
          <radialGradient id="drumGlass" cx="40%" cy="35%" r="65%">
            <stop offset="0%" stop-color="rgba(255,220,235,0.45)"/>
            <stop offset="60%" stop-color="rgba(180,100,130,0.15)"/>
            <stop offset="100%" stop-color="rgba(100,30,50,0.3)"/>
          </radialGradient>
          <!-- Water inside drum -->
          <radialGradient id="waterG" cx="50%" cy="60%" r="55%">
            <stop offset="0%" stop-color="rgba(200,160,180,0.5)"/>
            <stop offset="100%" stop-color="rgba(120,50,80,0.3)"/>
          </radialGradient>
          <!-- Shelf gradient -->
          <linearGradient id="shelfG" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f5e8ee"/>
            <stop offset="100%" stop-color="#e0cad5"/>
          </linearGradient>
          <!-- Basket -->
          <linearGradient id="basketG" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#e8d2dc"/>
            <stop offset="100%" stop-color="#c9a5b5"/>
          </linearGradient>
          <!-- Window light -->
          <radialGradient id="winLight" cx="50%" cy="30%" r="70%">
            <stop offset="0%" stop-color="rgba(255,220,200,0.9)"/>
            <stop offset="100%" stop-color="rgba(255,180,160,0.1)"/>
          </radialGradient>
          <!-- Bubble gradient -->
          <radialGradient id="bubbleG" cx="35%" cy="30%" r="65%">
            <stop offset="0%" stop-color="rgba(255,255,255,0.7)"/>
            <stop offset="100%" stop-color="rgba(255,200,220,0.15)"/>
          </radialGradient>
          <!-- Shirt gradient -->
          <linearGradient id="shirtG" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#f472b6"/>
            <stop offset="100%" stop-color="#be185d"/>
          </linearGradient>
          <!-- Detergent -->
          <linearGradient id="detG" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#fce7f0"/>
            <stop offset="100%" stop-color="#f9a8d4"/>
          </linearGradient>
          <clipPath id="drumClip">
            <circle cx="240" cy="370" r="72"/>
          </clipPath>
          <filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="6" stdDeviation="10" flood-color="rgba(76,18,8,0.3)"/>
          </filter>
          <filter id="glow">
            <feGaussianBlur stdDeviation="3" result="blur"/>
            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
          </filter>
        </defs>

        <!-- ── ROOM BACKGROUND ── -->
        <rect width="480" height="640" fill="url(#roomBg)"/>

        <!-- Wall texture subtle lines -->
        <line x1="0" y1="120" x2="480" y2="120" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="0" y1="240" x2="480" y2="240" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
        <line x1="160" y1="0" x2="160" y2="420" stroke="rgba(255,255,255,0.025)" stroke-width="1"/>
        <line x1="320" y1="0" x2="320" y2="420" stroke="rgba(255,255,255,0.025)" stroke-width="1"/>

        <!-- ── WINDOW top right ── -->
        <rect x="310" y="28" width="130" height="110" rx="10" fill="rgba(255,220,200,0.08)" stroke="rgba(255,255,255,0.12)" stroke-width="1.5"/>
        <!-- Window frame inner -->
        <rect x="318" y="36" width="114" height="94" rx="6" fill="url(#winLight)" opacity="0.45"/>
        <!-- Window cross -->
        <line x1="375" y1="36" x2="375" y2="130" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
        <line x1="318" y1="83" x2="432" y2="83" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
        <!-- Light beam -->
        <path d="M318,36 L240,420 L432,420 L432,36Z" fill="rgba(255,220,200,0.03)"/>

        <!-- ── FLOOR ── -->
        <rect x="0" y="530" width="480" height="110" fill="url(#floorG)"/>
        <!-- Floor line -->
        <line x1="0" y1="530" x2="480" y2="530" stroke="rgba(202,153,171,0.25)" stroke-width="1.5"/>
        <!-- Floor tile lines -->
        <line x1="120" y1="530" x2="120" y2="640" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="240" y1="530" x2="240" y2="640" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="360" y1="530" x2="360" y2="640" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="0" y1="580" x2="480" y2="580" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="0" y1="615" x2="480" y2="615" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>

        <!-- ── SHELF on wall ── -->
        <rect x="28" y="145" width="180" height="14" rx="7" fill="url(#shelfG)" filter="url(#softShadow)"/>
        <!-- Shelf bracket left -->
        <path d="M45,159 L45,175 L38,175" stroke="rgba(229,193,210,0.6)" stroke-width="2" stroke-linecap="round" fill="none"/>
        <!-- Shelf bracket right -->
        <path d="M190,159 L190,175 L197,175" stroke="rgba(229,193,210,0.6)" stroke-width="2" stroke-linecap="round" fill="none"/>

        <!-- Items on shelf -->
        <!-- Detergent bottle -->
        <rect x="42" y="100" width="32" height="46" rx="8" fill="url(#detG)" filter="url(#softShadow)"/>
        <rect x="50" y="93" width="16" height="10" rx="4" fill="#fce7f0"/>
        <rect x="54" y="87" width="8" height="8" rx="3" fill="#f9a8d4"/>
        <!-- Label on detergent -->
        <rect x="46" y="112" width="24" height="16" rx="4" fill="rgba(190,24,93,0.2)"/>
        <text x="58" y="123" text-anchor="middle" font-size="5" fill="rgba(190,24,93,0.9)" font-family="Plus Jakarta Sans, sans-serif" font-weight="700">WASH</text>
        <!-- Bubbles from detergent -->
        <circle cx="38" cy="90" r="5" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.4)" stroke-width="0.8"/>
        <circle cx="30" cy="79" r="3.5" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.3)" stroke-width="0.7"/>
        <circle cx="44" cy="74" r="2.5" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.3)" stroke-width="0.6"/>

        <!-- Folded towels on shelf -->
        <rect x="88" y="119" width="44" height="27" rx="5" fill="#e8a0b8" opacity="0.85"/>
        <rect x="88" y="119" width="44" height="8" rx="5" fill="#f0b8cc" opacity="0.9"/>
        <rect x="92" y="108" width="36" height="27" rx="5" fill="#d4788e" opacity="0.8"/>
        <rect x="92" y="108" width="36" height="8" rx="5" fill="#e090a8" opacity="0.85"/>
        <!-- Towel stripes -->
        <line x1="88" y1="127" x2="132" y2="127" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
        <line x1="92" y1="116" x2="128" y2="116" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>

        <!-- Small plant -->
        <rect x="160" y="121" width="24" height="24" rx="6" fill="#7B2535" opacity="0.6"/>
        <ellipse cx="172" cy="110" rx="14" ry="18" fill="#3a7a3a" opacity="0.7"/>
        <ellipse cx="163" cy="115" rx="10" ry="14" fill="#2e6b2e" opacity="0.7"/>
        <ellipse cx="181" cy="118" rx="9" ry="12" fill="#4a8a4a" opacity="0.6"/>
        <!-- Plant highlight -->
        <ellipse cx="168" cy="105" rx="4" ry="6" fill="rgba(100,200,100,0.3)"/>

        <!-- ── WASHING MACHINE ── -->
        <!-- Machine body shadow -->
        <ellipse cx="240" cy="545" rx="105" ry="12" fill="rgba(0,0,0,0.25)"/>

        <!-- Machine back panel -->
        <rect x="112" y="270" width="256" height="270" rx="18" fill="url(#machineG)" filter="url(#softShadow)"/>

        <!-- Machine front face -->
        <rect x="118" y="278" width="244" height="255" rx="14" fill="url(#machineFront)"/>

        <!-- Top control strip -->
        <rect x="112" y="270" width="256" height="52" rx="18" fill="#f0e2ea"/>
        <rect x="112" y="298" width="256" height="24" fill="#f0e2ea"/>

        <!-- Control knob left -->
        <circle cx="162" cy="296" r="18" fill="#e0cad5" stroke="rgba(137,58,73,0.2)" stroke-width="1.5"/>
        <circle cx="162" cy="296" r="12" fill="white" stroke="rgba(137,58,73,0.15)" stroke-width="1"/>
        <circle cx="162" cy="284" r="3" fill="#be185d" opacity="0.8"/>
        <!-- Knob marks -->
        <line x1="162" y1="278" x2="162" y2="282" stroke="rgba(137,58,73,0.4)" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="174" y1="284" x2="171" y2="287" stroke="rgba(137,58,73,0.3)" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="150" y1="284" x2="153" y2="287" stroke="rgba(137,58,73,0.3)" stroke-width="1.2" stroke-linecap="round"/>

        <!-- Digital display -->
        <rect x="190" y="279" width="100" height="34" rx="8" fill="#2a0808" opacity="0.85"/>
        <rect x="194" y="283" width="92" height="26" rx="5" fill="#1a0404"/>
        <!-- Display text -->
        <text x="240" y="300" text-anchor="middle" font-size="11" fill="#f472b6" font-family="monospace" font-weight="700" opacity="0.9">30:00</text>
        <circle cx="205" cy="291" r="2" fill="#22c55e" opacity="0.8">
          <animate attributeName="opacity" values="0.8;0.2;0.8" dur="1.5s" repeatCount="indefinite"/>
        </circle>

        <!-- Control knob right -->
        <circle cx="318" cy="296" r="18" fill="#e0cad5" stroke="rgba(137,58,73,0.2)" stroke-width="1.5"/>
        <circle cx="318" cy="296" r="12" fill="white" stroke="rgba(137,58,73,0.15)" stroke-width="1"/>
        <text x="318" y="300" text-anchor="middle" font-size="8" fill="#893A49" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">60°</text>

        <!-- ── DRUM ── -->
        <!-- Drum outer ring shadow -->
        <circle cx="240" cy="370" r="82" fill="rgba(76,18,8,0.15)"/>
        <!-- Drum outer ring -->
        <circle cx="240" cy="370" r="78" fill="#d4b0be" stroke="rgba(137,58,73,0.3)" stroke-width="2"/>
        <!-- Drum ring detail -->
        <circle cx="240" cy="370" r="74" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
        <!-- Drum main -->
        <circle cx="240" cy="370" r="72" fill="url(#drumG)"/>
        <!-- Water/clothes inside -->
        <circle cx="240" cy="370" r="72" fill="url(#waterG)"/>

        <!-- Clothes tumbling inside drum -->
        <g clip-path="url(#drumClip)">
          <!-- Pink shirt -->
          <path d="M190,345 Q182,338 175,342 Q170,346 173,356 Q176,364 183,360 L185,405 L225,405 L225,360 Q232,364 235,356 Q238,346 233,342 Q226,338 218,345 Q213,337 204,336 Q195,337 190,345Z" fill="url(#shirtG)" opacity="0.85"/>
          <!-- Collar -->
          <path d="M190,345 Q200,355 204,355 Q208,355 218,345" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2.5" stroke-linecap="round"/>

          <!-- White shirt partial -->
          <path d="M255,390 Q250,384 246,386 Q243,389 244,396 L245,420 L285,420 L286,396 Q287,389 284,386 Q280,384 275,390 Q270,385 265,385 Q258,385 255,390Z" fill="rgba(255,255,255,0.75)"/>

          <!-- Dark fabric bottom -->
          <ellipse cx="220" cy="415" rx="35" ry="15" fill="rgba(100,20,50,0.5)"/>
          <ellipse cx="265" cy="420" rx="25" ry="10" fill="rgba(80,10,40,0.4)"/>

          <!-- Water swirl -->
          <path d="M180,380 Q195,360 220,368 Q245,376 250,358 Q255,342 270,350 Q285,358 280,375 Q275,390 258,385" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="3" stroke-linecap="round"/>

          <!-- Foam/bubbles inside -->
          <circle cx="205" cy="400" r="6" fill="rgba(255,255,255,0.35)" stroke="rgba(255,255,255,0.5)" stroke-width="0.8"/>
          <circle cx="272" cy="395" r="4" fill="rgba(255,255,255,0.3)" stroke="rgba(255,255,255,0.4)" stroke-width="0.7"/>
          <circle cx="255" cy="410" r="3" fill="rgba(255,255,255,0.25)"/>
          <circle cx="218" cy="408" r="2.5" fill="rgba(255,255,255,0.3)"/>
        </g>

        <!-- Drum glass sheen -->
        <circle cx="240" cy="370" r="72" fill="url(#drumGlass)"/>
        <!-- Drum outer ring stroke -->
        <circle cx="240" cy="370" r="72" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="2"/>

        <!-- Drum bolt holes -->
        <circle cx="176" cy="370" r="5" fill="#d4b0be" stroke="rgba(137,58,73,0.3)" stroke-width="1"/>
        <circle cx="240" cy="306" r="5" fill="#d4b0be" stroke="rgba(137,58,73,0.3)" stroke-width="1"/>
        <circle cx="304" cy="370" r="5" fill="#d4b0be" stroke="rgba(137,58,73,0.3)" stroke-width="1"/>
        <circle cx="240" cy="434" r="5" fill="#d4b0be" stroke="rgba(137,58,73,0.3)" stroke-width="1"/>

        <!-- Machine bottom strip -->
        <rect x="118" y="460" width="244" height="58" rx="0" fill="#e8d8e2"/>
        <rect x="118" y="508" width="244" height="10" rx="0" fill="#dccad4"/>
        <!-- Bottom panel line -->
        <line x1="118" y1="460" x2="362" y2="460" stroke="rgba(137,58,73,0.15)" stroke-width="1"/>

        <!-- Door handle -->
        <rect x="215" y="462" width="50" height="8" rx="4" fill="rgba(137,58,73,0.25)" stroke="rgba(137,58,73,0.2)" stroke-width="1"/>

        <!-- Filter cap bottom left -->
        <circle cx="148" cy="490" r="12" fill="#e0cad5" stroke="rgba(137,58,73,0.2)" stroke-width="1.5"/>
        <circle cx="148" cy="490" r="7" fill="white"/>
        <line x1="145" y1="490" x2="151" y2="490" stroke="rgba(137,58,73,0.5)" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="148" y1="487" x2="148" y2="493" stroke="rgba(137,58,73,0.5)" stroke-width="1.5" stroke-linecap="round"/>

        <!-- USB/power indicator -->
        <circle cx="340" cy="485" r="6" fill="#22c55e" opacity="0.7" filter="url(#glow)">
          <animate attributeName="opacity" values="0.7;0.3;0.7" dur="2s" repeatCount="indefinite"/>
        </circle>
        <circle cx="325" cy="485" r="6" fill="#f59e0b" opacity="0.5"/>

        <!-- Machine feet -->
        <rect x="140" y="528" width="30" height="14" rx="7" fill="#c8aab8"/>
        <rect x="310" y="528" width="30" height="14" rx="7" fill="#c8aab8"/>

        <!-- ── LAUNDRY BASKET right side ── -->
        <g filter="url(#softShadow)">
          <!-- Basket body -->
          <path d="M370,430 Q368,510 378,530 L440,530 Q450,510 448,430 Z" fill="url(#basketG)"/>
          <!-- Basket weave lines horizontal -->
          <path d="M370,450 Q409,455 448,450" fill="none" stroke="rgba(137,58,73,0.2)" stroke-width="1.5"/>
          <path d="M370,470 Q409,475 448,470" fill="none" stroke="rgba(137,58,73,0.2)" stroke-width="1.5"/>
          <path d="M371,490 Q409,495 447,490" fill="none" stroke="rgba(137,58,73,0.2)" stroke-width="1.5"/>
          <path d="M372,510 Q409,514 446,510" fill="none" stroke="rgba(137,58,73,0.2)" stroke-width="1.5"/>
          <!-- Basket weave vertical -->
          <line x1="389" y1="430" x2="384" y2="530" stroke="rgba(137,58,73,0.15)" stroke-width="1"/>
          <line x1="409" y1="430" x2="409" y2="530" stroke="rgba(137,58,73,0.15)" stroke-width="1"/>
          <line x1="429" y1="430" x2="433" y2="530" stroke="rgba(137,58,73,0.15)" stroke-width="1"/>
          <!-- Basket rim -->
          <path d="M365,430 Q365,422 409,420 Q453,422 453,430 Q453,438 409,440 Q365,438 365,430Z" fill="#dcc0cc"/>
        </g>

        <!-- Clothes hanging out of basket -->
        <!-- Pink towel draping -->
        <path d="M368,425 Q350,440 345,465 Q342,480 350,490" fill="none" stroke="#f9a8d4" stroke-width="8" stroke-linecap="round" opacity="0.8"/>
        <!-- White sock over basket rim -->
        <path d="M415,422 Q420,405 428,400 Q438,396 440,408 Q442,418 435,422" fill="rgba(255,255,255,0.8)" stroke="rgba(200,160,180,0.5)" stroke-width="1"/>

        <!-- ── HANGING CLOTHES on left side ── -->
        <!-- Hanging rod -->
        <rect x="28" y="200" width="6" height="115" rx="3" fill="rgba(229,193,210,0.5)"/>
        <circle cx="31" cy="200" r="5" fill="rgba(229,193,210,0.6)"/>

        <!-- Hanger 1 - Pink dress -->
        <path d="M31,210 Q55,210 60,215 Q65,220 60,225 L42,260 Q40,270 42,280 L60,320 Q62,326 58,328 L28,328 Q24,326 26,320 L44,280 Q46,270 44,260 L26,225 Q21,220 26,215 Q31,210 31,210Z" fill="#f472b6" opacity="0.8"/>
        <!-- Dress collar -->
        <path d="M37,226 Q44,240 51,226" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linecap="round"/>
        <!-- Hanger wire -->
        <path d="M31,210 Q50,195 52,210" fill="none" stroke="rgba(229,193,210,0.7)" stroke-width="2" stroke-linecap="round"/>
        <circle cx="50" cy="198" r="3" fill="none" stroke="rgba(229,193,210,0.7)" stroke-width="2"/>

        <!-- Hanger 2 - White shirt (behind) -->
        <g opacity="0.6" transform="translate(4,12)">
          <path d="M31,220 Q53,220 56,224 Q60,228 56,232 L40,265 Q38,273 40,282 L55,315 Q57,320 54,322 L22,322 Q19,320 21,315 L36,282 Q38,273 36,265 L20,232 Q16,228 20,224 Q25,220 31,220Z" fill="rgba(255,255,255,0.75)"/>
          <path d="M31,220 Q48,206 50,220" fill="none" stroke="rgba(229,193,210,0.5)" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="48" cy="209" r="2.5" fill="none" stroke="rgba(229,193,210,0.5)" stroke-width="1.8"/>
        </g>

        <!-- ── FLOATING BUBBLES ── -->
        <!-- Large bubble -->
        <circle cx="95" cy="235" r="16" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.35)" stroke-width="1.2">
          <animateTransform attributeName="transform" type="translate" values="0,0;3,-18;-2,-35;0,-50" dur="4s" repeatCount="indefinite" keyTimes="0;0.3;0.7;1"/>
          <animate attributeName="opacity" values="0.9;0.8;0.5;0" dur="4s" repeatCount="indefinite"/>
        </circle>
        <circle cx="88" cy="229" r="5" fill="rgba(255,255,255,0.6)"/>

        <circle cx="355" cy="280" r="11" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.3)" stroke-width="1">
          <animateTransform attributeName="transform" type="translate" values="0,0;-4,-22;2,-42;0,-60" dur="5s" repeatCount="indefinite" keyTimes="0;0.3;0.7;1" begin="1s"/>
          <animate attributeName="opacity" values="0.8;0.7;0.4;0" dur="5s" repeatCount="indefinite" begin="1s"/>
        </circle>
        <circle cx="350" cy="275" r="3.5" fill="rgba(255,255,255,0.55)"/>

        <circle cx="130" cy="350" r="8" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.25)" stroke-width="0.8">
          <animateTransform attributeName="transform" type="translate" values="0,0;2,-15;-1,-28;0,-40" dur="3.5s" repeatCount="indefinite" keyTimes="0;0.3;0.7;1" begin="2s"/>
          <animate attributeName="opacity" values="0.7;0.6;0.35;0" dur="3.5s" repeatCount="indefinite" begin="2s"/>
        </circle>

        <circle cx="390" cy="350" r="6" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.25)" stroke-width="0.8">
          <animateTransform attributeName="transform" type="translate" values="0,0;3,-12;-1,-22;0,-32" dur="3s" repeatCount="indefinite" keyTimes="0;0.3;0.7;1" begin="0.5s"/>
          <animate attributeName="opacity" values="0.7;0.55;0.3;0" dur="3s" repeatCount="indefinite" begin="0.5s"/>
        </circle>

        <circle cx="72" cy="420" r="9" fill="url(#bubbleG)" stroke="rgba(255,255,255,0.28)" stroke-width="0.8">
          <animateTransform attributeName="transform" type="translate" values="0,0;-2,-18;1,-34;0,-48" dur="4.5s" repeatCount="indefinite" keyTimes="0;0.3;0.7;1" begin="1.5s"/>
          <animate attributeName="opacity" values="0.75;0.6;0.3;0" dur="4.5s" repeatCount="indefinite" begin="1.5s"/>
        </circle>
        <circle cx="68" cy="415" r="3" fill="rgba(255,255,255,0.5)"/>

        <!-- Small sparkle stars -->
        <path d="M400,230 L402,226 L404,230 L408,232 L404,234 L402,238 L400,234 L396,232Z" fill="rgba(255,255,255,0.4)" opacity="0.6">
          <animate attributeName="opacity" values="0.6;0.1;0.6" dur="3s" repeatCount="indefinite"/>
        </path>
        <path d="M55,190 L56.5,187 L58,190 L61,191.5 L58,193 L56.5,196 L55,193 L52,191.5Z" fill="rgba(255,200,220,0.5)" opacity="0.7">
          <animate attributeName="opacity" values="0.7;0.15;0.7" dur="2.5s" repeatCount="indefinite" begin="1s"/>
        </path>
        <path d="M430,180 L431,178 L432,180 L434,181 L432,182 L431,184 L430,182 L428,181Z" fill="rgba(255,255,255,0.5)" opacity="0.5">
          <animate attributeName="opacity" values="0.5;0.1;0.5" dur="2s" repeatCount="indefinite" begin="0.5s"/>
        </path>

        <!-- Steam from machine top -->
        <path d="M200,272 Q196,258 200,245 Q204,232 200,220" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="4" stroke-linecap="round">
          <animate attributeName="opacity" values="0.15;0.05;0.15" dur="2.5s" repeatCount="indefinite"/>
        </path>
        <path d="M240,270 Q238,255 242,242 Q246,229 242,216" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="3.5" stroke-linecap="round">
          <animate attributeName="opacity" values="0.12;0.04;0.12" dur="3s" repeatCount="indefinite" begin="0.8s"/>
        </path>
        <path d="M280,272 Q284,257 280,244 Q276,231 280,218" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="3" stroke-linecap="round">
          <animate attributeName="opacity" values="0.1;0.03;0.1" dur="2.8s" repeatCount="indefinite" begin="1.4s"/>
        </path>
      </svg>
    </div>
    </div>
  </div>
</div>
</div>

<script>
const card = document.getElementById('card');
function go(mode) {
  if(mode === 'register') { card.classList.add('reg'); } else { card.classList.remove('reg'); }
}
function tPw(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
