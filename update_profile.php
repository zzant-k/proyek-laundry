<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Update Profile Handler
 * ═══════════════════════════════════════════════════════
 */
require_once 'dashboard/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$userId = (int) $_SESSION['id'];
$nama   = trim($_POST['nama'] ?? '');
$email  = trim($_POST['email'] ?? '');
$no_hp  = trim($_POST['no_hp'] ?? '');

// Validasi
if (empty($nama) || empty($email)) {
    setFlash('danger', 'Nama dan email wajib diisi.');
    header('Location: profile.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('danger', 'Format email tidak valid.');
    header('Location: profile.php');
    exit;
}

// Cek email duplikat (bukan milik user ini)
$stDup = $conn->prepare("SELECT iduser FROM user WHERE email = ? AND iduser != ? LIMIT 1");
$stDup->bind_param('si', $email, $userId);
$stDup->execute();
if ($stDup->get_result()->num_rows > 0) {
    setFlash('danger', 'Email sudah digunakan oleh akun lain.');
    $stDup->close();
    header('Location: profile.php');
    exit;
}
$stDup->close();

// Handle foto upload
$fotoFilename = null;
if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto_profile'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    $allowedTypes = ['image/jpeg', 'image/png'];

    if ($file['size'] > $maxSize) {
        setFlash('danger', 'Ukuran foto maksimal 2MB.');
        header('Location: profile.php');
        exit;
    }

    if (!in_array($file['type'], $allowedTypes)) {
        setFlash('danger', 'Format foto harus JPG atau PNG.');
        header('Location: profile.php');
        exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fotoFilename = 'profile_' . $userId . '_' . time() . '.' . $ext;
    $uploadDir = __DIR__ . '/assets/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Hapus foto lama
    $stOldFoto = $conn->prepare("SELECT foto_profile FROM profile WHERE id_user = ? LIMIT 1");
    $stOldFoto->bind_param('i', $userId);
    $stOldFoto->execute();
    $oldFoto = $stOldFoto->get_result()->fetch_assoc();
    $stOldFoto->close();

    if (!empty($oldFoto['foto_profile']) && file_exists($uploadDir . $oldFoto['foto_profile'])) {
        unlink($uploadDir . $oldFoto['foto_profile']);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fotoFilename)) {
        setFlash('danger', 'Gagal mengupload foto.');
        header('Location: profile.php');
        exit;
    }
}

// Update profile table
if ($fotoFilename) {
    $stProfile = $conn->prepare("UPDATE profile SET nama = ?, email = ?, no_hp = ?, foto_profile = ? WHERE id_user = ?");
    $stProfile->bind_param('ssssi', $nama, $email, $no_hp, $fotoFilename, $userId);
} else {
    $stProfile = $conn->prepare("UPDATE profile SET nama = ?, email = ?, no_hp = ? WHERE id_user = ?");
    $stProfile->bind_param('sssi', $nama, $email, $no_hp, $userId);
}
$stProfile->execute();
$stProfile->close();

// Sync back to user table
$stUser = $conn->prepare("UPDATE user SET nama = ?, email = ?, no_hp = ? WHERE iduser = ?");
$stUser->bind_param('sssi', $nama, $email, $no_hp, $userId);
$stUser->execute();
$stUser->close();

// Update session
$_SESSION['nama']  = $nama;
$_SESSION['email'] = $email;
$_SESSION['no_hp'] = $no_hp;

setFlash('success', 'Profil berhasil diperbarui!');
header('Location: profile.php');
exit;
