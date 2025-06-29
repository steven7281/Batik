<?php
include 'component/header.php';
include "proses/connect.php";

// Check if there's a snap token
if (!isset($_GET['snap_token'])) {
    header("Location: keranjang.php");
    exit();
}

$snap_token = $_GET['snap_token'];

// Get order ID from database based on snap token
$query = mysqli_query($conn, "SELECT id_order FROM tb_order WHERE snap_token = '$snap_token'");
if (!$query || mysqli_num_rows($query) === 0) {
    header("Location: keranjang.php");
    exit();
}

$order = mysqli_fetch_assoc($query);
$order_id = $order['id_order'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pembayaran - Batik Alomani</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-AL1OzGCqVmNaoolG"></script>

</head>

<body>
    <div class="payment-container">
        <h2>Lanjutkan Pembayaran</h2>
        <p>Silakan klik tombol di bawah ini untuk melanjutkan ke halaman pembayaran</p>
        <button id="pay-button" class="payment-button">Bayar Sekarang</button>
    </div>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            snap.pay('<?php echo $snap_token; ?>', {
                onSuccess: function (result) {
                    // Send AJAX request to update payment status
                    fetch('proses/update_payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'order_id=<?php echo $order_id; ?>&status=Berhasil'
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = 'index.php';
                            } else {
                                alert('Pembayaran berhasil tetapi gagal memperbarui status. Silakan hubungi admin.');
                                window.location.href = 'index.php';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.location.href = 'index.php';
                        });
                },
                onPending: function (result) {
                    window.location.href = 'order_pending.php?order_id=' + result.order_id;
                },
                onError: function (result) {
                    window.location.href = 'order_error.php?order_id=' + result.order_id;
                }
            });
        };
    </script>
</body>

</html>
<?php include 'component/footer.php'; ?>