<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header("Location: index.php");
    exit;
}

// Ambil data order dengan join ke shipments
$order_query = mysqli_query($koneksi, "
    SELECT o.*, u.username, s.shipping_addres, s.shipping_method, s.shipment_status
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    LEFT JOIN shipments s ON o.order_id = s.order_id
    WHERE o.order_id = $order_id AND o.user_id = $user_id
");

$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    echo "Order tidak ditemukan!";
    exit;
}

// Ambil item order
$items_query = mysqli_query($koneksi, "
    SELECT oi.*, p.nama_produk, p.image_url 
    FROM order_item oi 
    JOIN produk p ON oi.id_produk = p.id_produk 
    WHERE oi.order_id = $order_id
");

$order_items = [];
while ($item = mysqli_fetch_assoc($items_query)) {
    $order_items[] = $item;
}

// Proses upload bukti pembayaran
if (isset($_POST['upload_payment'])) {
    $target_dir = "uploads/payments/";
    
    // Buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["payment_proof"]["name"], PATHINFO_EXTENSION);
    $new_filename = "payment_" . $order_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $uploadOk = 1;
    $imageFileType = strtolower($file_extension);
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["payment_proof"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $error_message = "File bukan gambar.";
        $uploadOk = 0;
    }
    
    // Check file size (max 5MB)
    if ($_FILES["payment_proof"]["size"] > 5000000) {
        $error_message = "File terlalu besar. Maksimal 5MB.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $error_message = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // Error message already set above
    } else {
        if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $target_file)) {
            // Update order dengan bukti pembayaran DAN status
            $update_query = "UPDATE orders SET payment_proof = ?, status = 'waiting_confirmation' WHERE order_id = ?";
            $stmt = $koneksi->prepare($update_query);
            $stmt->bind_param("si", $new_filename, $order_id);
            
            if ($stmt->execute()) {
                $success_message = "Bukti pembayaran berhasil diupload!";
                
                // Refresh data order
                $order_query = mysqli_query($koneksi, "
                    SELECT o.*, u.username, s.shipping_addres, s.shipping_method, s.shipment_status
                    FROM orders o 
                    JOIN users u ON o.user_id = u.user_id 
                    LEFT JOIN shipments s ON o.order_id = s.order_id
                    WHERE o.order_id = $order_id AND o.user_id = $user_id
                ");
                $order = mysqli_fetch_assoc($order_query);
            } else {
                $error_message = "Gagal menyimpan data pembayaran: " . $koneksi->error;
            }
        } else {
            $error_message = "Gagal mengupload file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Order #<?= $order_id ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
            margin-top: 5px;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-details {
            color: #666;
            font-size: 14px;
        }

        .product-price {
            font-weight: 600;
            color: #007bff;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-waiting {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .bank-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .bank-info h4 {
            color: #1976d2;
            margin-bottom: 15px;
        }

        .bank-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .bank-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e3f2fd;
        }

        .bank-name {
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 5px;
        }

        .account-number {
            font-family: monospace;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .account-name {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
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

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: #007bff;
            text-align: center;
            padding: 20px;
            background: #f8f9ff;
            border-radius: 8px;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .order-info {
                grid-template-columns: 1fr;
            }
            
            .bank-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; color: #333; margin-bottom: 30px;">
            Pembayaran Order #<?= $order_id ?>
        </h1>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Informasi Order -->
        <div class="card">
            <h3>📋 Informasi Pesanan</h3>
            <div class="order-info">
                <div class="info-item">
                    <div class="info-label">Order ID</div>
                    <div class="info-value">#<?= $order_id ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge status-<?= $order['status'] == 'pending' ? 'pending' : ($order['status'] == 'waiting_confirmation' ? 'waiting' : 'confirmed') ?>">
                            <?= $order['status'] == 'pending' ? 'Menunggu Pembayaran' : ($order['status'] == 'waiting_confirmation' ? 'Menunggu Konfirmasi' : 'Dikonfirmasi') ?>
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Order</div>
                    <div class="info-value"><?= date('d M Y H:i', strtotime($order['order_date'])) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Penerima</div>
                    <div class="info-value">
                        <?php 
                        if (isset($_SESSION['recipient_data']) && $_SESSION['recipient_data']['order_id'] == $order_id) {
                            echo htmlspecialchars($_SESSION['recipient_data']['nama_penerima']);
                        } else {
                            echo htmlspecialchars($order['username']);
                        }
                        ?>
                    </div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Alamat Pengiriman</div>
                    <div class="info-value">
                        <?php 
                        if (!empty($order['shipping_address'])) {
                            echo htmlspecialchars($order['shipping_address']);
                        } else {
                            echo 'Alamat sedang diproses';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk yang Dibeli -->
        <div class="card">
            <h3>🛍️ Produk yang Dibeli</h3>
            <?php foreach($order_items as $item): ?>
                <div class="product-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="product-image">
                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($item['nama_produk']) ?></div>
                        <div class="product-details">
                            <?= $item['quantity'] ?> x Rp<?= number_format($item['price'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="product-price">
                        Rp<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Total Pembayaran -->
        <div class="total-amount">
            Total Pembayaran: Rp<?= number_format($order['total_amount'], 0, ',', '.') ?>
        </div>

        <?php if ($order['status'] == 'pending'): ?>
            <!-- Informasi Bank -->
            <div class="card">
                <h3>🏦 Informasi Pembayaran</h3>
                <p style="margin-bottom: 20px;">Silakan transfer ke salah satu rekening berikut:</p>
                
                <div class="bank-info">
                    <h4>Pilih Bank untuk Transfer</h4>
                    <div class="bank-details">
                        <div class="bank-item">
                            <div class="bank-name">Bank BCA</div>
                            <div class="account-number">1234567890</div>
                            <div class="account-name">a/n Toko Online</div>
                        </div>
                        <div class="bank-item">
                            <div class="bank-name">Bank Mandiri</div>
                            <div class="account-number">0987654321</div>
                            <div class="account-name">a/n Toko Online</div>
                        </div>
                        <div class="bank-item">
                            <div class="bank-name">Bank BRI</div>
                            <div class="account-number">1122334455</div>
                            <div class="account-name">a/n Toko Online</div>
                        </div>
                        <div class="bank-item">
                            <div class="bank-name">Bank BNI</div>
                            <div class="account-number">5544332211</div>
                            <div class="account-name">a/n Toko Online</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Bukti Pembayaran -->
            <div class="card">
                <h3>📤 Upload Bukti Pembayaran</h3>
                <p style="margin-bottom: 20px;">Setelah melakukan transfer, silakan upload bukti pembayaran di bawah ini:</p>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="payment_proof">Bukti Pembayaran (JPG, PNG, max 5MB)</label>
                        <input type="file" id="payment_proof" name="payment_proof" class="form-control" accept="image/*" required>
                    </div>
                    
                    <button type="submit" name="upload_payment" class="btn">
                        📤 Upload Bukti Pembayaran
                    </button>
                </form>
            </div>
        <?php elseif ($order['status'] == 'waiting_confirmation'): ?>
            <div class="card">
                <h3>✅ Bukti Pembayaran Sudah Diupload</h3>
                <p>Terima kasih! Bukti pembayaran Anda sudah kami terima dan sedang dalam proses verifikasi.</p>
                <p>Kami akan mengkonfirmasi pembayaran Anda dalam 1x24 jam.</p>
                
                <?php if ($order['payment_proof']): ?>
                    <div style="margin-top: 20px;">
                        <strong>Bukti Pembayaran:</strong><br>
                        <img src="uploads/payments/<?= htmlspecialchars($order['payment_proof']) ?>" 
                             alt="Bukti Pembayaran" 
                             style="max-width: 300px; margin-top: 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <h3>🎉 Pembayaran Dikonfirmasi</h3>
                <p>Pembayaran Anda sudah dikonfirmasi! Pesanan sedang diproses dan akan segera dikirim.</p>
            </div>
        <?php endif; ?>

        <!-- Tombol Kembali -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn btn-secondary">🏠 Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
