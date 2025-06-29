<?php
include "connect.php";
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_keranjang = intval($_POST['id_keranjang']);
    
    // Verify the cart item belongs to the logged-in user
    $check = mysqli_query($conn, "
        SELECT * FROM tb_keranjang 
        WHERE id_keranjang = $id_keranjang 
        AND id_user = {$_SESSION['id_alomani']}
    ");
    
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
        exit;
    }
    
    // Delete the item
    $delete = mysqli_query($conn, "DELETE FROM tb_keranjang WHERE id_keranjang = $id_keranjang");
    
    if ($delete) {
        // Calculate new subtotal
        $subtotal_query = mysqli_query($conn, "
            SELECT SUM(p.harga * k.jumlah) as subtotal
            FROM tb_keranjang k
            JOIN tb_produk p ON k.nama_barang = p.id_produk
            WHERE k.id_user = {$_SESSION['id_alomani']}
        ");
        $subtotal_row = mysqli_fetch_assoc($subtotal_query);
        $new_subtotal = $subtotal_row['subtotal'] ?? 0;
        
        echo json_encode([
            'success' => true,
            'new_subtotal' => $new_subtotal
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus item']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}
?>