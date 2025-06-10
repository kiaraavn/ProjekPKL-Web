<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); // Ganti sesuai nama file login
    exit;
}
include 'koneksi.php';

$query = "SELECT * FROM kategori ORDER BY kategori_id ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Kategori</title>
  <link rel="stylesheet" href="frontend.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4ed;
      margin: 0;
    }
    .container {
      margin-left: 280px;
      padding: 30px;
    }
    .sidebar h2 {
  color: white !important;
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
    
    
    
    .main-content {
      margin-left: 270px;
      padding: 30px 40px ;
    }

    h2 {
      color: #4d2c1d;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #4d2c1d;
      color: white;
    }
    .btn {
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 6px;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
    }
    .btn-tambah {
      background-color: #6b4226;
      margin-bottom: 15px;
      display: inline-block;
    }
    .btn-edit {
      background-color: #876c5d;
    }
    .btn-hapus {
      background-color: #a74444;
    }
    .btn:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
  <h2>Data Kategori Produk</h2>

  <a href="tambah_kategori.php" class="btn btn-tambah">+ Tambah Kategori</a>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama Kategori</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['kategori_id'] ?></td>
            <td><?= $row['kategori_nama'] ?></td>
            <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
            <td>
              <a href="edit_kategori.php?id=<?= $row['kategori_id'] ?>" class="btn btn-edit">Edit</a>
              <a href="hapus_kategori.php?id=<?= $row['kategori_id'] ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" style="text-align:center; color: #777;">Belum ada kategori.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
