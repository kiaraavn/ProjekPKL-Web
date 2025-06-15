<?php
include "koneksi.php";

$result =  mysqli_query($koneksi,  "SELECT * FROM produk ORDER BY id_produk DESC LIMIT 18");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
      color: #4d2c1d;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #fff0c9;
      padding: 10px 40px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .logo {
      height: 95px;
      object-fit: contain;
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
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
      border-radius: 8px;
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

    .banner-duo {
  display: flex;
  gap: 20px;
  padding: 40px;
  justify-content: center;
  flex-wrap: wrap;
}

.banner-item {
  position: relative;
  width: 45%;
  cursor: pointer;
  overflow: hidden;
  border-radius: 16px;
  transition: transform 0.3s ease;
}

.banner-item img {
  width: 100%;
  height: auto;
  display: block;
  border-radius: 16px;
}

.banner-item:hover {
  transform: scale(1.02);
}

.text-overlay {
  position: absolute;
  bottom: 20px;
  left: 30px;
  font-size: 22px;
  font-weight: bold;
  color: white;
  text-shadow: 0 0 6px rgba(0,0,0,0.5);
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
   <nav class="navbar">
    <div class="logo">
        <a href="index.html">
          <img src="gambar/logo.png" alt="adorn_n_co-removebg-preview.png" style="height: 100px;">
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
  

    
  <h2>Shop Your Favorite Brands ✮✮</h2>

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

            <section class="banner-duo">
    <div class="banner-item" onclick="location.href='shop.html?kategori=wanita'">
      <img src="gambar/banner2.png" alt="Model Wanita" />
      <div class="text-overlay">Tampil Stylish</div>
    </div>
    <div class="banner-item" onclick="location.href='shop.html?kategori=pria'">
      <img src="gambar/banner1.jpeg" alt="Model Pria" />
      <div class="text-overlay">Gaya Kerenmu</div>
    </div>
  </section>

    <h2>Mix & Match Kategori Pilihanmu!</h2>
  <section class="kategori-banner">
    <div class="kategori-item">
      <img src="gambar/model1.jpeg" alt="Kategori Akseroris Kepala">
      <a href="kategori.php?kategori=3" class="kategori-btn">Shop Now</a>
    </div>
    <div class="kategori-item">
      <img src="gambar/model2.jpeg" alt="Kategori Aksesoris Kepala">
      <a href="kategori.php?kategori=3" class="kategori-btn">Shop Now</a>
    </div>
    <div class="kategori-item">
      <img src="gambar/model3.jpeg" alt="Kategori Aksesoris Kepala">
      <a href="kategori.php?kategori=3" class="kategori-btn">Shop Now</a>
    </div>
    <div class="kategori-item">
        <img src="gambar/model4.jpeg" alt="Kategori Bag">
        <a href="kategori.php?kategori=1" class="kategori-btn">Shop Now</a>
      </div>
  </section>

         <section class="new-product">
    <div class="container">
      <h2 class="title">Produk Terbaru</h2>
      <div class="product-grid">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <div class="product-card">
            <img src="<?= $row['image_url'] ?>" alt="<?= $row['nama_produk']; ?>">
            <h3><?= $row['nama_produk']; ?></h3>
            <p>Rp <?= number_format($row['price'], 0, ',', '.'); ?></p>
            <div class="rating">⭐⭐⭐⭐⭐</div>
            <a href="detail_produk.php?id=<?= $row['id_produk']; ?>" class="btn">Lihat Detail</a>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>


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