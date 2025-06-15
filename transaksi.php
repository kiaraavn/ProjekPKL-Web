<?php
session_start();

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); 
    exit;
}
include 'koneksi.php';

// Query yang disesuaikan dengan struktur database yang ada
$query = "SELECT o.order_id, o.user_id, o.status as order_status, o.total_amount, o.order_date, o.payment_proof,
                 u.username, 
                 s.shipping_method, s.shipment_status
          FROM orders o
          LEFT JOIN users u ON o.user_id = u.user_id
          LEFT JOIN shipments s ON o.order_id = s.order_id
          ORDER BY o.order_date DESC";
$result = mysqli_query($koneksi, $query);

// Handle delete action
if (isset($_POST['delete_order_id'])) {
    $order_id = intval($_POST['delete_order_id']);
    
    // Delete from order_item first
    $delete_items = "DELETE FROM order_item WHERE order_id = ?";
    $stmt1 = $koneksi->prepare($delete_items);
    $stmt1->bind_param("i", $order_id);
    
    // Delete from shipments
    $delete_shipments = "DELETE FROM shipments WHERE order_id = ?";
    $stmt2 = $koneksi->prepare($delete_shipments);
    $stmt2->bind_param("i", $order_id);
    
    // Delete from orders
    $delete_orders = "DELETE FROM orders WHERE order_id = ?";
    $stmt3 = $koneksi->prepare($delete_orders);
    $stmt3->bind_param("i", $order_id);
    
    if ($stmt1->execute() && $stmt2->execute() && $stmt3->execute()) {
        header("Location: transaksi.php?msg=hapus_sukses");
        exit;
    } else {
        $error_msg = "Gagal menghapus transaksi: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Transaksi - Admin Panel</title>
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

    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .stat-number {
      font-size: 2em;
      font-weight: bold;
      color: #4d2c1d;
    }

    .stat-label {
      color: #666;
      margin-top: 5px;
    }

    .table-container {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background-color: #4d2c1d;
    }

    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      color: white;
      font-weight: 600;
    }

    tbody tr:hover {
      background-color: #f8f9fa;
    }

    .status-badge {
      padding: 4px 12px;
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

    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12px;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      margin-right: 5px;
      transition: all 0.3s ease;
    }

    .btn-edit {
      background-color: #4d2c1d;
      color: white;
    }

    .btn-edit:hover {
      background-color: #6b3e2a;
    }

    .btn-delete {
      background-color: #dc3545;
      color: white;
    }

    .btn-delete:hover {
      background-color: #c82333;
    }

    .btn-view {
      background-color: #17a2b8;
      color: white;
    }

    .btn-view:hover {
      background-color: #138496;
    }

    .alert {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      border: 1px solid #c3e6cb;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border-color: #f5c6cb;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }

    .empty-state h3 {
      color: #4d2c1d;
      margin-bottom: 10px;
    }

    .action-buttons {
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
    }

    .bukti-transfer {
      max-width: 50px;
      max-height: 50px;
      border-radius: 4px;
      cursor: pointer;
    }

    .bukti-transfer:hover {
      transform: scale(1.1);
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px;
      }
      
      .sidebar {
        transform: translateX(-100%);
      }
      
      table {
        font-size: 14px;
      }
      
      th, td {
        padding: 10px 8px;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
  <div class="page-header">
    <h2>📊 Data Transaksi</h2>
  </div>

  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_sukses'): ?>
    <div class="alert">
      ✅ Transaksi berhasil dihapus.
    </div>
  <?php endif; ?>

  <?php if (isset($error_msg)): ?>
    <div class="alert alert-error">
      ❌ <?= $error_msg ?>
    </div>
  <?php endif; ?>

  <?php
  // Stats query yang disesuaikan dengan ENUM database
  $stats_query = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN o.status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                    SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders
                  FROM orders o";
  $stats_result = mysqli_query($koneksi, $stats_query);
  $stats = mysqli_fetch_assoc($stats_result);
  ?>

  <div class="stats-container">
    <div class="stat-card">
      <div class="stat-number"><?= $stats['total_orders'] ?></div>
      <div class="stat-label">Total Transaksi</div>
    </div>
    <div class="stat-card">
      <div class="stat-number"><?= $stats['pending_orders'] ?></div>
      <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
      <div class="stat-number"><?= $stats['shipped_orders'] ?></div>
      <div class="stat-label">Shipped</div>
    </div>
    <div class="stat-card">
      <div class="stat-number"><?= $stats['delivered_orders'] ?></div>
      <div class="stat-label">Delivered</div>
    </div>
  </div>

  <!-- Transactions Table -->
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Username</th>
          <th>Status Order</th>
          <th>Metode Kirim</th>
          <th>Status Kirim</th>
          <th>Total</th>
          <th>Bukti Transfer</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if(mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><strong>#<?= $row['order_id'] ?></strong></td>
              <td><?= htmlspecialchars($row['username'] ?? '-') ?></td>
              <td>
                <span class="status-badge status-<?= $row['order_status'] ?: 'pending' ?>">
                  <?php 
                  $status_labels = [
                    'pending' => 'PENDING',
                    'shipped' => 'SHIPPED', 
                    'delivered' => 'DELIVERED',
                    'cancelled' => 'CANCELLED'
                  ];
                  echo $status_labels[$row['order_status']] ?? 'PENDING';
                  ?>
                </span>
              </td>
              <td>
                <?php 
                if ($row['shipping_method']) {
                  echo $row['shipping_method'] == 'standard' ? 'Standard (3-5 hari)' : 'Express (1-2 hari)';
                } else {
                  echo '-';
                }
                ?>
              </td>
              <td>
                <?php if ($row['shipment_status']): ?>
                  <span class="status-badge status-<?= $row['shipment_status'] ?>">
                    <?= strtoupper($row['shipment_status']) ?>
                  </span>
                <?php else: ?>
                  <span style="color: #999;">-</span>
                <?php endif; ?>
              </td>
              <td><strong>Rp<?= number_format($row['total_amount'], 0, ',', '.') ?></strong></td>
              <td>
                <?php if (!empty($row['payment_proof'])): ?>
                  <img src="uploads/payments/<?= $row['payment_proof'] ?>" 
                       alt="Bukti Transfer" 
                       class="bukti-transfer"
                       onclick="window.open('uploads/payments/<?= $row['payment_proof'] ?>', '_blank')">
                <?php else: ?>
                  <span style="color: #999;">Belum ada</span>
                <?php endif; ?>
              </td>
              <td><?= date('d M Y H:i', strtotime($row['order_date'])) ?></td>
              <td>
                <div class="action-buttons">
                  <a href="edit_transaksi.php?order_id=<?= $row['order_id'] ?>" class="btn btn-edit">
                    ✏️ Edit
                  </a>
                  
                  <a href="detail_transaksi.php?order_id=<?= $row['order_id'] ?>" class="btn btn-view">
                    👁️ Detail
                  </a>
                  
                  <form method="post" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus transaksi #<?= $row['order_id'] ?>?')">
                    <input type="hidden" name="delete_order_id" value="<?= $row['order_id'] ?>">
                    <button type="submit" class="btn btn-delete">🗑️ Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9">
              <div class="empty-state">
                <h3>📋 Tidak ada transaksi</h3>
                <p>Belum ada transaksi yang tercatat dalam sistem.</p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
