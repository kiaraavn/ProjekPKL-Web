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

    nav ul {
      list-style: none;
      display: flex;
      gap: 30px;
    }

    nav ul li {
      display: inline;
    }

    nav ul li a {
      text-decoration: none;
      color: #4d2c1d;
      font-weight: bold;
    }

    .search-box {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 1rem;
}

.search-box input {
  padding: 10px 15px;
  border-radius: 20px;
  border: 1px solid #ccc;
  font-size: 14px;
  width: 300px;
  outline: none;
}

.icons {
  display: flex;
  align-items: center;
  gap: 15px;
}

.icons a {
  text-decoration: none;
  color: #4a3a0c; 
  font-size: 18px;
}

.icons li {
  list-style: none;
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
    <header>
        <img src="gambar/logo.png" alt="Logo AdorneeCo" class="logo" />
        <nav>
          <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="shop.html">Shop</a></li>
            <li><a href="about.html">About</a></li>
          </ul>
        </nav>
        <div class="search-box">
          <input type="text" placeholder="Cari produk, tren, dan merek...">
        </div>
        
        <div class="icons">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <li><a href="heart.html"><i class="fas fa-heart"></i></a>
            <li><a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
            <li><a href="user.html"><i class="fas fa-profil"></i></a>
        </div>
      </header>
    
      <h2>Produk Sesuai di Cari</h2>
    
          <?php
        // ✅ Ambil kategori dari URL
        $kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

      // ✅ Koneksi ke database
        $koneksi = new mysqli("localhost", "root", "", "db_adornee");

          // Cek koneksi
          if ($koneksi->connect_error) {
             die("Koneksi gagal: " . $koneksi->connect_error);
            }

// ✅ Query ambil produk berdasarkan kategori
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
      <img src="uploads/<?= $row['gambar'] ?>" alt="<?= $row['nama_produk'] ?>">
      <div class="product-name"><?= $row['nama_produk'] ?></div>
      <div class="product-price">Rp<?= number_format($row['harga'], 0, ',', '.') ?></div>
      <div class="rating">⭐⭐⭐⭐⭐</div>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="grid-column: 1 / -1; text-align: center;">Tidak ada produk untuk kategori ini.</p>
<?php endif; ?>
</div>

<?php
$stmt->close();
$koneksi->close();
?>




           

          <script>
            const urlParams = new URLSearchParams(window.location.search);
            const kategori = urlParams.get("kategori");
          
            document.querySelector("h2").textContent = "Produk Kategori: " + kategori;
          
            const semuaProduk = document.querySelectorAll(".produk");
            semuaProduk.forEach((produk) => {
              if (produk.dataset.kategori !== kategori) {
                produk.style.display = "none";
              }
            });
          </script>
      

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