<?php
include 'proses/connect.php';

if (isset($_POST['kecamatan_id'])) {
    $kecamatan_id = htmlentities($_POST['kecamatan_id']);

    $query = mysqli_query($conn, "SELECT id, name FROM villages WHERE district_id = '$kecamatan_id' ORDER BY name ASC");

    echo '<option value="">Pilih Kelurahan</option>';
    while ($row = mysqli_fetch_assoc($query)) {
        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
    }
}
?>
