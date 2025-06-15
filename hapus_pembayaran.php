<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $payment_id = intval($_POST['payment_id']);

    // Cek data dulu sebelum hapus
    $cek = mysqli_query($koneksi, "SELECT bukti_transfer FROM payments WHERE payment_id = $payment_id");
    $data = mysqli_fetch_assoc($cek);

    if ($data) {
        // Hapus file bukti transfer dari folder jika ada
        if (!empty($data['bukti_transfer']) && file_exists("uploads/" . $data['bukti_transfer'])) {
            unlink("uploads/" . $data['bukti_transfer']);
        }

        // Hapus data dari DB
        mysqli_query($koneksi, "DELETE FROM payments WHERE payment_id = $payment_id");

        $_SESSION['pesan'] = "Pembayaran berhasil dihapus.";
    } else {
        $_SESSION['pesan'] = "Data tidak ditemukan.";
    }
}

header("Location: riwayat_pembayaran.php");
exit;
?>
