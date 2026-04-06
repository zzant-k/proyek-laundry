<?php
/**
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 *  RUMAH LAUNDRY â€” API Cek Status Pesanan
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 */
header('Content-Type: application/json');
require_once 'config.php';

$kode = trim($_GET['kode_order'] ?? '');

if (empty($kode)) {
    echo json_encode(['found' => false, 'message' => 'Kode order tidak boleh kosong.']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM transaksi WHERE kode_order = ? LIMIT 1");
$stmt->bind_param('s', $kode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $order = $result->fetch_assoc();
    
    if ($order['status'] === 'Dibatalkan') {
        echo json_encode(['found' => false, 'message' => 'Pesanan telah dibatalkan.']);
        exit;
    }
    
    $statusMap = [
        'Baru'          => ['label' => 'Order Diterima', 'step' => 1, 'class' => 'waiting'],
        
        'Dicuci'        => ['label' => 'Diproses', 'step' => 2, 'class' => 'diproses'],
        'Diproses'      => ['label' => 'Diproses', 'step' => 2, 'class' => 'diproses'],
        'Sedang Dicuci' => ['label' => 'Diproses', 'step' => 2, 'class' => 'diproses'],
        'Di Proses'     => ['label' => 'Diproses', 'step' => 2, 'class' => 'diproses'],
        
        'Dijemput'      => ['label' => 'Selesai', 'step' => 3, 'class' => 'selesai'],
        'Dikirim'       => ['label' => 'Selesai', 'step' => 3, 'class' => 'selesai'],
        'Selesai'       => ['label' => 'Selesai', 'step' => 3, 'class' => 'selesai']
    ];

    $sInfo = $statusMap[$order['status']] ?? $statusMap['Baru'];

    echo json_encode([
        'found'   => true,
        'kode'    => $order['kode_order'],
        'nama'    => $order['nama'],
        'tanggal' => date('d F Y', strtotime($order['tanggal_penjemputan'])),
        'status'  => $sInfo['label'],
        'class'   => $sInfo['class'],
        'step'    => $sInfo['step']
    ]);
} else {
    echo json_encode(['found' => false]);
}
