<?php
include 'koneksi.php';

// Cek kalau id produk ada di query string
if (!isset($_GET['id'])) {
    header('Location: produk.php');
    exit;
}

$id = intval($_GET['id']);

// Ambil data produk berdasarkan id
$query = "SELECT * FROM produk WHERE id_produk = $id";
$result = mysqli_query($koneksi, $query);
if (mysqli_num_rows($result) == 0) {
    echo "Produk tidak ditemukan.";
    exit;
}

$produk = mysqli_fetch_assoc($result);

// Ambil data kategori untuk dropdown
$kategoriResult = mysqli_query($koneksi, "SELECT * FROM kategori");

// Kalau form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['description']);
    $harga = floatval($_POST['price']);
    $stok = intval($_POST['stok_quantity']);
    $kategori_id = intval($_POST['kategori_id']);

    // Handling upload gambar (optional)
    $image_url = $produk['image_url']; // default pakai gambar lama
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        // Buat folder uploads dulu kalau belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $tmp_name = $_FILES['image']['tmp_name'];
        $filename = basename($_FILES['image']['name']);
        $target_file = $target_dir . time() . "_" . $filename;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $image_url = $target_file;
        }
    }

    $updated_at = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE produk SET 
        nama_produk = '$nama',
        description = '$deskripsi',
        price = $harga,
        stok_quantity = $stok,
        kategori_id = $kategori_id,
        image_url = '$image_url',
        updated_at = '$updated_at'
        WHERE id_produk = $id";

    if (mysqli_query($koneksi, $updateQuery)) {
        header("Location: produk.php?msg=update_berhasil");
        exit;
    } else {
        $error = "Gagal update produk: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Produk</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
      background: #f5f5f5;
    }
    form {
      background: white;
      padding: 20px;
      max-width: 600px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input[type=text], input[type=number], textarea, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    button {
      margin-top: 20px;
      padding: 10px 15px;
      background-color: #3e2723;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
    }
    .error {
      background: #f8d7da;
      color: #842029;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
    }
    img.preview {
      margin-top: 10px;
      max-width: 150px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>

<h2>Edit Produk</h2>

<?php if (isset($error)): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
  <label for="nama_produk">Nama Produk:</label>
  <input type="text" id="nama_produk" name="nama_produk" required value="<?= htmlspecialchars($produk['nama_produk']) ?>">

  <label for="description">Deskripsi:</label>
  <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($produk['description']) ?></textarea>

  <label for="price">Harga:</label>
  <input type="number" id="price" name="price" required value="<?= htmlspecialchars($produk['price']) ?>" min="0" step="0.01">

  <label for="stok_quantity">Stok:</label>
  <input type="number" id="stok_quantity" name="stok_quantity" required value="<?= htmlspecialchars($produk['stok_quantity']) ?>" min="0">

  <label for="kategori">Kategori:</label>
  <select id="kategori" name="kategori_id" required>
    <option value="">-- Pilih Kategori --</option>
    <?php while($kategori = mysqli_fetch_assoc($kategoriResult)): 
      $selected = ($kategori['kategori_id'] == $produk['kategori_id']) ? 'selected' : '';
    ?>
      <option value="<?= $kategori['kategori_id'] ?>" <?= $selected ?>><?= htmlspecialchars($kategori['kategori_nama']) ?></option>
    <?php endwhile; ?>
  </select>

  <label for="image">Ganti Gambar (biarkan kosong jika tidak ingin mengubah):</label>
  <input type="file" id="image" name="image" accept="image/*">
  <?php if($produk['image_url']): ?>
    <img src="<?= htmlspecialchars($produk['image_url']) ?>" alt="Preview Gambar" class="preview" />
  <?php endif; ?>

  <button type="submit">Update Produk</button>
</form>

</body>
</html>
