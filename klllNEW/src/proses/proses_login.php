<?php
include "connect.php";
session_start();

// Ambil inputan
$username = isset($_POST['username']) ? htmlentities($_POST['username']) : "";
$nohp = isset($_POST['nohp']) ? htmlentities($_POST['nohp']) : "";
$password = isset($_POST['password']) ? md5(htmlentities($_POST['password'])) : "";

if (!empty($_POST['submit_validate'])) {
    // Cek username & password
    $query = mysqli_query(
        $conn,
        "SELECT * FROM tb_user 
         WHERE (username = '$username' OR nohp = '$username') 
         AND password = '$password'"
    );

    $hasil = mysqli_fetch_array($query);


    if ($hasil) {
        // Simpan ke session
        $_SESSION['username_alomani'] = $username;
        $_SESSION['level_alomani'] = $hasil['level'];
        $_SESSION['id_alomani'] = $hasil['id'];
        $_SESSION['nama_alomani'] = $hasil['nama'];

        // Arahkan sesuai level
        if ($hasil['level'] == 1) {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../index.php');
        }
        exit(); // WAJIB agar tidak lanjut proses script
    } else {
        echo "<script>
            alert('Username atau password yang Anda masukkan salah');
            window.location = '../login.php';
        </script>";
    }
}
?>