<?php
include "connect.php";


$name = isset($_POST['nama']) ? htmlentities( $_POST['nama']) : "";
$username = isset($_POST['username']) ? htmlentities( $_POST['username']) : "";
$nohp = isset($_POST['nohp']) ? htmlentities( $_POST['nohp']) : "";
$alamat = isset($_POST['alamat']) ? htmlentities( $_POST['alamat']) : "";
$password = isset($_POST['password']) ? md5($_POST['password']) : "";



if (!empty($_POST['input_user_validate'])) {
    $select = mysqli_query($conn, "SELECT * FROM tb_user WHERE username = '$username'");
    if (mysqli_num_rows($select) > 0) {
        $message = '<script>alert("username yang di masukan telah ada"); window.location="../register.php"</script>';
    } else {
        $query = mysqli_query($conn, "INSERT INTO tb_user (nama, username, nohp, alamat, password) 
                                   VALUES ('$name', '$username', '$nohp', '$alamat', '$password')");
        if ($query) {
            $message = '<script>alert("Berhasil Registrasi"); window.location="../index.php"</script>';
        } else {
            $message = '<script>alert("Gagal Registrasi")</script>';
        }
    }
}

echo $message;
?>