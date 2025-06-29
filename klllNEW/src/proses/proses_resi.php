<?php
include "connect.php";

// Inisialisasi pesan
$message = '';

// Pastikan form disubmit dengan benar
if (isset($_POST['input_resi_validate'])) {
    // Ambil dan sanitasi input
    $id_order = isset($_POST['id_order']) ? mysqli_real_escape_string($conn, trim($_POST['id_order'])) : "";
    $resi = isset($_POST['resi']) ? mysqli_real_escape_string($conn, trim($_POST['resi'])) : "";

    // Validasi input
    if (empty($id_order)) {
        $message = '<script>alert("Order ID harus diisi");
                    window.location="../admin/dashboard.php"</script>';
    } elseif (empty($resi)) {
        $message = '<script>alert("Nomor resi harus diisi");
                    window.location="../admin/dashboard.php"</script>';
    } elseif (strlen($resi) < 5) {
        $message = '<script>alert("Nomor resi minimal 5 karakter");
                    window.location="../admin/dashboard.php"</script>';
    } else {
        // Gunakan prepared statement untuk keamanan
        $check = $conn->prepare("SELECT id_order FROM tb_order WHERE id_order = ?");
        $check->bind_param("s", $id_order);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Update resi DAN status pengiriman sekaligus
            $update = $conn->prepare("UPDATE tb_order SET resi = ?, status_pengiriman = 'Dikirim' WHERE id_order = ?");
            $update->bind_param("ss", $resi, $id_order);

            if ($update->execute()) {
                $message = '<script>alert("Resi berhasil ditambahkan dan status diubah menjadi Dikirim");
                            window.location="../admin/dashboard.php"</script>';

                // Catat aktivitas di log (opsional)
                error_log("Admin updated resi for order $id_order to $resi and set status to Dikirim");
            } else {
                $message = '<script>alert("Gagal mengupdate: ' . $conn->error . '");
                            window.location="../admin/dashboard.php"</script>';
            }
        } else {
            $message = '<script>alert("Order ID tidak ditemukan");
                        window.location="../admin/dashboard.php"</script>';
        }
    }
} else {
    $message = '<script>alert("Akses tidak valid");
                window.location="../admin/dashboard.php"</script>';
}

echo $message;
?>