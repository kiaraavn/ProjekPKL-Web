<?php
// include koneksi database
include 'koneksi.php';

// query ambil data keranjang dan produk (join)
$sql = "
SELECT 
  c.cart_id,
  c.quantity,
  p.id_produk,
  p.nama_produk,
  p.description,
  p.price,
  p.stok_quantity,
  p.image_url
FROM cart c
JOIN produk p ON c.id_produk = p.id_produk
";

$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Keranjang Belanja</title>
  <style>
    .produk {
      border: 1px solid #ddd;
      padding: 10px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
    }
    .produk img {
      width: 100px;
      margin-right: 15px;
    }
    .produk-details {
      flex-grow: 1;
    }
  </style>
</head>
<body>
  <h1>Keranjang Belanja</h1>

  <?php
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      ?>
      <div class="produk">
        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
        <div class="produk-details">
          <h3><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
          <p><?php echo htmlspecialchars($row['description']); ?></p>
          <p>Harga: Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></p>
          <p>Stok: <?php echo $row['stok_quantity']; ?></p>
          <p>Jumlah di keranjang: <?php echo $row['quantity']; ?></p>
        </div>
      </div>
      <?php
    }
  } else {
    echo "<p>Keranjang kamu kosong.</p>";
  }
  ?>

</body>
</html>
