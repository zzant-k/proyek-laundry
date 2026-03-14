<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Hapus Transaksi
 * ═══════════════════════════════════════════════════════
 */
require_once 'config.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($id) {
    // Ambil kode_order sebelum dihapus untuk cascade ke pengiriman
    $stKode = $conn->prepare("SELECT kode_order FROM transaksi WHERE id_laundry = ? LIMIT 1");
    $stKode->bind_param('i', $id);
    $stKode->execute();
    $rowKode = $stKode->get_result()->fetch_assoc();
    $stKode->close();
    $kodeOrder = $rowKode['kode_order'] ?? null;

    $stmt = $conn->prepare("DELETE FROM transaksi WHERE id_laundry = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        // Cascade: hapus dari pengiriman juga
        if ($kodeOrder) {
            $stDel = $conn->prepare("DELETE FROM pengiriman WHERE kode_order = ?");
            $stDel->bind_param('s', $kodeOrder);
            $stDel->execute();
            $stDel->close();
        }
        setFlash('success', 'Transaksi berhasil dihapus.');
    } else {
        setFlash('danger', 'Gagal menghapus transaksi.');
    }
}

header('Location: transaksi.php');
exit;
