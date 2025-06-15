<?php
session_start();
include 'koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['submit_edit_review'])) {
  $review_id = intval($_POST['review_id']);
  $rating = intval($_POST['rating']);
  $comment = mysqli_real_escape_string($koneksi, $_POST['comment']);
  $user_id = $_SESSION['user_id'];

  $cekQuery = mysqli_query($koneksi, "SELECT * FROM review WHERE review_id = $review_id AND user_id = $user_id");
  if (mysqli_num_rows($cekQuery) > 0) {
    $update = mysqli_query($koneksi, "UPDATE review SET rating = $rating, comment = '$comment', tanggal_comment = NOW() WHERE review_id = $review_id");
  }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (isset($_POST['edit_review_btn'])) {
  $edit_id = $_POST['edit_id'];
  $edit_rating = $_POST['edit_rating'];
  $edit_comment = $_POST['edit_comment'];
  $tanggal_comment = date('Y-m-d');

  $query = "UPDATE reviews SET rating = '$edit_rating', comment = '$edit_comment', tanggal_comment = '$tanggal_comment' 
            WHERE review_id = '$edit_id' AND user_id = '{$_SESSION['user_id']}'";
  mysqli_query($koneksi, $query);
}

if (isset($_POST['hapus_review_btn'])) {
  $hapus_id = isset($_POST['hapus_id']) ? $_POST['hapus_id'] : null;
  if ($hapus_id) {
    $query = "DELETE FROM reviews WHERE review_id = '$hapus_id' AND user_id = '{$_SESSION['user_id']}'";
    mysqli_query($koneksi, $query);
    header("Location: detail_produk.php?id=" . $_GET['id']); 
    exit;
  }
}

$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
  echo "Produk tidak ditemukan.";
  exit;
}

if (isset($_POST['submit_review'])) {
  $user_id = $_SESSION['user_id']; 
  $rating = intval($_POST['rating']);
  $comment = $_POST['comment'];
  $id_produk = $id;

  $query = "INSERT INTO reviews (user_id, id_produk, rating, comment, tanggal_comment)
              VALUES ('$user_id', '$id_produk', '$rating', '$comment', CURDATE())";

    if (mysqli_query($koneksi, $query)) {
        header("Location: detail_produk.php?id=$id");
        exit;
    } else {
        echo "Gagal menyimpan review: " . mysqli_error($koneksi);
    }
}

$reviewQuery = mysqli_query($koneksi, "
  SELECT r.*, u.username 
  FROM reviews r
  LEFT JOIN users u ON r.user_id = u.user_id
  WHERE r.id_produk = $id
  ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $produk['nama_produk'] ?> - Detail Produk</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #fdfcf4;
      color: #4d2c1d;
    }
    .container {
      display: flex;
      padding: 60px 80px;
      gap: 60px;
    }

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

    .left img {
      width: 400px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .right {
      flex: 1;
    }
    .product-name {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 15px;
    }
    .price {
      font-size: 22px;
      color: #a0522d;
      margin-bottom: 20px;
    }
    .btn {
      background-color: #4d2c1d;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: bold;
      margin-top: 20px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background-color: #5e3c28;
    }
    
    .review-box {
      background-color: #fff8e1; 
      border: 1px solid #e0c9a6;
      border-radius: 10px;
      padding: 20px;
      margin-top: 30px;
      color: #4d2c1d;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .review-box h3 {
      font-size: 20px;
      margin-bottom: 15px;
      color: #4d2c1d;
      border-bottom: 2px solid #d4b38c;
      padding-bottom: 5px;
    }

    .review-item {
      margin-bottom: 15px;
      padding-bottom: 10px;
    }

    .review-item strong {
      color: #4d2c1d;
      font-weight: bold;
    }

    .review-item span {
      color: #ff9800;
      font-weight: bold;
      margin-left: 5px;
    }

    .review-item p {
      margin: 5px 0;
      line-height: 1.4;
    }

    .review-item small {
      color: #888;
      font-size: 0.85rem;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
      background-color: #fff8e7;
      margin: 10% auto;
      padding: 25px;
      border: 1px solid #bfa277;
      border-radius: 10px;
      width: 400px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      position: relative;
    }

    .close {
      position: absolute;
      top: 12px; right: 18px;
      font-size: 24px;
      color: #5d4037;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: #a0522d;
    }

    .modal-content input,
    .modal-content select,
    .modal-content textarea {
      width: 100%;
      padding: 8px;
      margin-top: 8px;
      margin-bottom: 16px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-family: 'Segoe UI', sans-serif;
    }

    .modal-content button {
      background-color: #5d4037;
      color: white;
      padding: 8px 18px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .modal-content button:hover {
      background-color: #8b5e3c;
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="logo">
        <a href="index.php">
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

  <div class="container">
    <div class="left">
      <img src="<?= $produk['image_url'] ?>" alt="<?= $produk['nama_produk'] ?>">
    </div>
    <div class="right">
      <div class="product-name"><?= $produk['nama_produk'] ?></div>
      <div class="price">Rp<?= number_format($produk['price'], 0, ',', '.') ?></div>
 
      <!-- BAGIAN JUMLAH -->
      <div style="margin-bottom: 15px;">
        <label for="jumlah"><strong>Jumlah:</strong></label>
        <select name="jumlah" id="jumlah" style="margin-left: 10px; padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
          <?php for ($i=1; $i<=10; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <!-- FORM BELI SEKARANG - LANGSUNG KE CHECKOUT -->
      <form action="checkout1.php" method="post" style="display:inline-block;">
        <input type="hidden" name="beli_langsung" value="1">
        <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
        <input type="hidden" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>">
        <input type="hidden" name="price" value="<?= $produk['price'] ?>">
        <input type="hidden" name="image_url" value="<?= $produk['image_url'] ?>">
        <input type="hidden" name="quantity" id="jumlahInputCheckout" value="1">
        <button type="submit" class="btn" style="background-color:#8b5e3c;">
          <i class="fas fa-bolt"></i> Beli Sekarang
        </button>
      </form>

      <hr style="border: none; border-top: 2px solid #333; width: 100%; margin: 20px 0;">

      <!-- Informasi Produk -->
      <section style="margin-top: 30px;">
        <h3>Informasi Produk</h3>
        <p><?= nl2br(htmlspecialchars($produk['description'])) ?></p>
      </section>

      <!-- Ulasan Produk -->
      <section style="margin-top: 40px;">
        <h3>Ulasan Pembeli</h3>
        <?php if (mysqli_num_rows($reviewQuery) > 0) : ?>
          <?php while ($rev = mysqli_fetch_assoc($reviewQuery)) : ?>
            <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
              <strong><?= htmlspecialchars($rev['username'] ?? 'User') ?></strong>
              <span style="color: gold;"><?= str_repeat('⭐', $rev['rating']) ?></span><br>
              <small style="color: #999;"><?= date('d M Y', strtotime($rev['tanggal_comment'])) ?></small>
              <p style="margin-top: 6px;"><?= htmlspecialchars($rev['comment']) ?></p>

              <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rev['user_id']) : ?>
                <button onclick="openEditModal('<?= $rev['review_id'] ?>', '<?= $rev['rating'] ?>', `<?= htmlspecialchars($rev['comment']) ?>`)" style="padding: 4px 10px; margin-top: 6px; margin-right: 6px;">
                  Edit
                </button>
                <form method="post" style="display: inline;">
                  <input type="hidden" name="hapus_id" value="<?= $rev['review_id'] ?>">
                  <button type="submit" name="hapus_review_btn" onclick="return confirm('Yakin mau hapus review ini?')" style="padding: 4px 10px;">Hapus</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        <?php else : ?>
          <p style="color: #888;">Belum ada ulasan untuk produk ini.</p>
        <?php endif; ?>
      </section>

      <?php if (isset($_SESSION['user_id'])) : ?>
        <div style="margin-top: 40px;">
          <h3>Beri Ulasan Produk Ini</h3>
          <form method="post">
            <label for="rating">Rating:</label><br>
            <select name="rating" id="rating" required>
              <option value="">Pilih rating</option>
              <option value="5">⭐️⭐️⭐️⭐️⭐️ (5)</option>
              <option value="4">⭐️⭐️⭐️⭐️ (4)</option>
              <option value="3">⭐️⭐️⭐️ (3)</option>
              <option value="2">⭐️⭐️ (2)</option>
              <option value="1">⭐️ (1)</option>
            </select><br><br>

            <label for="comment">Komentar:</label><br>
            <textarea name="comment" id="comment" rows="4" style="width: 100%;" required></textarea><br><br>

            <button type="submit" name="submit_review" style="padding: 8px 18px; background-color: #5d4037; color: white; border: none; border-radius: 6px; cursor: pointer;">
              Kirim Ulasan
            </button>
          </form>
        </div>
      <?php else : ?>
        <p style="margin-top: 30px; color: #a0522d;">Login dulu untuk memberikan ulasan.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal Edit Review -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEditModal()">&times;</span>
      <h3>Edit Ulasan</h3>
      <form method="post">
        <input type="hidden" name="edit_id" id="edit_id">

        <label for="edit_rating">Rating:</label><br>
        <select name="edit_rating" id="edit_rating" required>
          <option value="5">⭐️⭐️⭐️⭐️⭐️ (5)</option>
          <option value="4">⭐️⭐️⭐️⭐️ (4)</option>
          <option value="3">⭐️⭐️⭐️ (3)</option>
          <option value="2">⭐️⭐️ (2)</option>
          <option value="1">⭐️ (1)</option>
        </select><br><br>

        <label for="edit_comment">Komentar:</label><br>
        <textarea name="edit_comment" id="edit_comment" rows="4" required></textarea><br><br>

        <button type="submit" name="edit_review_btn">Update</button>
        <button type="button" onclick="closeEditModal()" style="margin-left: 10px; background:#ccc;">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const jumlahSelect = document.getElementById('jumlah');
      
      // Set nilai awal
      document.getElementById('jumlahInputCheckout').value = jumlahSelect.value;
      
      // Update quantity saat dropdown berubah
      jumlahSelect.addEventListener('change', function() {
        document.getElementById('jumlahInputCheckout').value = this.value;
      });
    });

    function openEditModal(reviewId, rating, comment) {
      document.getElementById('edit_id').value = reviewId;
      document.getElementById('edit_rating').value = rating;
      document.getElementById('edit_comment').value = comment;
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }
  </script>
</body>
</html>
