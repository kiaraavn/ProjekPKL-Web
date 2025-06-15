<?php
include 'koneksi.php';
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar - Adornee</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('gambar/bg2.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    .form-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .box {
      background-color:rgba(152, 115, 115, 0.71);
      border-radius: 18px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      display: flex;
      overflow: hidden;
      width: 800px;
      max-width: 90%;
    }

    .left {
      background-color:rgba(181, 154, 154, 0.69);
      width: 40%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 22px;
      font-weight: bold;
      color: #333;
      padding: 20px;
    }

    .right {
      width: 60%;
      padding: 40px;
      background-color: #fff;
    }

    .right h2 {
      color:rgb(93, 62, 61);
      margin-bottom: 25px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="date"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color:rgba(148, 119, 113, 0.69);
    }

    .btn-daftar {
      background-color:rgb(100, 64, 63);
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .btn-daftar:hover {
      background-color: #922d2d;
    }

    .link-login {
      margin-top: 20px;
      font-size: 14px;
    }

    .link-login a {
      color: #a94442;
      text-decoration: none;
    }

    .link-login a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="form-container">
  <div class="box">
    <div class="left">
      SIGN UP
    </div>
    <div class="right">
      <h2>Buat Akun Baru</h2>
      <form action="proses_register.php" method="post">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" required>

        <button type="submit" class="btn-daftar">DAFTAR</button>
      </form>
      <div class="link-login">
        Sudah punya akun? <a href="login.php">Login</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
