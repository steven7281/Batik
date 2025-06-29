<?php
include 'component/header.php';
include 'connect.php';
session_start();

$id_user = $_SESSION['id_alomani'];
$total_harga = 0;

// Ambil data keranjang
$query = mysqli_query($conn, "SELECT k.*, p.nama_barang, p.harga, p.foto FROM tb_keranjang k
JOIN tb_produk p ON k.id_produk = p.id_produk
WHERE k.id_user = '$id_user'");
$keranjang = [];
while ($row = mysqli_fetch_assoc($query)) {
    $keranjang[] = $row;
    $total_harga += $row['harga'] * $row['jumlah'];
}

// Proses jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlentities($_POST['nama']);
    $telepon = htmlentities($_POST['telepon']);
    $provinsi = htmlentities($_POST['provinsi']);
    $kota = htmlentities($_POST['kota']);
    $kecamatan = htmlentities($_POST['kecamatan']);
    $kelurahan = htmlentities($_POST['kelurahan']);
    $kode_pos = htmlentities($_POST['kode_pos']);
    $alamat_lengkap = htmlentities($_POST['alamat']);
    $jasa_kirim = htmlentities($_POST['shipping']);
    $total = $total_harga;

    $alamat = "$alamat_lengkap, $kelurahan, $kecamatan, $kota, $provinsi - $kode_pos";

    $insert = mysqli_query($conn, "INSERT INTO tb_order 
    (id_user, nama_penerima, telepon, alamat, jasa_kirim, total, status_order, tgl_order)
    VALUES 
    ('$id_user', '$nama', '$telepon', '$alamat', '$jasa_kirim', '$total', 'Menunggu Pembayaran', NOW())");

    if ($insert) {
        echo "<script>window.location.href='last.php';</script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal menyimpan pesanan.</div>";
    }
}
?>
