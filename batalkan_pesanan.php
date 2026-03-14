<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Batalkan Pesanan (User)
 * ═══════════════════════════════════════════════════════
 */
require_once 'dashboard/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId   = (int) $_SESSION['id'];
$idLaundry = (int) ($_POST['id_laundry'] ?? 0);

if ($idLaundry <= 0) {
    setFlash('danger', 'ID pesanan tidak valid.');
    header('Location: riwayat_pesanan.php');
    exit;
}

// Verify: pesanan milik user ini dan status masih 'Baru'
$stmt = $conn->prepare("SELECT id_laundry, status, kode_order FROM transaksi WHERE id_laundry = ? AND id_user = ? LIMIT 1");
$stmt->bind_param('ii', $idLaundry, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    setFlash('danger', 'Pesanan tidak ditemukan atau bukan milik Anda.');
    header('Location: riwayat_pesanan.php');
    exit;
}

if ($order['status'] !== 'Baru') {
    setFlash('danger', 'Pesanan hanya bisa dibatalkan jika statusnya masih "Baru".');
    header('Location: riwayat_pesanan.php');
    exit;
}

// Update status to Dibatalkan
$upd = $conn->prepare("UPDATE transaksi SET status = 'Dibatalkan' WHERE id_laundry = ? AND id_user = ?");
$upd->bind_param('ii', $idLaundry, $userId);

if ($upd->execute()) {
    setFlash('success', 'Pesanan #' . $order['kode_order'] . ' berhasil dibatalkan.');
} else {
    setFlash('danger', 'Gagal membatalkan pesanan. Silakan coba lagi.');
}
$upd->close();

header('Location: riwayat_pesanan.php');
exit;
