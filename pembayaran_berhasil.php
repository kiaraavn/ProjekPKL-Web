<?php
session_start();
include 'koneksi.php';

// Ambil order_id dari URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    header("Location: index.php");
    exit;
}

// Ambil data order dan payment
$query = "SELECT o.order_id, o.total_amount, o.order_date, o.status,
                 p.payment_method, p.amount as payment_amount, p.payment_status
          FROM orders o 
          LEFT JOIN payments p ON o.order_id = p.order_id 
          WHERE o.order_id = ?";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header("Location: index.php");
    exit;
}

// Tentukan total yang akan ditampilkan
$total_display = $order['payment_amount'] ? $order['payment_amount'] : $order['total_amount'];

// Format metode pembayaran
$payment_method_display = $order['payment_method'] ? $order['payment_method'] : 'Transfer Bank';
if ($payment_method_display == 'transfer') {
    $payment_method_display = 'Transfer Bank BCA - kia 759866392';
} elseif ($payment_method_display == 'cod') {
    $payment_method_display = 'Cash on Delivery (COD)';
} elseif ($payment_method_display == 'ewallet') {
    $payment_method_display = 'E-Wallet (OVO/GoPay/DANA)';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f4f1ed 0%, #e8e5e0 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .success-header {
            background: linear-gradient(135deg, #6f4e37 0%, #8b5a3c 100%);
            color: white;
            padding: 40px 30px;
        }

        .success-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .success-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .success-content {
            padding: 40px 30px;
        }

        .order-details {
            text-align: left;
            margin-bottom: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #4d2c1d;
            font-size: 16px;
        }

        .detail-value {
            color: #333;
            font-size: 16px;
            text-align: right;
            max-width: 60%;
        }

        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #6f4e37;
        }

        .btn-home {
            background: linear-gradient(135deg, #6f4e37 0%, #8b5a3c 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 78, 55, 0.3);
        }

        .success-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        /* Debug info */
        .debug-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="success-container">
    <!-- Debug Info (hapus di production) -->
    <div class="debug-info">
        <strong>Debug:</strong><br>
        Order ID: <?= $order_id ?><br>
        Total Amount: <?= $order['total_amount'] ?><br>
        Payment Amount: <?= $order['payment_amount'] ?><br>
        Display Total: <?= $total_display ?>
    </div>

    <div class="success-header">
        <div class="success-icon">✅</div>
        <h1>Pembayaran Berhasil!</h1>
        <p>Terima kasih, pembayaran Anda telah kami terima</p>
    </div>

    <div class="success-content">
        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order ID:</span>
                <span class="detail-value">#<?= $order['order_id'] ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Jumlah:</span>
                <span class="detail-value total-amount">Rp <?= number_format($total_display, 0, ',', '.') ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Metode:</span>
                <span class="detail-value"><?= htmlspecialchars($payment_method_display) ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tanggal:</span>
                <span class="detail-value"><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value" style="color: #28a745; font-weight: 600;">
                    <?= ucfirst($order['status']) ?>
                </span>
            </div>
        </div>

        <a href="index.php" class="btn-home">Kembali ke Beranda</a>
    </div>
</div>

</body>
</html>
