<?php
include 'proses/connect.php';

if (isset($_POST['kota_id'])) {
    $kota_id = htmlentities($_POST['kota_id']);

    $query = mysqli_query($conn, "SELECT id, name FROM districts WHERE regency_id = '$kota_id' ORDER BY name ASC");

    echo '<option value="">Pilih Kecamatan</option>';
    while ($row = mysqli_fetch_assoc($query)) {
        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
    }
}
?>
