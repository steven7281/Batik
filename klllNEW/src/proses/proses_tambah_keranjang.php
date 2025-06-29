<?php
include "connect.php";
session_start();

if (!isset($_SESSION['id_alomani'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location.href='../login.php';</script>";
    exit;
}

$id_user = $_SESSION['id_alomani'];
$nama_barang = isset($_POST['id_produk']) ? intval($_POST['id_produk']) : 0;
$ukuran = isset($_POST['ukuran']) ? htmlentities($_POST['ukuran']) : '';
$jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 1;
$status = 0; // Always set status to 0 for new items

// Check if there's an existing product with the same size in the user's cart
$cek = mysqli_query($conn, "SELECT * FROM tb_keranjang WHERE id_user = $id_user AND nama_barang = $nama_barang AND ukuran = '$ukuran'");

if (mysqli_num_rows($cek) > 0) {
    $existing_item = mysqli_fetch_assoc($cek);

    if ($existing_item['status'] == 1) {
        // If existing item has status=1 (completed), create a new entry
        mysqli_query($conn, "INSERT INTO tb_keranjang (id_user, nama_barang, ukuran, jumlah, status) 
                           VALUES ($id_user, $nama_barang, '$ukuran', $jumlah, $status)");
    } else {
        // If existing item has status=0 (active), update the quantity
        mysqli_query($conn, "UPDATE tb_keranjang 
                            SET jumlah = jumlah + $jumlah 
                            WHERE id_user = $id_user 
                            AND nama_barang = $nama_barang 
                            AND ukuran = '$ukuran'
                            AND status = 0");
    }
} else {
    // If no existing item, create a new entry
    mysqli_query($conn, "INSERT INTO tb_keranjang (id_user, nama_barang, ukuran, jumlah, status) 
                        VALUES ($id_user, $nama_barang, '$ukuran', $jumlah, $status)");
}

echo "<script>alert('Produk berhasil ditambahkan ke keranjang'); window.location.href='../keranjang.php';</script>";
?>