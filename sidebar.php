<div class="sidebar">
  <div>
    <h2>Admin Panel</h2>
    <a href="admin1.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin1.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="produk.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : '' ?>">Daftar Produk</a>
    <a href="transaksi.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'transaksi.php' ? 'active' : '' ?>">Transaksi</a>
    <a href="laporan_pendapatan.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'laporan_pendapatan.php' ? 'active' : '' ?>">Laporan Pendapatan</a>
    <a href="data_akun.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'data_akun.php' ? 'active' : '' ?>">Data Akun</a>
    <a href="data_kategori.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'data_kategori.php' ? 'active' : '' ?>">Data Kategori</a>
    <a href="logout_admin.php" class="nav-link">Logout</a>
  </div>
  <div class="profile">
    <img src="gambar/kansa kiara.jpg" alt="User Profile">
    <div><strong>kiara kansa</strong></div>
    <small>admin</small>
  </div>
</div>
