<?php
session_start();
include 'koneksi.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lacak Pesanan</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f1ed;
      margin: 0;
      padding: 0;
      color: #4b3b2b;
    }

    /* Navigation Bar */
    .navbar {
      background: #fff;
      border-bottom: 1px solid #e5d4c1;
      padding: 15px 0;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .navbar-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
    }

    .navbar-left {
      display: flex;
      gap: 30px;
    }

    .navbar-left a {
      color: #8b5e3c;
      text-decoration: none;
      font-weight: 500;
      font-size: 16px;
      transition: color 0.3s;
    }

    .navbar-left a:hover {
      color: #a4744a;
    }

    .navbar-right {
      display: flex;
      gap: 15px;
      align-items: center;
    }

    .navbar-icon {
      width: 24px;
      height: 24px;
      color: #8b5e3c;
      cursor: pointer;
      transition: color 0.3s;
    }

    .navbar-icon:hover {
      color: #a4744a;
    }

    /* Main Content */
    .main-wrapper {
      padding: 20px;
    }
    
    .container {
      display: flex;
      max-width: 1200px;
      margin: 0 auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(75, 59, 43, 0.2);
      overflow: hidden;
      min-height: 600px;
    }
    
    .sidebar {
      background: #8b5e3c;
      width: 320px;
      padding: 0;
      color: #fff;
      display: flex;
      flex-direction: column;
    }
    
    .sidebar a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      font-size: 18px;
      padding: 24px 30px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      transition: background 0.3s;
      display: block;
    }
    
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255,255,255,0.1);
    }
    
    .main-content {
      flex: 1;
      padding: 0;
    }

    h2 {
      margin: 0;
      padding: 30px 40px;
      border-bottom: 2px solid #8b5e3c;
      background: #fff;
      font-size: 24px;
      font-weight: bold;
      color: #8b5e3c;
    }
    
    .box {
      background: #fdf9f4;
      margin: 30px 40px;
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid #e5d4c1;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
    }

    th, td {
      padding: 16px 20px;
      text-align: center;
      border-bottom: 1px solid #e5d4c1;
    }

    th {
      background-color: #d4b896;
      color: #5a3e2b;
      font-weight: bold;
      font-size: 16px;
    }

    td {
      background-color: #fdf9f4;
      font-size: 15px;
    }

    tr:nth-child(even) td {
      background-color: #f8f3ec;
    }

    tr:hover td {
      background-color: #f0e8dd;
    }

    td:nth-child(3) {
      font-weight: 500;
    }

    td:nth-child(4) {
      font-weight: bold;
      color: #8b5e3c;
    }
  </style>
</head>
<body>


<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-left">
      <a href="index.php">Home</a>
      <a href="shop1.php">Shop</a>
      <a href="about1.html">About</a>
    </div>
    <div class="navbar-right">
      <svg class="navbar-icon" fill="currentColor" viewBox="0 0 24 24">
        <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
      </svg>
      <svg class="navbar-icon" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"/>
      </svg>
      <svg class="navbar-icon" fill="currentColor" viewBox="0 0 24 24">
        <path d="M15.5 14H14.71L14.43 13.73C15.41 12.59 16 11.11 16 9.5C16 5.91 13.09 3 9.5 3C5.91 3 3 5.91 3 9.5C3 13.09 5.91 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z"/>
      </svg>
      <a href="logout.php">
        <svg class="navbar-icon" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17 7L15.59 8.41L18.17 11H8V13H18.17L15.59 15.59L17 17L22 12L17 7ZM4 5H12V3H4C2.9 3 2 3.9 2 5V19C2 20.1 2.9 21 4 21H12V19H4V5Z"/>
        </svg>
      </a>
    </div>
  </div>
</nav>

<div class="main-wrapper">
  <div class="container">
    <nav class="sidebar">
      <a href="user.php">Akun Saya</a>
      <a href="lacak_pesanan.php" class="active">Lacak Pesanan</a>
      <a href="ulasan_saya.php">Ulasan Saya</a>
    </nav>

    <div class="main-content">
      <h2>Riwayat Pesanan</h2>
      <div class="box">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal Pesan</th>
              <th>Status</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
              $total = number_format($row['total_amount'], 0, ',', '.');
              echo "<tr>
                      <td>{$no}</td>
                      <td>{$row['order_date']}</td>
                      <td>{$row['status']}</td>
                      <td>Rp {$total}</td>
                    </tr>";
              $no++;
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>