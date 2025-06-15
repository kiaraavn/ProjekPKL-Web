<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
         body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
      color: #4d2c1d;
    }

   

    
    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 50px;
      background-color: #fcf3d7;
      border-bottom: 1px solid #ccc;
    }

    .nav-center ul {
      list-style: none;
      display: flex;
      gap: 30px;
      margin: 0;
      padding: 0;
    }

    .nav-center ul li a {
      text-decoration: none;
      color: #4d2c1d;
      font-weight: bold;
      font-size: 18px;
    }

    .nav-icons a {
      color: #4d2c1d;
      margin-left: 20px;
      font-size: 20px;
      text-decoration: none;
    }

   


    h2 {
      text-align: center;
      margin: 30px 0 10px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 20px;
      padding: 20px 40px;
    }

    .product-card {
      background-color: #fff;
      padding: 10px;
      border-radius: 10px;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
      cursor: pointer;
    }

    .product-card:hover {
      transform: scale(1.03);
    }

    .product-card img {
    width: 100%;         
    height: 250px;       
    object-fit: cover;   
    border-radius: 10px; 
    }


    .product-name {
      font-weight: bold;
      margin: 8px 0 5px;
    }

    .product-price {
      color: #a52a2a;
      font-size: 14px;
    }

    .rating {
      color: gold;
      font-size: 13px;
    }

    .site-footer {
  background-color: #633c1d;
  color: #fff;
  padding: 3rem 2rem 1rem;
  font-size: 0.95rem;
}

.footer-container {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: auto;
  gap: 2rem;
}

.footer-section {
  flex: 1;
  min-width: 200px;
}

.footer-section h3 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
  color: #fdd8a1;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 0.5rem;
}

.footer-section ul li a {
  color: #ffeccf;
  text-decoration: none;
}

.footer-section ul li a:hover {
  text-decoration: underline;
}

.sosmed-icons a img {
  width: 28px;
  margin-right: 10px;
  transition: transform 0.2s;
}

.sosmed-icons a:hover img {
  transform: scale(1.1);
}

.footer-bottom {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid #ffffff30;
  font-size: 0.85rem;
  color: #ffeacc;
}

    </style>
</head>
<body>
    <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">
        <a href="index.php">
          <img src="gambar/logo.png" alt="Adornee Co" style="height: 100px;">
        </a>
      </div>
      
    <div class="nav-center">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="shop1.php">Shop</a></li>
        <li><a href="about1.html" class="active">about</a></li>
      </ul>
    </div>
    <div class="nav-icons">
      <a href="#"><i class="fas fa-search"></i></a>
      <a href="user.php"><i class="fas fa-user"></i></a>
       <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
    </div>
  </nav>

  <script>
   const navLinks = document.querySelectorAll('ul li a');
    
    navLinks.forEach(link => {
      link.addEventListener('click', function() {
        // Menghapus kelas active dari semua link
        navLinks.forEach(link => link.classList.remove('active'));
        // Menambahkan kelas active ke link yang diklik
        this.classList.add('active');
      });
    });
    
  </script>
  
  
  

    
      <h2>Produk Sesuai di Cari</h2>
    
          <?php
        // bagian kategori ambil dari database
        $kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';


        if (!empty($kategori)){
      // ✅ Koneksi ke database
        $koneksi = new mysqli("localhost", "root", "", "db_adornee");

          // Cek koneksi
          if ($koneksi->connect_error) {
             die("Koneksi gagal: " . $koneksi->connect_error);
            }

// bagian kategori ambil dari produk
$sql = "SELECT * FROM produk WHERE kategori_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $kategori);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Produk Kategori: <?= htmlspecialchars($kategori) ?></h2>

<div class="product-grid">
<?php if ($result->num_rows > 0): ?>
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="product-card">
      <img src="<?= $row['image_url'] ?>" alt="<?= $row['nama_produk'] ?>">
      <div class="product-name"><?= $row['nama_produk'] ?></div>
      <div class="product-price">Rp<?= number_format($row['price'], 0, ',', '.') ?></div>
      <div class="rating">⭐⭐⭐⭐⭐</div>
      <a href="detail_produk.php?id=<?= $row['id_produk']; ?>" class="btn">Lihat Detail</a>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="grid-column: 1 / -1; text-align: center;">Tidak ada produk untuk kategori ini.</p>
<?php endif; ?>
</div>

<?php
$stmt->close();
$koneksi->close();
 
} else {
    echo "<p style='text-align:center;'>Kategori tidak ditemukan di URL.</p>";
}
?>




           

        
      

      <footer class="site-footer">
        <div class="footer-container">
      
          <div class="footer-section">
            <h3>Tentang Kami</h3>
            <p>AdorneeCo adalah toko online fashion yang menyediakan aksesoris dan produk trendi untuk kamu yang stylish!</p>
          </div>
      
          <div class="footer-section">
            <h3>Bantuan</h3>
            <ul>
              <li><a href="#">FAQ</a></li>
              <li><a href="#">Cara Pembelian</a></li>
              <li><a href="#">Pengembalian Barang</a></li>
            </ul>
          </div>
      
          <div class="footer-section">
            <h3>Ikuti Kami</h3>
            <div class="sosmed-icons">
              <a href="#"><img src="gambar/instagram.svg" alt="Instagram"></a>
              <a href="#"><img src="gambar/twitter.svg" alt="Twitter"></a>
              <a href="#"><img src="gambar/tiktok.svg" alt="Tiktok"></a>
            </div>
          </div>
      
        </div>
      
        <div class="footer-bottom">
          <p>&copy; 2025 AdorneeCo. All rights reserved.</p>
        </div>
      </footer>
</body>
</html>