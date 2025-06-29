<?php
include "connect.php";
$id_produk = isset($_POST['id_produk']) ? htmlentities($_POST['id_produk']) : "";
$stok = isset($_POST['stok']) ? htmlentities($_POST['stok']) : "";

if (!empty($_POST['input_stok_validate'])) {
    $check = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id_produk = '$id_produk'");

    if (mysqli_num_rows($check) > 0) {
        $query = mysqli_query($conn, "UPDATE tb_produk SET stok ='$stok' WHERE id_produk = '$id_produk'");

        if ($query) {
            $message = '<script>alert("Stok berhasil diupdate");
                        window.location="../admin/dashboard.php"</script>';
        } else {
            $message = '<script>alert("Gagal mengupdate stok");
                        window.location="../admin/dashboard.php"</script>';
        }
    } else {
        $message = '<script>alert("Kode produk tidak ditemukan");
                    window.location="../admin/dashboard.php"</script>';
    }
}
echo $message; 