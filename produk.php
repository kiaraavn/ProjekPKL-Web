<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); 
    exit;
}
include 'koneksi.php';


$query = "SELECT p.id_produk, p.nama_produk, p.description, p.price, p.stok_quantity, p.kategori_id, p.image_url, p.created_at, p.updated_at, k.kategori_nama 
          FROM produk p
          LEFT JOIN kategori k ON p.kategori_id = k.kategori_id
          ORDER BY p.id_produk ASC";

$result = mysqli_query($koneksi, $query);

// untuk deskripsi
function potongDeskripsi($text, $limit = 50) {
    if(strlen($text) > $limit){
        return substr($text, 0, $limit) . '...';
    } else {
        return $text;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Daftar Produk</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      background-color: #fdfcf4;
      min-height: 100vh;
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
      margin-left: 250px;
      padding: 30px;
      background-color: #f4f6f9;
      min-height: 100vh;
    }

    h1 {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #333;
    }

     .btn-tambah {
      display: inline-block;
      padding: 10px 20px;
      background-color: #6a4029;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      margin-bottom: 20px;
    }
    .btn-tambah:hover {
      background-color: #8b5e3c;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    table thead {
      background-color: #343a40;
      color: white;
    }

    table th,
    table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
      vertical-align: middle;
    }

    table tbody tr:hover {
      background-color: #f1f1f1;
    }

    table img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    /* tombol edit dan hapus */
    .btn {
      display: inline-block;
      padding: 6px 14px;
      font-size: 14px;
      border-radius: 6px;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: background-color 0.3s ease;
      color: white;
      margin-right: 5px;
      font-weight: 600;
    }

    .btn-edit {
      background-color:rgb(85, 56, 56);
    }
    .btn-edit:hover {
      background-color:rgb(85, 56, 56);
    }

    .btn-hapus {
      background-color:rgb(85, 56, 56);
    }
    .btn-hapus:hover {
      background-color:rgb(85, 56, 56);
    }

    /* buat form hapus inline */
    form.inline {
      display: inline;
    }

    td[colspan="10"] {
      text-align: center;
      padding: 20px;
      color: #999;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>
     
  <div class="main-content">
    <h1>Daftar Produk</h1>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'hapus_berhasil'): ?>
  <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
    ✅ Produk berhasil dihapus.
  </div>
<?php endif; ?>
<a href="tambah_produk.php" class="btn-tambah">+ Tambah Produk</a>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Produk</th>
          <th>Deskripsi</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Kategori</th>
          <th>Gambar</th>
          <th>Dibuat</th>
          <th>Diupdate</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if(mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= htmlspecialchars($row['id_produk']) ?></td>
              <td><?= htmlspecialchars($row['nama_produk']) ?></td>
              <td><?= htmlspecialchars(potongDeskripsi($row['description'])) ?></td>
              <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
              <td><?= htmlspecialchars($row['stok_quantity']) ?></td>
              <td><?= htmlspecialchars($row['kategori_nama'] ?? 'Kategori tidak ada') ?></td>
              <td>
                <?php if($row['image_url']): ?>
                  <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
              <td><?= date('d M Y', strtotime($row['updated_at'])) ?></td>
              <td>
                <a href="edit_produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-edit">Edit</a>
                <form action="hapus_produk.php" method="post" class="inline" onsubmit="return confirm('Yakin mau hapus?')">
                  <input type="hidden" name="id" value="<?= $row['id_produk'] ?>">
                  <button type="submit" class="btn btn-hapus">Hapus</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="10">Tidak ada data produk.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
