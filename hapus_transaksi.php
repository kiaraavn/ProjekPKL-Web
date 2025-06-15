<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        $query = "DELETE FROM payments WHERE payment_id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: transaksi.php?msg=hapus_sukses");
            exit();
        } else {
            echo "Gagal menghapus transaksi.";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "ID transaksi tidak valid.";
    }
} else {
    echo "Akses tidak diizinkan.";
}
?>
