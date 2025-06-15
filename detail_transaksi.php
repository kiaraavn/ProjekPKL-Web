<?php
session_start();
// Cek session
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); 
    exit;
}
include 'koneksi.php';

// Ambil order_id dari parameter URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; border-radius: 8px; margin: 20px;'>";
    echo "<h3>❌ ID Transaksi tidak valid!</h3>";
    echo "<p>URL yang benar: detail_transaksi.php?order_id=123</p>";
    echo "<p><a href='transaksi.php' style='color: #721c24; font-weight: bold;'>← Kembali ke Transaksi</a></p>";
    echo "</div>";
    exit;
}

// Query untuk mengambil data transaksi lengkap - sesuai struktur database yang ada
$query = "SELECT o.*, 
                 u.username, u.email, u.no_telepon,
                 s.shipping_addres, s.shipping_method, s.shipment_status, s.created_at as shipment_date
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.user_id 
          LEFT JOIN shipments s ON o.order_id = s.order_id
          WHERE o.order_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();

if (!$transaksi) {
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; border-radius: 8px; margin: 20px;'>";
    echo "<h3>❌ Transaksi tidak ditemukan!</h3>";
    echo "<p>Order ID: <strong>$order_id</strong> tidak ada di database</p>";
    echo "<p><a href='transaksi.php' style='color: #721c24; font-weight: bold;'>← Kembali ke Transaksi</a></p>";
    echo "</div>";
    exit;
}

// Query untuk mengambil item yang dibeli
$query_items = "SELECT oi.*, p.nama_produk, p.price, p.image_url
                FROM order_item oi
                JOIN produk p ON oi.id_produk = p.id_produk
                WHERE oi.order_id = ?";
$stmt_items = $koneksi->prepare($query_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

// Ambil data recipient jika ada di session
$recipient_data = null;
if (isset($_SESSION['recipient_data']) && $_SESSION['recipient_data']['order_id'] == $order_id) {
    $recipient_data = $_SESSION['recipient_data'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi #<?= $order_id ?> - Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4ed;
            margin: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #3e2723;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            color: white !important;
            margin-bottom: 30px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            display: block;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: #5d4037;
        }

        .profile {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            color: #4d2c1d;
            margin: 0;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-edit {
            background-color: #4d2c1d;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .card-header h3 {
            color: #4d2c1d;
            margin: 0;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-shipped {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .info-value {
            font-weight: 600;
            color: #333;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 600;
            color: #4d2c1d;
            background-color: #f8f9fa;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .bukti-transfer {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid #ddd;
            cursor: pointer;
        }

        .bukti-transfer:hover {
            border-color: #4d2c1d;
        }

        .timeline {
            position: relative;
            margin: 20px 0;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -30px;
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #ddd;
        }

        .timeline-item:last-child:before {
            height: 15px;
        }

        .timeline-item:after {
            content: '';
            position: absolute;
            left: -36px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #4d2c1d;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .timeline-desc {
            font-size: 14px;
            color: #333;
        }

        .summary-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-total {
            font-weight: 700;
            font-size: 18px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div>
        <h2>Admin Panel</h2>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="produk.php" class="nav-link">Daftar Produk</a>
        <a href="transaksi.php" class="nav-link active">Transaksi</a>
        <a href="data_akun.php" class="nav-link">Data Akun</a>
        <a href="data_kategori.php" class="nav-link">Data Kategori</a>
    </div>
    <div class="profile">
        <div>Admin</div>
        <a href="logout_admin.php" class="nav-link">Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h2>📋 Detail Transaksi #<?= $order_id ?></h2>
        <div>
            <a href="transaksi.php" class="btn btn-back">← Kembali</a>
            <a href="edit_transaksi.php?order_id=<?= $order_id ?>" class="btn btn-edit">✏️ Edit</a>
        </div>
    </div>

    <!-- Order Info -->
    <div class="card">
        <div class="card-header">
            <h3>📦 Informasi Order</h3>
            <span class="status-badge status-<?= $transaksi['status'] ?: 'pending' ?>">
                <?= strtoupper($transaksi['status'] ?: 'pending') ?>
            </span>
        </div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Order ID</div>
                    <div class="info-value">#<?= $transaksi['order_id'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Order</div>
                    <div class="info-value"><?= date('d M Y H:i', strtotime($transaksi['order_date'])) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Last Update</div>
                    <div class="info-value">
                        <?= $transaksi['updated_at'] ? date('d M Y H:i', strtotime($transaksi['updated_at'])) : 'Belum diupdate' ?>
                    </div>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <div class="info-label">Customer</div>
                    <div class="info-value"><?= htmlspecialchars($transaksi['username']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($transaksi['email'] ?? '-') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">No. Telepon</div>
                    <div class="info-value"><?= htmlspecialchars($transaksi['no_telepon'] ?? '-') ?></div>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <div class="info-label">Total Amount</div>
                    <div class="info-value">Rp<?= number_format($transaksi['total_amount'], 0, ',', '.') ?></div>
                </div>
                <?php if ($recipient_data): ?>
                <div class="info-item">
                    <div class="info-label">Nama Penerima</div>
                    <div class="info-value"><?= htmlspecialchars($recipient_data['nama_penerima']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telepon Penerima</div>
                    <div class="info-value"><?= htmlspecialchars($recipient_data['telepon_penerima']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payment Info -->
    <div class="card">
        <div class="card-header">
            <h3>💳 Informasi Pembayaran</h3>
            <span class="status-badge status-<?= !empty($transaksi['payment_proof']) ? 'delivered' : 'pending' ?>">
                <?= !empty($transaksi['payment_proof']) ? 'PAID' : 'PENDING' ?>
            </span>
        </div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Status Pembayaran</div>
                    <div class="info-value">
                        <?= !empty($transaksi['payment_proof']) ? 'Sudah Upload Bukti' : 'Belum Bayar' ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jumlah</div>
                    <div class="info-value">Rp<?= number_format($transaksi['total_amount'], 0, ',', '.') ?></div>
                </div>
            </div>
            <?php if (!empty($transaksi['payment_proof'])): ?>
            <div>
                <div class="info-item">
                    <div class="info-label">Bukti Transfer</div>
                    <div class="info-value">
                        <img src="uploads/payments/<?= $transaksi['payment_proof'] ?>" 
                             alt="Bukti Transfer" 
                             class="bukti-transfer"
                             onclick="window.open('uploads/payments/<?= $transaksi['payment_proof'] ?>', '_blank')">
                        <br><small style="color: #666;">Klik untuk memperbesar</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Shipment Info -->
    <?php if (!empty($transaksi['shipping_addres'])): ?>
    <div class="card">
        <div class="card-header">
            <h3>🚚 Informasi Pengiriman</h3>
            <span class="status-badge status-<?= $transaksi['shipment_status'] ?: 'pending' ?>">
                <?= strtoupper($transaksi['shipment_status'] ?: 'pending') ?>
            </span>
        </div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Metode Pengiriman</div>
                    <div class="info-value">
                        <?= $transaksi['shipping_method'] == 'express' ? 'Express (1-2 hari)' : 'Standard (3-5 hari)' ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Pengiriman</div>
                    <div class="info-value"><?= ucfirst($transaksi['shipment_status'] ?: 'pending') ?></div>
                </div>
                <?php if ($transaksi['shipment_date']): ?>
                <div class="info-item">
                    <div class="info-label">Tanggal Pengiriman</div>
                    <div class="info-value"><?= date('d M Y H:i', strtotime($transaksi['shipment_date'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
            <div>
                <div class="info-item">
                    <div class="info-label">Alamat Pengiriman</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($transaksi['shipping_addres'])) ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Order Items -->
    <div class="card">
        <div class="card-header">
            <h3>🛍️ Item yang Dibeli</h3>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_items = 0;
                    $subtotal = 0;
                    if(mysqli_num_rows($result_items) > 0): 
                        while($item = mysqli_fetch_assoc($result_items)):
                            $item_subtotal = $item['price'] * $item['quantity'];
                            $subtotal += $item_subtotal;
                            $total_items += $item['quantity'];
                    ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?= $item['image_url'] ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="product-img">
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; background: #eee; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #999;">No img</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                            <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rp<?= number_format($item_subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">Tidak ada item yang dibeli</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="summary-box">
            <div class="summary-row">
                <span>Subtotal (<?= $total_items ?> items)</span>
                <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>
            <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span>Rp<?= number_format(($transaksi['total_amount'] - $subtotal), 0, ',', '.') ?></span>
            </div>
            <div class="summary-row summary-total">
                <span>Total</span>
                <span>Rp<?= number_format($transaksi['total_amount'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Order Timeline -->
    <div class="card">
        <div class="card-header">
            <h3>📅 Timeline Transaksi</h3>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date"><?= date('d M Y H:i', strtotime($transaksi['order_date'])) ?></div>
                <div class="timeline-title">Order Dibuat</div>
                <div class="timeline-desc">Customer membuat order baru</div>
            </div>

            <?php if (!empty($transaksi['payment_proof'])): ?>
            <div class="timeline-item">
                <div class="timeline-date"><?= $transaksi['updated_at'] ? date('d M Y H:i', strtotime($transaksi['updated_at'])) : 'Unknown' ?></div>
                <div class="timeline-title">Bukti Pembayaran Diupload</div>
                <div class="timeline-desc">Customer mengupload bukti transfer</div>
            </div>
            <?php endif; ?>

            <?php if ($transaksi['status'] == 'shipped'): ?>
            <div class="timeline-item">
                <div class="timeline-date"><?= $transaksi['updated_at'] ? date('d M Y H:i', strtotime($transaksi['updated_at'])) : 'Unknown' ?></div>
                <div class="timeline-title">Order Dikirim</div>
                <div class="timeline-desc">Paket telah dikirim ke customer</div>
            </div>
            <?php endif; ?>

            <?php if ($transaksi['status'] == 'delivered'): ?>
            <div class="timeline-item">
                <div class="timeline-date"><?= $transaksi['updated_at'] ? date('d M Y H:i', strtotime($transaksi['updated_at'])) : 'Unknown' ?></div>
                <div class="timeline-title">Order Selesai</div>
                <div class="timeline-desc">Paket telah diterima oleh customer</div>
            </div>
            <?php endif; ?>

            <?php if ($transaksi['status'] == 'cancelled'): ?>
            <div class="timeline-item">
                <div class="timeline-date"><?= $transaksi['updated_at'] ? date('d M Y H:i', strtotime($transaksi['updated_at'])) : 'Unknown' ?></div>
                <div class="timeline-title">Order Dibatalkan</div>
                <div class="timeline-desc">Order telah dibatalkan</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmDelete(orderId) {
    if (confirm(`Yakin ingin menghapus transaksi #${orderId}?\n\nData yang akan dihapus:\n- Order #${orderId}\n- Order items\n- Shipment data`)) {
        // Redirect ke halaman hapus atau buat form delete
        window.location.href = `transaksi.php?delete_order_id=${orderId}`;
    }
}
</script>

</body>
</html>
