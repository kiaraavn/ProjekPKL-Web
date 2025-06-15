<?php
session_start();
include 'koneksi.php';

// Cek session login admin (bisa kamu sesuaikan)
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $price = floatval($_POST['price']);
    $stok_quantity = intval($_POST['stok_quantity']);
    $kategori_id = intval($_POST['kategori_id']);

    // Handle file upload gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];

        
        $upload_dir = '';
        $image_new_name = time() . '_' . basename($image_name);
        $upload_path = $upload_dir . $image_new_name;

        if (move_uploaded_file($image_tmp, $upload_path)) {
           
            $query = "INSERT INTO produk (nama_produk, description, price, stok_quantity, kategori_id, image_url, created_at, updated_at) 
                      VALUES ('$nama_produk', '$description', $price, $stok_quantity, $kategori_id, '$image_new_name', NOW(), NOW())";

            if (mysqli_query($koneksi, $query)) {
                header("Location: produk.php?msg=success");
                exit;
            } else {
                $error = "Gagal menambahkan produk: " . mysqli_error($koneksi);
            }
        } else {
            $error = "Gagal mengupload gambar.";
        }
    } else {
        $error = "Mohon pilih gambar produk.";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Tambah Produk</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Contoh styling sederhana sesuai style sebelumnya */
    .container {
      max-width: 600px;
      margin: 50px auto;
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 0 10px #ccc;
    }
    label {
      display: block;
      margin: 15px 0 5px;
      font-weight: bold;
      color: #8b5e3c;
    }
    input[type="text"], input[type="number"], textarea, select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      background-color: #8b5e3c;
      color: white;
      border: none;
      padding: 12px 25px;
      margin-top: 20px;
      cursor: pointer;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #6e482a;
    }
    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Tambah Produk Baru</h2>

  <?php if ($error): ?>
    <p class="error"><?= $error ?></p>
  <?php endif; ?>

  <form action="tambah_produk.php" method="post" enctype="multipart/form-data">
    <label for="nama_produk">Nama Produk</label>
    <input type="text" id="nama_produk" name="nama_produk" required />

    <label for="description">Deskripsi</label>
    <textarea id="description" name="description" rows="4" required></textarea>

    <label for="price">Harga</label>
    <input type="number" id="price" name="price" step="0.01" min="0" required />

    <label for="stok_quantity">Stok</label>
    <input type="number" id="stok_quantity" name="stok_quantity" min="0" required />

    <label for="kategori_id">Kategori</label>
    <select id="kategori_id" name="kategori_id" required>
      <option value="">-- Pilih Kategori --</option>
      <?php
      // Ambil kategori dari DB
      $kategori_result = mysqli_query($koneksi, "SELECT * FROM kategori");
      while ($row = mysqli_fetch_assoc($kategori_result)) {
          echo "<option value='{$row['kategori_id']}'>{$row['kategori_nama']}</option>";
      }
      ?>
    </select>

    <label for="image">Gambar Produk</label>
    <input type="file" id="image" name="image" accept="" required />

    <button type="submit">Tambah Produk</button>
  </form>
</div>

</body>
</html>
