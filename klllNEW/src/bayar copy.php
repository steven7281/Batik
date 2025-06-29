<?php include 'component/header.php'; ?>
<?php
include "proses/connect.php";

$id_user = $_SESSION['id_alomani'];

// Query ambil keranjang yang status = 0 (belum dibayar)
$query_keranjang = mysqli_query(
    $conn,
    "SELECT k.*, p.nama_barang AS nama_produk, p.harga, p.foto 
     FROM tb_keranjang k 
     JOIN tb_produk p ON k.nama_barang = p.id_produk 
     WHERE k.id_user = '$id_user' AND k.status = 0"
);

$keranjang = [];
$total_harga = 0;

while ($row = mysqli_fetch_assoc($query_keranjang)) {
    $keranjang[] = $row;
    $subtotal = $row['harga'] * $row['jumlah'];
    $total_harga += $subtotal;
}

// Inisialisasi variabel ongkir dan grand_total
$ongkir = 0;
$grand_total = $total_harga;

// Handle form submission untuk menghitung ongkir

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

        .cart-summary h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .product-image img {
            width: 80px;
            height: auto;
            border-radius: 5px;
        }

        .product-details {
            flex: 1;
            margin-left: 15px;
        }

        .product-details p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }

        .quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .quantity button {
            background-color: #814603;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .quantity span {
            font-size: 16px;
            font-weight: bold;
        }

        .price {
            font-size: 16px;
            font-weight: bold;
            color: #814603;
            text-align: right;
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

        .total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .btn-payment {
            background-color: #814603;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
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

        .cart-modern-qty-btn {
            background: #f3f0e9;
            border: none;
            font-size: 1.3rem;
            font-weight: bold;
            color: #814603;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            transition: background 0.2s;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .cart-modern-qty-btn:hover {
            background: #ffd700;
            color: #222;
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

        .cart-modern-btn-secondary {
            background: #f3f0e9;
            color: #814603;
            font-weight: bold;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            width: 100%;
            padding: 14px 0;
            margin-top: 10px;
            letter-spacing: 1px;
            transition: background 0.2s;
        }

        .cart-modern-btn-secondary:hover {
            background: #e0e0e0;
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
                </div>

                <div class="mb-3">
                    <label class="form-label">Kota/Kabupaten</label>
                    <select class="form-select select-address" id="kota" name="kota" disabled required>
                        <option value="">Pilih Kota/Kabupaten</option>
                        <?php include 'get_kota.php'; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kecamatan</label>
                    <select class="form-select select-address" id="kecamatan" name="kecamatan" disabled required>
                        <option value="">Pilih Kecamatan</option>
                        <?php include 'get_kecamatan.php'; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kelurahan</label>
                    <select class="form-select select-address" id="kelurahan" name="kelurahan" disabled required>
                        <option value="">Pilih Kelurahan</option>
                        <?php include 'get_kecamatan.php'; ?>
                    </select>
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



                <button type="submit" class="btn-payment">BAYAR</button>
            </form>

            <!-- KERANJANG -->
            <div class="col-md-6 cart-summary">
                <h2>Pesanan Anda</h2>
                <?php
                $id_user = $_SESSION['id_alomani'];
                $query = mysqli_query($conn, "
        SELECT k.*, p.nama_barang, p.harga, p.foto 
        FROM tb_keranjang k
        JOIN tb_produk p ON k.nama_barang = p.id_produk
        WHERE k.id_user = $id_user
    ");

                $total = 0;
                $i = 1;
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
                            <div class="cart-modern-name"><?= strtoupper($row['nama_barang']) ?> (<?= $row['ukuran'] ?>)
                            </div>
                            <form method="post" class="quantity-form">
                                <input type="hidden" name="id_keranjang" value="<?= $row['id_keranjang'] ?>">
                                <div class="cart-modern-qty-row">
                            
                                    <span id="quantity-<?= $row['id_keranjang'] ?>">QTY : <?= $jumlah ?></span>
                                   
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
                } ?>

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

                <input type="hidden" id="ongkir_input" name="ongkir" value="0">
                <input type="hidden" id="total_harga" value="<?= $total ?>">
                <input type="hidden" name="grand_total" id="grand_total_input" value="<?= $total ?>">
            </div>
        </div>

    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function formatRupiah(angka) {
            return 'Rp' + angka.toLocaleString('id-ID');
        }

        // Fungsi update ongkir dan total
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

            // Update tampilan
            document.getElementById('regulerPrice').innerText = formatRupiah(harga);
            document.getElementById('ongkirSummary').innerText = formatRupiah(harga);
            document.getElementById('ongkir_input').value = harga;

            // Hitung grand total
            updateGrandTotal(harga);
        }

        // Fungsi update grand total
        function updateGrandTotal(ongkir) {
            const subtotal = parseInt(document.getElementById('total_harga').value) || 0;
            const grandTotal = subtotal + ongkir;

            // Update tampilan
            document.getElementById('grandTotal').innerText = formatRupiah(grandTotal);
            document.getElementById('grand_total_input').value = grandTotal;
        }

        // Mapping provinsi ke pulau
        const provinsiKePulau = {
            // Jawa
            '31': 'Jawa', '32': 'Jawa', '33': 'Jawa', '34': 'Jawa', '35': 'Jawa', '36': 'Jawa', '37': 'Jawa',
            // Sumatra
            '11': 'Sumatra', '12': 'Sumatra', '13': 'Sumatra', '14': 'Sumatra', '15': 'Sumatra', '16': 'Sumatra',
            '17': 'Sumatra', '18': 'Sumatra', '19': 'Sumatra', '21': 'Sumatra',
            // Bali & Nusa Tenggara
            '51': 'Bali', '52': 'NTB', '53': 'NTT',
            // Kalimantan
            '61': 'Kalimantan', '62': 'Kalimantan', '63': 'Kalimantan', '64': 'Kalimantan',
            // Sulawesi
            '71': 'Sulawesi', '72': 'Sulawesi', '73': 'Sulawesi', '74': 'Sulawesi', '75': 'Sulawesi', '76': 'Sulawesi',
            // Maluku
            '81': 'Maluku', '82': 'Maluku',
            // Papua
            '91': 'Papua', '92': 'Papua', '94': 'Papua'
        };

        // Event listener untuk provinsi
        document.getElementById('provinsi').addEventListener('change', function () {
            const provId = this.value;
            const pulau = provinsiKePulau[provId] || 'Jawa';
            setShippingPriceByPulau(pulau);
        });

        // Inisialisasi awal
        document.addEventListener('DOMContentLoaded', function () {
            // Set default ongkir
            setShippingPriceByPulau('Jawa');

            // AJAX untuk select2 (jika diperlukan)
            // ... kode AJAX yang sudah ada ...
        });

        $(document).ready(function () {
            // Ketika provinsi dipilih
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

            // Ketika kota dipilih
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

            // Ketika kecamatan dipilih
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

        function increaseQuantity(id) {
            const el = document.getElementById(id);
            let val = parseInt(el.textContent);
            if (!isNaN(val)) el.textContent = val + 1;
        }

        function decreaseQuantity(id) {
            const el = document.getElementById(id);
            let val = parseInt(el.textContent);
            if (!isNaN(val) && val > 1) el.textContent = val - 1;
        }
        function updateShipping() {
            // Submit form secara otomatis ketika pilihan shipping berubah
            document.getElementById('paymentForm').submit();
        }

        // Fungsi untuk format angka
        function formatRupiah(angka) {
            return 'Rp' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>

    <?php include 'component/footer.php'; ?>
</body>

</html>