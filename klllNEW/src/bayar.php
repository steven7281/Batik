<?php
ob_start();


if (headers_sent($filename, $linenum)) {
    die("Headers already sent in $filename on line $linenum");
}

include 'component/header.php';

if (!isset($_SESSION['id_alomani'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='login.php';</script>";
    exit();
}

require 'C:\laragon\www\klllNEW\klllNEW\vendor\autoload.php';
require_once "proses/connect.php";

// Pastikan koneksi database berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

\Midtrans\Config::$serverKey = 'SB-Mid-server-63EuSLQ8WsSNsT2znZ8XApds';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form dengan escape string
    $id_user = $_SESSION['id_alomani'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon'] ?? '');
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi_text'] ?? '');
    $kota = mysqli_real_escape_string($conn, $_POST['kota_text'] ?? '');
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan_text'] ?? '');
    $kelurahan = mysqli_real_escape_string($conn, $_POST['kelurahan_text'] ?? '');
    $kode_pos = mysqli_real_escape_string($conn, $_POST['kode_pos'] ?? '');
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '');
    $ongkir = isset($_POST['ongkir']) ? (float) $_POST['ongkir'] : 0;

    if (empty($nama) || empty($telepon) || empty($alamat)) {
        echo "<script>alert('Data tidak lengkap'); window.history.back();</script>";
        exit();
    }

    // Query data keranjang
    $query_cart = mysqli_query($conn, "SELECT k.*, p.nama_barang, p.harga, p.id_produk FROM tb_keranjang k 
        JOIN tb_produk p ON k.nama_barang = p.id_produk 
        WHERE k.id_user = '$id_user' AND k.status = 0");

    if (!$query_cart) {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
        exit();
    }

    if (mysqli_num_rows($query_cart) === 0) {
        echo "<script>alert('Keranjang kosong'); window.location='keranjang.php';</script>";
        exit();
    }

    // Proses item keranjang
    $items = array();
    $subtotal = 0;
    $ukuran = ''; // Inisialisasi variabel ukuran

    while ($row = mysqli_fetch_assoc($query_cart)) {
        $items[] = array(
            'id' => $row['id_produk'],
            'price' => $row['harga'],
            'quantity' => $row['jumlah'],
            'name' => $row['nama_barang'] . ' (Ukuran: ' . $row['ukuran'] . ')',
            'ukuran' => $row['ukuran']
        );
        $subtotal += $row['harga'] * $row['jumlah'];
        $ukuran = $row['ukuran']; // Simpan ukuran terakhir untuk order utama
    }

    $grand_total = $subtotal + $ongkir;

    if ($grand_total < 1) {
        echo "<script>alert('Total pembayaran tidak valid'); window.history.back();</script>";
        exit();
    }

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // 1. Simpan data order utama
        $query_order = "INSERT INTO tb_order (id_user, nama, nohp, provinsi, kota, kecamatan, kelurahan, kode_pos, alamat, ukuran, grand_total, status_pembayaran) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query_order);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }

        $status_pembayaran = 'Pending';
        mysqli_stmt_bind_param(
            $stmt,
            'isssssssssds',
            $id_user,
            $nama,
            $telepon,
            $provinsi,
            $kota,
            $kecamatan,
            $kelurahan,
            $kode_pos,
            $alamat,
            $ukuran,
            $grand_total,
            $status_pembayaran
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }

        $order_id = mysqli_insert_id($conn);

        // 2. Update status keranjang
        if (!mysqli_query($conn, "UPDATE tb_keranjang SET status = 1 WHERE id_user = '$id_user' AND status = 0")) {
            throw new Exception("Update keranjang failed: " . mysqli_error($conn));
        }

        // 3. Simpan detail order
        foreach ($items as $item) {
            $query_detail = "INSERT INTO tb_order_detail 
                            (id_order, id_produk, nama_barang, harga, jumlah, ukuran, ongkir, total_harga) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($conn, $query_detail);
            if (!$stmt_detail) {
                throw new Exception("Prepare detail failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt_detail,
                'iisdisdd',
                $order_id,
                $item['id'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $item['ukuran'],
                $ongkir,
                $grand_total
            );

            if (!mysqli_stmt_execute($stmt_detail)) {
                throw new Exception("Execute detail failed: " . mysqli_stmt_error($stmt_detail));
            }
        }

        // 4. Proses Midtrans
        $params = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => $grand_total,
            ),
            'item_details' => $items,
            'customer_details' => array(
                'first_name' => $nama,
                'phone' => $telepon,
                'shipping_address' => array(
                    'address' => $alamat,
                    'city' => $kota,
                    'postal_code' => $kode_pos,
                )
            ),
            'callbacks' => array(
                'finish' => 'https://yourdomain.com/index.php'
            )
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // 5. Update token pembayaran
        $update_query = "UPDATE tb_order SET snap_token = ? WHERE id_order = ?";
        $stmt_update = mysqli_prepare($conn, $update_query);
        if (!$stmt_update) {
            throw new Exception("Prepare update failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt_update, 'si', $snapToken, $order_id);
        if (!mysqli_stmt_execute($stmt_update)) {
            throw new Exception("Execute update failed: " . mysqli_stmt_error($stmt_update));
        }

        // Commit transaksi jika semua sukses
        mysqli_commit($conn);

        ob_end_clean();
        header("Location: pembayaran_midtrans.php?snap_token=" . $snapToken . "&redirect=index.php");
        exit();

    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Batik Alomani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/nav-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body {
            font-family: 'Lexend Mega', sans-serif;
        }

        .breadcrumb {
            font-size: 14px;
            margin: 20px 0;
            text-align: center;
            color: #999;
        }

        .form-section {
            background-color: #e0e0e0;
            padding: 20px;
            border-radius: 10px;
        }

        .form-section input,
        .form-section textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            background-color: #d6d6d6;
        }

        .cart-summary {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
        }

        .cart-modern-box {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            max-width: 900px;
            margin: 40px auto 32px auto;
            padding: 36px 32px 28px 32px;
        }

        .cart-modern-title {
            font-weight: bold;
            font-size: 1.4rem;
            margin-bottom: 18px;
            letter-spacing: 1px;
            color: #814603;
        }

        .cart-modern-product-list {
            margin-bottom: 24px;
        }

        .cart-modern-product-item {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 18px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-modern-product-item:last-child {
            border-bottom: none;
        }

        .cart-modern-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            background: #f3f0e9;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        }

        .cart-modern-info {
            flex: 1;
            font-family: 'Lato', sans-serif;
        }

        .cart-modern-name {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
            color: #222;
        }

        .cart-modern-qty-row {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 1.2rem;
            margin-bottom: 0;
        }

        .cart-modern-price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #814603;
            min-width: 110px;
            text-align: right;
        }

        .cart-modern-summary {
            margin-top: 32px;
            border-top: 1.5px solid #eee;
            padding-top: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .cart-modern-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.1rem;
        }

        .cart-modern-summary-row.total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #814603;
        }

        .cart-modern-btn-main {
            background: #814603;
            color: #fff;
            font-weight: bold;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            width: 100%;
            padding: 14px 0;
            margin-top: 18px;
            letter-spacing: 1px;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        }

        .cart-modern-btn-main:hover {
            background: #663702;
        }

        .shipping-options {
            margin-top: 20px;
        }

        .shipping-options label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #d6d6d6;
            border-radius: 5px;
            cursor: pointer;
        }

        .shipping-options input {
            margin-right: 10px;
        }

        @media (max-width: 600px) {
            .cart-modern-box {
                padding: 18px 4px 12px 4px;
            }

            .cart-modern-product-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .cart-modern-img {
                width: 70px;
                height: 70px;
            }

            .cart-modern-summary {
                padding-top: 10px;
            }
        }
    </style>
</head>

<body>
    <main class="container">
        <div class="breadcrumb">
            <a href="keranjang.php" class="text-dark text-decoration-none">Keranjang</a> →
            <span style="color: red;">Pembayaran</span> →
            <span>Selesai</span>
        </div>
        <div class="row">
            <!-- FORM -->
            <form class="col-md-6 form-section" method="POST">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" placeholder="Masukkan Nama" required>

                <label class="form-label">No Telephone</label>
                <input type="number" name="telepon" placeholder="No. Telepon" required>

                <div class="mb-3">
                    <label class="form-label">Provinsi</label>
                    <select class="form-select select-address" id="provinsi" name="provinsi" required>
                        <option value="">Pilih Provinsi</option>
                        <?php include 'get_provinsi.php'; ?>
                    </select>
                    <input type="hidden" id="provinsi_text" name="provinsi_text">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kota/Kabupaten</label>
                    <select class="form-select select-address" id="kota" name="kota" disabled required>
                        <option value="">Pilih Kota/Kabupaten</option>
                        <?php include 'get_kota.php'; ?>
                    </select>
                    <input type="hidden" id="kota_text" name="kota_text">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kecamatan</label>
                    <select class="form-select select-address" id="kecamatan" name="kecamatan" disabled required>
                        <option value="">Pilih Kecamatan</option>
                        <?php include 'get_kecamatan.php'; ?>
                    </select>
                    <input type="hidden" id="kecamatan_text" name="kecamatan_text">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kelurahan</label>
                    <select class="form-select select-address" id="kelurahan" name="kelurahan" disabled required>
                        <option value="">Pilih Kelurahan</option>
                        <?php include 'get_kelurahan.php'; ?>
                    </select>
                    <input type="hidden" id="kelurahan_text" name="kelurahan_text">
                </div>

                <label class="form-label">Kode Pos</label>
                <input type="number" name="kode_pos" placeholder="Kode Pos" required>

                <label class="form-label">Alamat</label>
                <textarea name="alamat" placeholder="Alamat Lengkap" rows="3" required></textarea>

                <div class="shipping-options">
                    <h6>PILIH JASA KIRIM</h6>
                    <label>
                        <input name="shipping" value="Reguler" disabled>
                        <span id="regulerPrice">Rp0</span>
                    </label>
                </div>

                <input type="hidden" id="ongkir_input" name="ongkir" value="0">
                <button type="submit" class="cart-modern-btn-main">LANJUT</button>
            </form>

            <!-- CART SUMMARY -->
            <div class="col-md-6 cart-summary">
                <div class="cart-modern-box">
                    <h2 class="cart-modern-title">Pesanan Anda</h2>
                    <div class="cart-modern-product-list">
                        <?php
                        $id_user = $_SESSION['id_alomani'];

                        $query = mysqli_query($conn, "
        SELECT k.*, p.nama_barang, p.harga, p.foto 
        FROM tb_keranjang k
        JOIN tb_produk p ON k.nama_barang = p.id_produk
        WHERE k.id_user = $id_user AND k.status = 0
    ");

                        $total = 0;
                        $i = 1;

                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                $harga = intval($row['harga']);
                                $jumlah = intval($row['jumlah']);
                                $subtotal = $harga * $jumlah;
                                $total += $subtotal;
                                ?>
                                <div class="cart-modern-product-item" id="product-<?= $row['id_keranjang'] ?>">
                                    <img src="assets/images/<?= $row['foto'] ?>" class="cart-modern-img"
                                        alt="<?= $row['nama_barang'] ?>">
                                    <div class="cart-modern-info">
                                        <div class="cart-modern-name"><?= strtoupper($row['nama_barang']) ?>
                                            (<?= $row['ukuran'] ?>)</div>
                                        <form method="post" class="quantity-form">
                                            <input type="hidden" name="id_keranjang" value="<?= $row['id_keranjang'] ?>">
                                            <div class="cart-modern-qty-row">

                                                <span id="quantity-<?= $row['id_keranjang'] ?>">QTY :<?= $jumlah ?></span>

                                            </div>
                                        </form>
                                    </div>
                                    <div class="cart-modern-price-container">
                                        <div class="cart-modern-price" id="price-<?= $row['id_keranjang'] ?>">
                                            Rp<?= number_format($subtotal, 0, ',', '.') ?>
                                        </div>

                                    </div>
                                </div>
                                <?php $i++;
                            }
                        }
                        ?>
                    </div>

                    <div class="cart-modern-summary">
                        <div class="cart-modern-summary-row">
                            <span>Subtotal</span>
                            <span id="cart-total">Rp<?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        <div class="cart-modern-summary-row">
                            <span>Ongkir (Reguler)</span>
                            <span id="ongkirSummary">Rp0</span>
                        </div>
                        <div class="cart-modern-summary-row total">
                            <span>Total</span>
                            <span id="grandTotal">Rp<?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <input type="hidden" id="total_harga" value="<?= $total ?>">
                    <input type="hidden" name="grand_total" id="grand_total" value="<?= $total ?>">
                </div>
            </div>
        </div>
    </main>

    <script>
        function formatRupiah(angka) {
            return 'Rp' + angka.toLocaleString('id-ID');
        }

        function setShippingPriceByPulau(pulau) {
            let harga = 0;
            switch (pulau) {
                case 'Jawa': harga = 15000; break;
                case 'Sumatra': harga = 25000; break;
                case 'Kalimantan': harga = 35000; break;
                case 'Sulawesi': harga = 40000; break;
                case 'Bali': harga = 20000; break;
                case 'NTB': case 'NTT': harga = 55000; break;
                case 'Maluku': harga = 65000; break;
                case 'Papua': harga = 80000; break;
                default: harga = 15000;
            }

            document.getElementById('regulerPrice').innerText = formatRupiah(harga);
            document.getElementById('ongkirSummary').innerText = formatRupiah(harga);
            document.getElementById('ongkir_input').value = harga;
            updateGrandTotal(harga);
        }

        function updateGrandTotal(ongkir) {
            const subtotal = parseInt(document.getElementById('total_harga').value) || 0;
            const grandTotal = subtotal + ongkir;
            document.getElementById('grandTotal').innerText = formatRupiah(grandTotal);
            document.getElementById('grand_total').value = grandTotal;
        }

        const provinsiKePulau = {
            '31': 'Jawa', '32': 'Jawa', '33': 'Jawa', '34': 'Jawa', '35': 'Jawa', '36': 'Jawa', '37': 'Jawa',
            '11': 'Sumatra', '12': 'Sumatra', '13': 'Sumatra', '14': 'Sumatra', '15': 'Sumatra', '16': 'Sumatra',
            '17': 'Sumatra', '18': 'Sumatra', '19': 'Sumatra', '21': 'Sumatra',
            '51': 'Bali', '52': 'NTB', '53': 'NTT',
            '61': 'Kalimantan', '62': 'Kalimantan', '63': 'Kalimantan', '64': 'Kalimantan',
            '71': 'Sulawesi', '72': 'Sulawesi', '73': 'Sulawesi', '74': 'Sulawesi', '75': 'Sulawesi', '76': 'Sulawesi',
            '81': 'Maluku', '82': 'Maluku',
            '91': 'Papua', '92': 'Papua', '94': 'Papua'
        };

        document.getElementById('provinsi').addEventListener('change', function () {
            const provId = this.value;
            const pulau = provinsiKePulau[provId] || 'Jawa';
            setShippingPriceByPulau(pulau);
        });

        document.addEventListener('DOMContentLoaded', function () {
            setShippingPriceByPulau('Jawa');
        });

        $(document).ready(function () {
            // Update hidden text fields when selections change
            $('#provinsi').on('change', function () {
                $('#provinsi_text').val($(this).find('option:selected').text());
            });

            $('#kota').on('change', function () {
                $('#kota_text').val($(this).find('option:selected').text());
            });

            $('#kecamatan').on('change', function () {
                $('#kecamatan_text').val($(this).find('option:selected').text());
            });

            $('#kelurahan').on('change', function () {
                $('#kelurahan_text').val($(this).find('option:selected').text());
            });

            // AJAX for address selection
            $('#provinsi').on('change', function () {
                var provinsi_id = $(this).val();
                if (provinsi_id) {
                    $.ajax({
                        url: 'get_kota.php',
                        type: 'POST',
                        data: { provinsi_id: provinsi_id },
                        success: function (data) {
                            $('#kota').html(data).prop('disabled', false);
                            $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
                            $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
                        }
                    });
                } else {
                    $('#kota, #kecamatan, #kelurahan').html('<option value="">Pilih</option>').prop('disabled', true);
                }
            });

            $('#kota').on('change', function () {
                var kota_id = $(this).val();
                if (kota_id) {
                    $.ajax({
                        url: 'get_kecamatan.php',
                        type: 'POST',
                        data: { kota_id: kota_id },
                        success: function (data) {
                            $('#kecamatan').html(data).prop('disabled', false);
                            $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
                        }
                    });
                } else {
                    $('#kecamatan, #kelurahan').html('<option value="">Pilih</option>').prop('disabled', true);
                }
            });

            $('#kecamatan').on('change', function () {
                var kecamatan_id = $(this).val();
                if (kecamatan_id) {
                    $.ajax({
                        url: 'get_kelurahan.php',
                        type: 'POST',
                        data: { kecamatan_id: kecamatan_id },
                        success: function (data) {
                            $('#kelurahan').html(data).prop('disabled', false);
                        }
                    });
                } else {
                    $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
                }
            });
        });
    </script>

    <?php include 'component/footer.php'; ?>
</body>

</html>