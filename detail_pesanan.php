<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id'])) {
    header("Location: lacak_pesanan.php");
    exit;
}

$order_id = intval($_GET['order_id']);

// Ambil data pesanan (orders)
$queryOrder = "SELECT * FROM orders WHERE order_id = $order_id AND user_id = $user_id LIMIT 1";
$resultOrder = mysqli_query($koneksi, $queryOrder);
$order = mysqli_fetch_assoc($resultOrder);

if (!$order) {
    echo "Pesanan tidak ditemukan atau Anda tidak berhak mengaksesnya.";
    exit;
}

// Ambil detail item pesanan
$queryItems = "
SELECT oi.order_item_id, oi.quantity, oi.price, p.nama_produk, p.image_url 
FROM order_item oi 
JOIN produk p ON oi.id_produk = p.id_produk 
WHERE oi.order_id = $order_id";
$resultItems = mysqli_query($koneksi, $queryItems);

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Detail Pesanan - User</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Container dan layout mirip halaman user/lacak pesanan */
    .container {
      max-width: 900px;
      margin: 30px auto;
      background-color: white;
      display: flex;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      min-height: 500px;
    }
    .sidebar {
      width: 220px;
      background-color: #8b5e3c;
      color: white;
      padding: 20px;
      border-radius: 10px 0 0 10px;
      display: flex;
      flex-direction: column;
      height: 100%;
      min-height: 100%;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      margin: 12px 0;
      font-weight: 600;
    }
    .sidebar a.active, .sidebar a:hover {
      text-decoration: underline;
    }
    .main-content {
      flex: 1;
      padding: 30px 40px;
      border-radius: 0 10px 10px 0;
    }
    h2 {
      margin-bottom: 20px;
      color: #8b5e3c;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 12px;
      text-align: left;
    }
    th {
      background-color: #f4e9db;
      color: #8b5e3c;
    }
    img.product-img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 5px;
    }
    .back-link {
      display: inline-block;
      margin-top: 20px;
      color: #8b5e3c;
      text-decoration: none;
      font-weight: 600;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="sidebar">
    <a href="user.php">Akun Saya</a>
    <a href="lacak_pesanan.php" class="active">Lacak Pesanan</a>
    <a href="ulasan_saya.php">Ulasan Saya</a>
  </div>

  <div class="main-content">
    <h2>Detail Pesanan #<?= htmlspecialchars($order['order_id']) ?></h2>

    <p><strong>Tanggal Pesanan:</strong> <?= date('d M Y, H:i', strtotime($order['order_date'])) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
    <p><strong>Total Bayar:</strong> Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></p>

    <table>
      <thead>
        <tr>
          <th>Produk</th>
          <th>Gambar</th>
          <th>Jumlah</th>
          <th>Harga per Unit</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($item = mysqli_fetch_assoc($resultItems)): ?>
        <tr>
          <td><?= htmlspecialchars($item['nama_produk']) ?></td>
          <td><img src="uploads/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="product-img"></td>
          <td><?= $item['quantity'] ?></td>
          <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <a href="lacak_pesanan.php" class="back-link">&larr; Kembali ke Lacak Pesanan</a>
  </div>
</div>

</body>
</html>
