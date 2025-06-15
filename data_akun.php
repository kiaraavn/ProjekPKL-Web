<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); 
}
include 'koneksi.php';

// Ambil semua user
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Akun</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #fdfcf4;
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

    h1 {
      font-size: 26px;
      margin-bottom: 20px;
      color: #4d2c1d;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    thead {
      background-color: #4d2c1d;
      color: #fff;
    }

    .btn-hapus {
      background-color: #a52a2a;
      color: #fff;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-size: 13px;
      cursor: pointer;
    }

    .btn-hapus:hover {
      background-color: #871f1f;
    }

    .alert-sukses {
      background-color: #d4edda;
      color: #155724;
      padding: 12px;
      border: 1px solid #c3e6cb;
      border-radius: 5px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <h1>Data Akun Pengguna</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'hapus_berhasil'): ?>
      <div class="alert-sukses">✅ User berhasil dihapus.</div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Tanggal Lahir</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if(mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= $row['user_id'] ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= $row['tanggal_lahir'] ?></td>
              <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
              <td>
                <a href="hapus_user.php?id=<?= $row['user_id'] ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; color: #999;">Tidak ada akun pengguna.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
