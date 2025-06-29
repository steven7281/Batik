<?php
include "connect.php";
$id_produk = isset($_POST['id_produk']) ? htmlentities($_POST['id_produk']) : "";
$nama_barang = isset($_POST['nama_barang']) ? htmlentities($_POST['nama_barang']) : "";
$deskripsi = isset($_POST['deskripsi']) ? htmlentities($_POST['deskripsi']) : "";
$katagori = isset($_POST['katagori']) ? htmlentities($_POST['katagori']) : "";
$harga = isset($_POST['harga']) ? htmlentities($_POST['harga']) : "";
$stok = isset($_POST['stok']) ? htmlentities($_POST['stok']) : "";

$kode_rand = rand(10000, 99999) . "-";
$target_dir = "../assets/images/" . $kode_rand;
$target_file = $target_dir . basename($_FILES['foto']['name']);
$imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (!empty($_POST['input_menu_validate'])) {
    // Cek Apakah Gambar Atau Bukan 
    $cek = getimagesize($_FILES['foto']['tmp_name']);
    if ($cek === false) {
        $message = "Ini bukan file gambar";
        $statusUpload = 0;
    } else {
        $statusUpload = 1;
        if (file_exists($target_file)) {
            $message = "Maaf, File yang Dimasukkan Telah Ada";
            $statusUpload = 0;
        } else {
            if ($_FILES['foto']['size'] > 500000) { //500kb
                $message = "File foto yang di upload terlalu besar";
            } else {
                if ($imageType != "jpg" && $imageType != "png" && $imageType != "jpg" && $imageType != "gif") {
                    $message = "Maaf, hanya diperbolehkan gambar yang memiliki format JPG, JPEG, PNG dan GIF";
                    $statusUpload = 0;
                }
            }
        }
    }
    if ($statusUpload == 0) {
        $message = '<script>alert("' . $message . ', Gambar tidak dapat diupload");
    window.location="../admin/dashboard.php"</script>';
    } else {
        $select = mysqli_query($conn, "SELECT * FROM tb_produk WHERE nama_barang = '$nama_barang'");
        if (mysqli_num_rows($select) > 0) {
            $message = '<script>alert("nama barang yang di masukan telah ada"); window.location="../admin/dashboard.php"</script>';
        } else {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $query = mysqli_query($conn, "UPDATE tb_produk SET foto='" . $kode_rand . $_FILES['foto']['name'] . "', nama_barang= '$nama_barang', deskripsi='$deskripsi', harga='$harga', stok='$stok' WHERE id_produk='$id_produk'");
                if ($query) {
                    $message = '<script>alert("Data berhasil dimasukkan");
        window.location="../admin/dashboard.php"</script>';
                } else {
                    $message = '<script>alert("Data gagal dimasukkan");
        window.location="../admin/dashboard.php"</script>';
                }
            } else {
                $message = '<script>alert("maaf terjadi kesalahan file tidak dapat di upload");
            window.location="../admin/dashboard.php"</script>';
            }
        }
    }
}
echo $message;
?>