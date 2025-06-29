<?php
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    // Validate status
    $allowed_statuses = ['Berhasil', 'Pending', 'Gagal'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit;
    }

    // Update payment status
    $query = mysqli_query($conn, "UPDATE tb_order SET status_pembayaran = '$status' WHERE id_order = $order_id");
    
    if ($query) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}
?>