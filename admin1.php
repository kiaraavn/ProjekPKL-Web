<?php
session_start();


$koneksi = new mysqli("localhost", "root", "", "db_adornee");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}


if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}


// Jumlah kategori
$resultKategori = $koneksi->query("SELECT COUNT(*) as total FROM kategori");
$rowKategori = $resultKategori->fetch_assoc();
$totalKategori = $rowKategori['total'];

// Jumlah produk
$resultProduk = $koneksi->query("SELECT COUNT(*) as total FROM produk");
$rowProduk = $resultProduk->fetch_assoc();
$totalProduk = $rowProduk['total'];

// Jumlah admin
$resultAdmin = $koneksi->query("SELECT COUNT(*) as total FROM admin");
$rowAdmin = $resultAdmin->fetch_assoc();
$totalAdmin = $rowAdmin['total'];

// Jumlah pelanggan
$resultUsers = $koneksi->query("SELECT COUNT(*) as total FROM users");
$rowUsers = $resultUsers->fetch_assoc();
$totalUsers = $rowUsers['total'];

// Jumlah transaksi
$resultPayments = $koneksi->query("SELECT COUNT(*) as total FROM payments");
$rowPayments = $resultPayments->fetch_assoc();
$totalPayments = $rowPayments['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      display: flex; min-height: 100vh;
      background-color: #fdfcf4;
    }
    .sidebar {
      width: 250px; background-color: #3e2723; color: white;
      display: flex; flex-direction: column; justify-content: space-between;
      padding: 20px;
    }
    .sidebar h2 { margin-bottom: 30px; }
    .nav-link {
      color: white; text-decoration: none;
      padding: 10px 15px; border-radius: 8px;
      margin-bottom: 10px; display: block;
      transition: background 0.3s ease;
    }
    .nav-link:hover, .nav-link.active { background-color: #5d4037; }
    .profile {
      text-align: center; margin-top: 30px; font-size: 14px;
    }
    .profile img {
      width: 40px; height: 40px; border-radius: 50%;
      margin-bottom: 5px;
    }
    .content {
      flex-grow: 1; padding: 40px;
    }
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }
    .card {
      background-color: white; border-radius: 12px;
      box-shadow: 0 2px 8px rgba(114, 73, 73, 0.08);
      padding: 20px; text-align: center;
      transition: transform 0.2s ease;
    }
    .card:hover { transform: translateY(-5px); }
    .card .icon {
      font-size: 32px; margin-bottom: 10px; color:rgb(90, 61, 61);
    }
    .card h2 {
      font-size: 24px; margin-bottom: 5px;
    }
    .card p {
      color: #666; font-size: 14px;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <div>
      <h2>Admin Panel</h2>
      <a href="admin1.php" class="nav-link active">Dashboard</a>
      <a href="produk.php" class="nav-link">Daftar Produk</a>
      <a href="transaksi.php" class="nav-link">Transaksi</a>
      <a href="data_akun.php" class="nav-link">Data Akun</a>
      <a href="data_kategori.php" class="nav-link">Data Kategori</a>
      <a href="logout_admin.php" class="nav-link">Logout</a>

    </div>

    <div class="profile">
      <img src="kansa kiara.jpg" alt="User Profile" />
      <div><strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong></div>
      <small>admin</small>
    </div>
  </div>

  <div class="content">
    <h1 style="margin-bottom: 30px;">Dashboard</h1>
    <div class="dashboard-cards">
      <div class="card">
        <i class="fas fa-th-large icon"></i>
        <h2><?php echo $totalKategori; ?></h2>
        <p>Jumlah Kategori</p>
      </div>
      <div class="card">
        <i class="fas fa-cubes icon"></i>
        <h2><?php echo $totalProduk; ?></h2>
        <p>Jumlah Produk</p>
      </div>
      <div class="card">
        <i class="fas fa-user-shield icon"></i>
        <h2><?php echo $totalAdmin; ?></h2>
        <p>Jumlah Admin</p>
      </div>
      <div class="card">
        <i class="fas fa-users icon"></i>
        <h2><?php echo $totalUsers; ?></h2>
        <p>Jumlah Pelanggan</p>
      </div>
      <div class="card">
        <i class="fas fa-shopping-cart icon"></i>
        <h2><?php echo $totalPayments; ?></h2>
        <p>Jumlah Transaksi</p>
      </div>
    </div>
  </div>

</body>
</html>
