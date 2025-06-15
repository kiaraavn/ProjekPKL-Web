<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT alamat FROM users WHERE user_id = $user_id";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat = htmlspecialchars($_POST['alamat']);

    $update = "UPDATE users SET alamat = '$alamat' WHERE user_id = $user_id";
    if (mysqli_query($koneksi, $update)) {
        header("Location: user.php?update=alamat_success");
        exit;
    } else {
        $error = "Gagal memperbarui alamat.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Alamat</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f9f6f1;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #d3bba6;
        }
        h2 {
            text-align: center;
            color: #8b5e3c;
        }
        textarea {
            width: 100%;
            height: 120px;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #8b5e3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .alert {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Alamat</h2>

    <?php if ($error): ?>
        <div class="alert"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Alamat Lengkap:</label>
        <textarea name="alamat" required><?= htmlspecialchars($user['alamat']) ?></textarea>
        <button type="submit" class="btn">Simpan Alamat</button>
    </form>
</div>
</body>
</html>
