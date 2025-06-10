
<?php
session_start();
include 'koneksi.php';


$user_id = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produk']) && isset($_POST['jumlah'])) {
  $id_produk = intval($_POST['id_produk']);
  $jumlah = intval($_POST['jumlah']);

  
  $cek = mysqli_query($koneksi, "SELECT * FROM cart WHERE user_id = $user_id AND id_produk = $id_produk");

  if (mysqli_num_rows($cek) > 0) {
   
    mysqli_query($koneksi, "UPDATE cart SET quantity = quantity + $jumlah WHERE user_id = $user_id AND id_produk = $id_produk");
  } else {
    
    $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id_produk"));

    if ($produk) {
      $harga = $produk['price'];
      $gambar = $produk['image_url'];

    
      mysqli_query($koneksi, "INSERT INTO cart (user_id, id_produk, price, image_url, quantity, created_at) 
        VALUES ($user_id, $id_produk, $harga, '$gambar', $jumlah, NOW())");
    }
  }

  header("Location: keranjang.php");
  exit;
}


$query = "SELECT c.*, p.nama_produk, p.image_url, p.price 
          FROM cart c
          JOIN produk p ON c.id_produk = p.id_produk
          WHERE c.user_id = $user_id
          ORDER BY c.created_at DESC";
$result = mysqli_query($koneksi, $query);



?>




<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keranjang Belanja</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fdf0f4;
      color:rgb(55, 32, 22);
    }
       .navbar {
  background-color: #fff;
  border-radius: 12px;
  padding: 12px 24px;
  margin-bottom: 30px;
  box-shadow: 0 0 8px rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 30px;
  padding: 0;
  margin: 0;
}

.nav-links li a {
  text-decoration: none;
  color: #4d2c1d;
  font-weight: bold;
  position: relative;
  padding-bottom: 4px;
  transition: color 0.3s;
}

.nav-links li a:hover,
.nav-links li a.active {
  color: #a52a2a;
}

.nav-links li a.active::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  height: 2px;
  width: 100%;
  background-color: #a52a2a;
  border-radius: 2px;
}

.nav-right a {
  text-decoration: none;
  font-size: 20px;
  margin-left: 16px;
  color: #4d2c1d;
  transition: color 0.2s ease;
}

.nav-right a:hover {
  color: #a52a2a;
}

@media (max-width: 768px) {
  .nav-links {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .nav-right {
    margin-top: 10px;
    display: flex;
    gap: 16px;
  }

  .navbar {
    flex-direction: column;
    align-items: flex-start;
  }
}

    h2 {
      text-align: center;
      padding: 30px 0 10px;
    }

    table {
      width: 90%;
      margin: auto;
      border-collapse: collapse;
      background-color: #fffaf0;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border: 1px solid #e0c7b1;
    }

    th {
      background-color:rgb(57, 41, 32);
      color: white;
    }

    img {
      width: 70px;
      height: auto;
      border-radius: 6px;
    }

    .checkout-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 95%;
      background-color: #fff4e6;
      border-top: 1px solid #cba78d;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
      display: none;
      z-index: 1000;
    }

    .checkout-bar .total {
      font-weight: bold;
      color: #5d4037;
    }

    .checkout-bar button {
      padding: 10px 20px;
      background-color: #8b5e3c;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      /* cursor: pointer; */
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="nav-left">
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="shop1.php">Shop</a></li>
        <li><a href="about1.html">About</a></li>
        
      </ul>
    </div>
    <div class="nav-right">
       
    
      <a href="#keranjang.php"><i class="fas fa-shopping-cart"></i></a>
      <a href="user.php"><i class="fas fa-user"></i></a>
        <a href="user.php"><i class="fas fa-search"></i></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>

    
    </div>
  </nav>

<h2>Keranjang Belanja</h2>
<form action="checkout.php" method="post">
  <table>
    <thead>
      <tr>
        <th><input type="checkbox" id="selectAll"></th>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Subtotal</th>
        <th>Waktu</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        
        <?php if (!$result) {
    echo "Query Error: " . mysqli_error($koneksi);
    exit;
}
while($row = mysqli_fetch_assoc($result)): ?>

          <tr>
            <td>
              <input 
                type="checkbox" 
                class="productCheckbox" 
                name="id_produk[]" 
                value="<?= htmlspecialchars($row['id_produk']) ?>" 
                data-price="<?= $row['price'] * $row['quantity'] ?>">
            </td>
            <td><img src="<?= $row['image_url'] ?>" alt="<?= $row['id_produk'] ?>" width="60"></td>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
            <td>
          <form method="post" action="update_keranjang.php" style="margin:0;">
           <input type="hidden" name="id_produk" value="<?= htmlspecialchars($row['id_produk']) ?>">
             <select name="quantity" onchange="this.form.submit()">
             <?php for ($i = 1; $i <= 10; $i++): ?>
             <option value="<?= $i ?>" <?= $i == $row['quantity'] ? 'selected' : '' ?>><?= $i ?></option>
              <?php endfor; ?>
              </select>
             </form>
            </td>

            <td>Rp <?= number_format($row['price'] * $row['quantity'], 0, ',', '.') ?></td>
            <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
            <td>
         <form method="post" action="hapus_keranjang.php" onsubmit="return confirm('Yakin hapus produk ini?');" style="display:inline;">
    <input type="hidden" name="id_produk" value="<?= htmlspecialchars($row['id_produk']) ?>">
    <button type="submit" style="background:#e74c3c; color:#fff; border:none; padding:5px 10px; border-radius:4px;">Hapus</button>
</form>

        </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">Keranjang kosong.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- ✅ Panel Checkout -->
  <div class="checkout-bar" id="checkoutBar" style="display: none;">
    <div class="total">Total: <span id="totalHarga">Rp 0</span></div>
    <button type="submit" id="checkoutBtn" class="checkout-button">Checkout</button>
  </div>
</form>



<script>
  const checkboxes = document.querySelectorAll('.productCheckbox');
  const totalHarga = document.getElementById('totalHarga');
  const checkoutBar = document.getElementById('checkoutBar');
  const selectAll = document.getElementById('selectAll');

  function updateTotal() {
    let total = 0;
    let anyChecked = false;
    checkboxes.forEach(cb => {
      if (cb.checked) {
        total += parseFloat(cb.dataset.price);
        anyChecked = true;
      }
    });
    totalHarga.innerText = 'Rp ' + total.toLocaleString('id-ID');
    checkoutBar.style.display = anyChecked ? 'flex' : 'none';
  }

  checkboxes.forEach(cb => cb.addEventListener('change', updateTotal));
  if (selectAll) {
  selectAll.addEventListener('change', function() {
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateTotal();
  });
}
</script>

</body>
</html>
