<?php
session_start();
include 'koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin'])) {
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24;'>";
    echo "<h3>❌ Akses Ditolak!</h3>";
    echo "<p>Silakan login sebagai admin terlebih dahulu</p>";
    echo "<p><a href='admin_login.php'>Login Admin</a></p>";
    echo "</div>";
    exit;
}

// Ambil order_id
$order_id = 0;
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = intval($_GET['id']);
}

if ($order_id == 0) {
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24;'>";
    echo "<h3>❌ Order ID tidak valid!</h3>";
    echo "<p><a href='transaksi.php'>← Kembali ke Transaksi</a></p>";
    echo "</div>";
    exit;
}

// Ambil data transaksi
$query = "SELECT o.*, u.username, s.shipping_addres, s.shipping_method, s.shipment_status
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          LEFT JOIN shipments s ON o.order_id = s.order_id
          WHERE o.order_id = $order_id";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Database error: " . mysqli_error($koneksi));
}

$transaksi = mysqli_fetch_assoc($result);

if (!$transaksi) {
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24;'>";
    echo "<h3>❌ Transaksi tidak ditemukan!</h3>";
    echo "<p>Order ID: <strong>$order_id</strong></p>";
    echo "<p><a href='transaksi.php'>← Kembali ke Transaksi</a></p>";
    echo "</div>";
    exit;
}

// Proses update
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];
    $new_total = floatval($_POST['total_amount']);
    $new_shipping_method = $_POST['shipping_method'] ?? 'standard';
    $new_shipment_status = $_POST['shipment_status'] ?? 'pending';
    
    // Validasi status sesuai ENUM
    $valid_statuses = ['pending', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        $error_message = "Status tidak valid!";
    } else {
        // Update orders table
        $update_orders_query = "UPDATE orders SET status = ?, total_amount = ? WHERE order_id = ?";
        $stmt1 = $koneksi->prepare($update_orders_query);
        
        if (!$stmt1) {
            $error_message = "Prepare failed: " . $koneksi->error;
        } else {
            $stmt1->bind_param("sdi", $new_status, $new_total, $order_id);
            $order_updated = $stmt1->execute();
            
            if (!$order_updated) {
                $error_message = "Update orders failed: " . $stmt1->error;
            }
        }
        
        // Update shipments table jika ada
        $shipment_updated = true;
        if (!empty($transaksi['shipping_address'])) {
            $update_shipments_query = "UPDATE shipments SET shipping_method = ?, shipment_status = ? WHERE order_id = ?";
            $stmt2 = $koneksi->prepare($update_shipments_query);
            
            if (!$stmt2) {
                $error_message .= " | Shipment prepare failed: " . $koneksi->error;
            } else {
                $stmt2->bind_param("ssi", $new_shipping_method, $new_shipment_status, $order_id);
                $shipment_updated = $stmt2->execute();
                
                if (!$shipment_updated) {
                    $error_message .= " | Update shipments failed: " . $stmt2->error;
                }
            }
        }
        
        if ($order_updated && $shipment_updated && empty($error_message)) {
            $success_message = "✅ Transaksi berhasil diupdate!";
            
            // Refresh data
            $result = mysqli_query($koneksi, $query);
            $transaksi = mysqli_fetch_assoc($result);
        } else if (empty($error_message)) {
            $error_message = "❌ Gagal mengupdate data";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi #<?= $order_id ?> - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4ed;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #4d2c1d;
            margin: 0;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }

        .form-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4d2c1d;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #4d2c1d;
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #4d2c1d;
            color: white;
        }

        .btn-primary:hover {
            background-color: #6b3e2a;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            width: 150px;
            color: #4d2c1d;
        }

        .status-current {
            background: #fff3cd;
            color: #856404;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-shipped { background: #cce5ff; color: #004085; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>✏️ Edit Transaksi #<?= $order_id ?></h1>
        <a href="transaksi.php" class="btn-back">← Kembali ke Transaksi</a>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-error"><?= $error_message ?></div>
    <?php endif; ?>

    <!-- Info Customer -->
    <div class="info-box">
        <h4 style="color: #4d2c1d; margin-bottom: 15px;">👤 Info Transaksi</h4>
        <div class="info-row">
            <span class="info-label">Order ID:</span>
            <span><?= $transaksi['order_id'] ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Username:</span>
            <span><?= htmlspecialchars($transaksi['username']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Order Date:</span>
            <span><?= date('d M Y H:i', strtotime($transaksi['order_date'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Current Status:</span>
            <span><span class="status-current status-<?= $transaksi['status'] ?: 'pending' ?>"><?= strtoupper($transaksi['status'] ?: 'pending') ?></span></span>
        </div>
        <div class="info-row">
            <span class="info-label">Current Total:</span>
            <span><strong>Rp<?= number_format($transaksi['total_amount'], 0, ',', '.') ?></strong></span>
        </div>
        <?php if (!empty($transaksi['payment_proof'])): ?>
        <div class="info-row">
            <span class="info-label">Bukti Transfer:</span>
            <span>
                <img src="uploads/payments/<?= $transaksi['payment_proof'] ?>" 
                     alt="Bukti Transfer" 
                     style="max-width: 100px; border-radius: 4px; cursor: pointer;"
                     onclick="window.open('uploads/payments/<?= $transaksi['payment_proof'] ?>', '_blank')">
            </span>
        </div>
        <?php endif; ?>
        <?php if ($transaksi['shipping_addres']): ?>
        <div class="info-row">
            <span class="info-label">Alamat Kirim:</span>
            <span><?= htmlspecialchars($transaksi['shipping_addres']) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Form Edit -->
    <div class="form-container">
        <h3 style="color: #4d2c1d; margin-bottom: 25px;">📝 Edit Transaksi</h3>
        
        <form method="POST">
            <div class="form-group">
                <label>Order ID (Read Only)</label>
                <input type="text" class="form-control" value="<?= $transaksi['order_id'] ?>" readonly>
            </div>

            <div class="form-group">
                <label>Username (Read Only)</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($transaksi['username']) ?>" readonly>
            </div>

            <div class="form-group">
                <label for="status">Status Order * (Sesuai Database ENUM)</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="pending" <?= ($transaksi['status'] ?: 'pending') == 'pending' ? 'selected' : '' ?>>
                        PENDING - Menunggu Pembayaran
                    </option>
                    <option value="shipped" <?= $transaksi['status'] == 'shipped' ? 'selected' : '' ?>>
                        SHIPPED - Dikirim
                    </option>
                    <option value="delivered" <?= $transaksi['status'] == 'delivered' ? 'selected' : '' ?>>
                        DELIVERED - Selesai/Terkirim
                    </option>
                    <option value="cancelled" <?= $transaksi['status'] == 'cancelled' ? 'selected' : '' ?>>
                        CANCELLED - Dibatalkan
                    </option>
                </select>
                <small style="color: #666; font-size: 12px;">
                    * Status sesuai dengan ENUM database: pending, shipped, delivered, cancelled
                </small>
            </div>

            <?php if ($transaksi['shipping_addres']): ?>
            <div class="form-group">
                <label for="shipping_method">Metode Pengiriman</label>
                <select name="shipping_method" id="shipping_method" class="form-control">
                    <option value="standard" <?= ($transaksi['shipping_method'] ?: 'standard') == 'standard' ? 'selected' : '' ?>>Standard (3-5 hari)</option>
                    <option value="express" <?= $transaksi['shipping_method'] == 'express' ? 'selected' : '' ?>>Express (1-2 hari)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="shipment_status">Status Pengiriman</label>
                <select name="shipment_status" id="shipment_status" class="form-control">
                    <option value="pending" <?= ($transaksi['shipment_status'] ?: 'pending') == 'pending' ? 'selected' : '' ?>>Pending - Menunggu</option>
                    <option value="shipped" <?= $transaksi['shipment_status'] == 'shipped' ? 'selected' : '' ?>>Shipped - Dikirim</option>
                    <option value="delivered" <?= $transaksi['shipment_status'] == 'delivered' ? 'selected' : '' ?>>Delivered - Terkirim</option>
                    <option value="returned" <?= $transaksi['shipment_status'] == 'returned' ? 'selected' : '' ?>>Returned - Dikembalikan</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="total_amount">Total Amount (Rp) *</label>
                <input type="number" name="total_amount" id="total_amount" class="form-control" 
                       value="<?= $transaksi['total_amount'] ?>" required min="0" step="1000">
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    💾 Update Transaksi
                </button>
                <a href="transaksi.php" class="btn btn-secondary">❌ Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});
</script>

</body>
</html>
