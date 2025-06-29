<?php
include "connect.php";
$query = $conn->query("SELECT * FROM provinces");
while ($row = $query->fetch_assoc()) {
    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
}
?>