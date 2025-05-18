<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']);

        // Query hapus produk berdasarkan id
        $query = "DELETE FROM produk WHERE id_produk = $id";

        if (mysqli_query($koneksi, $query)) {
            // Jika berhasil hapus, redirect ke daftar produk dengan pesan sukses
            header('Location: produk.php?msg=hapus_berhasil');
            exit;
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        echo "ID produk tidak valid.";
    }
} else {
    echo "Metode request tidak diperbolehkan.";
}
?>
