<?php

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama']);

  if (!empty($nama)) {
    $stmt = $koneksi->prepare("INSERT INTO kategori (kategori_nama) VALUES (?)");
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    header("Location: data_kategori.php");
    exit;
  } else {
    $error = "Nama kategori tidak boleh kosong!";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Tambah Kategori</title>
  <link rel="stylesheet" href="frontend.css">
  <style>
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
    input, button { 
        padding: 10px; 
        margin-top: 10px; 
        width: 100%; 
        max-width: 400px; 
    }
    label { 
        display: block; 
        margin-top: 15px; 
    }
    .btn { 
        background: #4d2c1d; 
        color: white; 
        border: none; 
        cursor: pointer; 
        border-radius: 5px; 
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  <div class="container">
    <h2>Tambah Kategori</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
      <label for="nama">Nama Kategori:</label>
      <input type="text" id="nama" name="nama" required>
      <button type="submit" class="btn">Simpan</button>
    </form>
  </div>
</body>
</html>
