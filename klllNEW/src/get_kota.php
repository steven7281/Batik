<?php
include 'proses/connect.php';

if (isset($_POST['provinsi_id'])) {
    $provinsi_id = htmlentities($_POST['provinsi_id']);

    $query = mysqli_query($conn, "SELECT id, name FROM regencies WHERE province_id = '$provinsi_id' ORDER BY name ASC");

    echo '<option value="">Pilih Kota/Kabupaten</option>';
    while ($row = mysqli_fetch_assoc($query)) {
        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
    }
}
?>
