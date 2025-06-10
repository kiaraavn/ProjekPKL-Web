<?php
session_start();


include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["email"] = $user["email"];
        header("Location: index.php");
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('gambar/background.jpg'); 
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: rgba(255, 255, 255, 0.63);
      display: flex;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      max-width: 800px;
      width: 100%;
      overflow: hidden;
    }

    .login-left {
      background-color:rgba(237, 231, 230, 0.4);
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .login-left i {
      font-size: 60px;
      color: #a0522d;
    }

    .login-right {
      flex: 2;
      padding: 40px;
    }

    .login-right h2 {
      margin-bottom: 20px;
      color: #a0522d;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px 14px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #f1f6ff;
      font-size: 16px;
    }

    button {
      background-color: #a94442;
      color: white;
      padding: 10px 25px;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 15px;
    }

    button:hover {
      background-color: #8b3c3a;
    }

    .register-link {
      margin-top: 15px;
      display: block;
      color: #5a2e17;
    }

    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <div class="login-container">
    <div class="login-left">
      <i class="fas fa-user-circle"></i>
    </div>
    <div class="login-right">
      <h2>Selamat Datang Kembali</h2>
      <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="post" action="">
        <label for="email">Email</label><br>
        <input type="email" name="email" required placeholder="Masukkan email"><br>

        <label for="password">Password</label><br>
        <input type="password" name="password" required placeholder="Masukkan password"><br>

        <button type="submit">LOGIN</button>
      </form>
      <p class="register-link">Pelanggan baru? Ayo Daftar dulu <a href="register.php">Daftar</a></p>
    </div>
  </div>
</body>
</html>
