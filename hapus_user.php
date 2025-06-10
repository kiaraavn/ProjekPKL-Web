<?php
include 'koneksi.php';

// Pastikan ID user dikirim lewat parameter URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Query hapus user
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Jika berhasil, kembali ke halaman data akun
        header("Location: data_akun.php?msg=hapus_berhasil");
        exit();
    } else {
        echo "Gagal menghapus user.";
    }

    $stmt->close();
} else {
    echo "ID user tidak ditemukan.";
}

$koneksi->close();
?>
