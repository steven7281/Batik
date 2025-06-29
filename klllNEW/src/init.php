<?php
// init.php
if (session_status() === PHP_SESSION_NONE) {
    session_name('BATIK_SESSION');
    session_start([
        'cookie_lifetime' => 86400,
        'read_and_close'  => false,
    ]);
}

// Include file penting lainnya
require_once "proses/connect.php";
?>