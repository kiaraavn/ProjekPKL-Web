<?php

$koneksi = new mysqli("localhost", "root", "", "db_adornee");


if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // query ke database dan cek login 
    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: admin1.php"); 
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
  body {
    background: #fff7f0;
    background-image: url('gambar/bg3.jpg'); 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }
  .login-box {
    background: #6b4c3b;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    width: 320px;
    color: #fff;
    text-align: center;
  }
  .login-box .icon-user {
    font-size: 80px;
    margin-bottom: 20px;
    color: #d9b382;
  }
  .login-box h2 {
    margin-bottom: 25px;
  }

  /* Container untuk input dan icon */
  .input-group {
    position: relative;
    margin-bottom: 20px;
  }
  .input-group input {
    width: 100%;
    padding: 12px 15px 12px 40px; 
    border: none;
    border-radius: 5px;
    font-size: 16px;
    color: #6b4c3b;
    background: #fff;
    box-sizing: border-box;
  }
  .input-group .icon-left {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #d9b382;
    font-size: 18px;
    pointer-events: none; /* supaya icon ga bisa diklik */
  }

  .login-box button {
    background-color: #d9b382;
    color: #6b4c3b;
    border: none;
    padding: 12px 20px;
    font-size: 18px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }
  .login-box button:hover {
    background-color: #c2a36b;
  }
  .error {
    background-color: #ff6b6b;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-weight: 600;
    color: white;
  }
</style>
</head>
<body>
  <div class="login-box">
    <i class="fa-solid fa-user icon-user"></i>
    <h2>Login Admin</h2>
    <?php if (isset($error)) { echo '<div class="error">'.$error.'</div>'; } ?>
    <form method="POST">
        <div class="input-group">
          <i class="fa-solid fa-user icon-left"></i>
          <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
          <i class="fa-solid fa-lock icon-left"></i>
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login">Login</button>
    </form>
  </div>
</body>
</html>
