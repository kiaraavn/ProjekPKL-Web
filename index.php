<?php
include 'koneksi.php';

$result = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id_produk DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AdorneeCo</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f7ecc8;
      color: #4d2c1d;
    }

    .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
    }

    .navbar {
      background-color: #fef3d5;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .logo {
     height: 95px;
      object-fit: contain;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      color: #633c1d;
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


    .hero {
      position: relative;
      width: 100%;
      height: 400px;
      overflow: hidden;
    }

    .hero-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .shop-btn {
      position: absolute;
      bottom: 20px;
      left: 30px;
      background-color: #9e4421;
      color: white;
      padding: 10px 20px;
      border: none;
      font-weight: bold;
      cursor: pointer;
      border-radius: 8px;
    }

    .fitur-layanan {
  display: flex;
  justify-content: center;
  gap: 3rem;
  padding: 3rem 2rem 2rem;
  background-color: #fff1dc;
  text-align: center;
}

.layanan-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 150px;
}

.layanan-item img {
  width: 60px;
  height: 60px;
  margin-bottom: 1rem;
}

.layanan-item p {
  color: #633c1d;
  font-weight: 500;
  font-size: 1rem;
}


    .kategori-banner {
  display: flex;
  justify-content: center;
  gap: 2rem;
  padding: 3rem 2rem;
  background-color: #fff8ec;
}

.kategori-item {
  position: relative;
  width: 250px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.kategori-item:hover {
  transform: translateY(-5px);
}

.kategori-item img {
  width: 100%;
  height: auto;
  display: block;
}

.kategori-btn {
  position: absolute;
  bottom: 15px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #a0522d;
  color: white;
  padding: 0.7rem 1.4rem;
  border: none;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.3s;
}

.kategori-btn:hover {
  background-color: #84401d;
}


    .products-section {
  padding: 3rem 2rem;
  background-color: #fffaf0;
  text-align: center;
}

.products-section h2 {
  font-size: 2rem;
  color: #633c1d;
  margin-bottom: 2rem;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  max-width: 1100px;
  margin: auto;
}

.product-card {
  background: #fff;
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
  transition: transform 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
}

.product-card img {
  width: 100%;
  border-radius: 8px;
  object-fit: cover;
}

.product-card h3 {
  margin-top: 1rem;
  color: #333;
}

.product-card p {
  color: #a0522d;
  font-weight: bold;
}

.product {
  width: 200px;
  text-align: center;
  margin: 10px;
  transition: transform 0.3s ease;
}

.product img {
  width: 100%;
  border-radius: 12px;
}

.product:hover {
  transform: scale(1.05);
}

.product h4 {
  margin: 10px 0 5px;
  font-size: 16px;
  color: #3e1f12;
}

.product p {
  color: #7d3c2d;
}

h2 {
      text-align: center;
      margin: 30px 0 10px;
    }
/* CSS */
.banner-wrapper {
  display: flex;
  justify-content: center;  /* tengah secara horizontal */
  align-items: center;      /* tengah secara vertikal (kalau dibutuhkan) */
  width: 100%;
  padding: 20px 0;
  background-color: #fff8e7; /* opsional: latar belakang biar bersih */
}

.banner-container {
  max-width: 1200px;
  width: 90%;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.banner-image {
  width: 100%;
  height: auto;
  display: block;
  object-fit: cover;
}

/* CSS */
.banner-wrapper {
  display: flex;
  justify-content: center;
  padding: 20px 0;
  background-color: #fff8e7;
}

.banner-container {
  position: relative;
  max-width: 1200px;
  width: 90%;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.banner-image {
  width: 100%;
  height: auto;
  display: block;
  object-fit: cover;
}

.banner-button-wrapper {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
}

.shop-button {
  background-color: #ff6600;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 30px;
  font-size: 16px;
  text-decoration: none;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  transition: background-color 0.3s ease;
}

.shop-button:hover {
  background-color: #e65c00;
}



.brand-section {
  padding: 4rem 2rem;
  background-color: #fffaf0;
  text-align: center;
}

.brand-section h2 {
  font-size: 1.8rem;
  color: #633c1d;
  margin-bottom: 2rem;
}

.brand-logos {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.brand-logos img {
  max-width: 120px;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.brand-logos img:hover {
  opacity: 1;
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
          <img src="gambar/logo.png" alt="Aornee Co" style="height: 100px;">
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
  
 
  

  <!-- Hero Section -->
  <section class="hero">
    <img src="gambar/banner.png" alt="Banner Aksesoris" class="hero-img">
    <button class="shop-btn">Shop Now</button>
  </section>

  <!-- Layanan -->
  <section class="fitur-layanan">
    <div class="layanan-item">
      <img src="gambar/logoT.png" alt="Gratis Ongkir">
      <p>Enjoy free shiping on all orders over $100</p>
    </div>
    <div class="layanan-item">
      <img src="gambar/coin.png" alt="Bayar di Tempat">
      <p>Easy return & exchanges</p>
    </div>
    <div class="layanan-item">
      <img src="gambar/check.png" alt="Customer Service">
      <p>CS 24/7</p>
    </div>
    <div class="layanan-item">
        <img src="gambar/mirror.png" alt="Quality">
        <p>Quality that enchances your natural beauty</p>
      </div>
  </section>
  

    <!-- Kategori -->
  <section class="kategori-banner">
    <div class="kategori-item">
      <img src="gambar/kategoriT.jpeg" alt="Tas">
      <a href="kategori.php?kategori=1" class="kategori-btn">Shop Now</a>
    </div>
    <div class="kategori-item">
      <img src="gambar/kategoriW.jpeg" alt="Aksesoris Tangan">
      <a href="kategori.php?kategori=2" class="kategori-btn">Shop Now</a>
    </div>
    <div class="kategori-item">
      <img src="gambar/kategoriH.jpeg" alt=" Aksesoris Kepala">
      <a href="kategori.php?kategori=3" class="kategori-btn">Shop Now</a>
    </div>
  </section>
  
<!-- Produk -->
    <section class="new-product">
    <div class="container">
      <h2 class="title">Produk Terbaru</h2>
      <div class="product-grid">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <div class="product-card">
            <img src="<?= $row['image_url'] ?>" alt="<?= $row['nama_produk']; ?>">
            <h3><?= $row['nama_produk']; ?></h3>
            <p>Rp <?= number_format($row['price'], 0, ',', '.'); ?></p>
            <a href="detail_produk.php?id=<?= $row['id_produk']; ?>" class="btn">Lihat Detail</a>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- HTML -->
   <h2>Curated Just for You</h2>
<div class="banner-wrapper">
  <div class="banner-container">
    <img src="gambar/flashsale.png" alt="Flash Sale Banner" class="banner-image">
    <div class="banner-button-wrapper">
      <a href="shop1.php" class="shop-button">Shop Now</a>
    </div>
  </div>
</div>



  
  <!-- Brand -->
  <section class="brand-section">
    <h2>Brand Favorit Kami</h2>
    <div class="brand-logos">
      <img src="gambar/channel.png" alt="Chanel">
      <img src="gambar/ysl.png" alt="YSL">
      <img src="gambar/lv.png" alt="Hermes">
      <img src="gambar/dior.png" alt="Gucci">
      <img src="gambar/celine.png" alt="Celine">
      <img src="gambar/hermes.png" alt="Hermes">
      <!-- Tambah brand lain sesuai desain -->
    </div>
  </section>

  <!-- Footer -->
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
