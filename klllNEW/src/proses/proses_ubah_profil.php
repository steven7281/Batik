<?php
include "../proses/connect.php";
session_start();

$user_id = $_SESSION['id_alomani'] ?? null;
if (!$user_id) {
    header("Location: ../login.php");
    exit();
}


$query = mysqli_query($conn, "SELECT * FROM tb_user WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);


$nama = isset($_POST['nama']) && $_POST['nama'] !== '' ? $_POST['nama'] : $user['nama'];
$username = isset($_POST['username']) && $_POST['username'] !== '' ? $_POST['username'] : $user['username'];
$nohp = isset($_POST['nohp']) && $_POST['nohp'] !== '' ? $_POST['nohp'] : $user['nohp'];
$alamat = isset($_POST['alamat']) && $_POST['alamat'] !== '' ? $_POST['alamat'] : $user['alamat'];

if (
    $nama !== $user['nama'] ||
    $username !== $user['username'] ||
    $nohp !== $user['nohp'] ||
    $alamat !== $user['alamat']
) {
    mysqli_query($conn, "UPDATE tb_user SET nama='$nama', username='$username', nohp='$nohp', alamat='$alamat' WHERE id='$user_id'");
}

if (
    !empty($_POST['current_password']) &&
    !empty($_POST['new_password']) &&
    !empty($_POST['confirm_password'])
) {
    $current_password = md5($_POST['current_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($current_password !== $user['password']) {
        header("Location: ../profil.php?error=Password lama salah");
        exit();
    }

    if ($new_password !== $confirm_password) {
        header("Location: ../profil.php?error=Konfirmasi password tidak cocok");
        exit();
    }

    $hashed = md5($new_password);
    mysqli_query($conn, "UPDATE tb_user SET password='$hashed' WHERE id='$user_id'");
}


if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed)) {
        $newname = uniqid() . '.' . $ext;
        move_uploaded_file($tmp, "../assets/images/$newname");
        mysqli_query($conn, "UPDATE tb_user SET foto='$newname' WHERE id='$user_id'");
    }
}

header("Location: ../profil.php?success=Profil berhasil diperbarui");
exit();
?>