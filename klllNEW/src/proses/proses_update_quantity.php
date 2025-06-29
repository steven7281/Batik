<?php
session_start();
include 'connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_keranjang = (int)$_POST['id_keranjang'];
    $new_quantity = max(1, (int)$_POST['new_quantity']);

    try {
        // Dapatkan data produk
        $product_query = mysqli_query($conn, 
            "SELECT p.harga, k.jumlah as old_quantity 
             FROM tb_keranjang k 
             JOIN tb_produk p ON k.nama_barang = p.id_produk 
             WHERE k.id_keranjang = $id_keranjang");
        
        if(mysqli_num_rows($product_query) == 0) {
            throw new Exception("Produk tidak ditemukan");
        }

        $product = mysqli_fetch_assoc($product_query);
        $harga = (int)$product['harga'];
        $old_quantity = (int)$product['old_quantity'];

        // Update database
        $update = mysqli_query($conn, 
            "UPDATE tb_keranjang SET jumlah = $new_quantity 
             WHERE id_keranjang = $id_keranjang");

        if(!$update) {
            throw new Exception("Gagal update database");
        }

        // Hitung nilai baru
        $new_subtotal = $harga * $new_quantity;
        
        $id_user = $_SESSION['id_alomani'];
        $total_query = mysqli_query($conn, 
            "SELECT SUM(p.harga * k.jumlah) as total 
             FROM tb_keranjang k 
             JOIN tb_produk p ON k.nama_barang = p.id_produk 
             WHERE k.id_user = $id_user");
        
        $total_row = mysqli_fetch_assoc($total_query);
        $total = $total_row['total'] ?? 0;

        echo json_encode([
            'success' => true,
            'new_price' => $new_subtotal,
            'total' => $total,
            'old_quantity' => $old_quantity
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'old_quantity' => $old_quantity ?? 0
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid'
    ]);
}
?>