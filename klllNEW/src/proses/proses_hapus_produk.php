<?php
include "connect.php";

$id_produk = isset($_POST['id_produk']) ? htmlentities($_POST['id_produk']) : "";
$foto = isset($_POST['foto']) ? htmlentities($_POST['foto']) : "";
$message = '';

if (!empty($_POST['input_user_validate'])) {
    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // 1. Hapus terlebih dahulu data terkait di tb_keranjang
        $delete_cart = mysqli_query($conn, "DELETE FROM tb_keranjang WHERE nama_barang = '$id_produk'");
        if (!$delete_cart) {
            throw new Exception("Gagal menghapus data keranjang");
        }

        // 2. Hapus data produk
        $delete_product = mysqli_query($conn, "DELETE FROM tb_produk WHERE id_produk = '$id_produk'");
        if (!$delete_product) {
            throw new Exception("Gagal menghapus data produk");
        }

        // 3. Hapus file foto jika produk berhasil dihapus dari database
        if (file_exists("../assets/images/$foto")) {
            unlink("../assets/images/$foto");
        }

        // Commit transaksi jika semua berhasil
        mysqli_commit($conn);

        $message = '<script>alert("Data berhasil dihapus");
                   window.location="../admin/dashboard.php#products"</script>';
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        mysqli_rollback($conn);
        $message = '<script>alert("Data gagal dihapus: ' . $e->getMessage() . '");
                   window.location="../admin/dashboard.php#products"</script>';
    }
}

echo $message;
?>