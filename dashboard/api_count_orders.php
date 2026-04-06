<?php
/**
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 *  RUMAH LAUNDRY â€” API: Count User Orders
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 */
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['count' => 0]);
    exit;
}

$userId = (int) $_SESSION['id'];
$stmt = $conn->prepare("SELECT COUNT(*) as c FROM transaksi WHERE id_user = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$count = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

echo json_encode(['count' => (int) $count]);
