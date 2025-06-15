<?php
// hapus_kategori.php
include 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  $stmt = $koneksi->prepare("DELETE FROM kategori WHERE kategori_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}

header("Location: data_kategori.php");
exit;
?>