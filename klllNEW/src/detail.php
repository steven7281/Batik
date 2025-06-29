<?php
include 'component/header.php';
include 'proses/connect.php';

// Ambil ID produk dari URL
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data produk dari DB
$query = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id_produk = $id_produk");
$produk = mysqli_fetch_assoc($query);

// Jika tidak ditemukan, redirect ke halaman utama
if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan'); window.location.href='index.php';</script>";
    exit;
}
?>

<!-- Detail Produk -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Thumbnail Images -->
            <div class="col-md-1 col-3 thumbnail-images">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <img src="assets/images/<?= $produk['foto'] ?>" alt="Thumbnail <?= $i + 1 ?>" class="img-fluid">
                <?php endfor; ?>
            </div>

            <!-- Gambar Utama -->
            <div class="col-md-4 col-9 main-image">
                <img src="assets/images/<?= $produk['foto'] ?>" alt="Main Product" class="img-fluid">
            </div>

            <!-- Informasi Produk -->
            <div class="col-md-6 col-12 mt-4 mt-md-0">
                <h1 class="product-title"><?= strtoupper($produk['nama_barang']) ?></h1>
                <p class="product-price">Rp. <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                <div class="divider"></div>

                <div class="mb-3">
                    <h6><b>Deskripsi</b></h6>
                    <p class="product-detail">
                        <?= nl2br(htmlentities($produk['deskripsi'])) ?>
                    </p>
                </div>

                <form action="proses/proses_tambah_keranjang.php" method="POST" class="w-100"
                    onsubmit="syncBeforeSubmit()">
                    <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                    <input type="hidden" name="ukuran" id="hiddenUkuran" value="M"> <!-- default -->
                    <input type="hidden" name="jumlah" id="hiddenJumlah" value="1">

                    <div class="mb-3">
                        <h6><b>Size</b></h6>
                        <div class="btn-group" role="group" aria-label="Size options">
                            <button type="button" class="btn btn-outline-dark  btn-sm size-btn" data-size="S">S</button>
                            <button type="button" class="btn btn-outline-dark btn-sm size-btn" data-size="M">M</button>
                            <button type="button" class="btn btn-outline-dark btn-sm size-btn" data-size="L">L</button>
                            <button type="button" class="btn btn-outline-dark btn-sm size-btn"
data-size="XL">XL</button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <button class="btn btn-outline-dark" type="button"
                            onclick="decreaseQuantity('productQuantity')">-</button>
                        <input type="text" id="productQuantity" value="1" class="form-control text-center mx-2"
                            style="width: 50px;" min="1" oninput="validateInput(this)">
                        <button class="btn btn-outline-dark" type="button"
                            onclick="increaseQuantity('productQuantity')">+</button>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3">
                        <button type="submit" name="submit_cart" class="detail-modern-cart-btn w-100">ADD TO
                            CART</button>
                        <button type="button" class="detail-modern-buy-btn w-100 text-center" onclick="goToBayar()">BUY
                            NOW</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Produk Rekomendasi -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4" style="color: #814603;">YOU MAY ALSO LIKE</h2>
        <div class="row justify-content-center">
            <?php
            $rekomendasi = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id_produk != $id_produk ORDER BY RAND() LIMIT 3");
            while ($rec = mysqli_fetch_assoc($rekomendasi)):
                ?>
                <div class="col-md-3 col-6 mb-4">
                    <a href="detail.php?id=<?= $rec['id_produk'] ?>" class="text-decoration-none text-dark">
                        <div class="card border-0">
                            <div class="position-relative">
                                <span class="badge bg-danger position-absolute" style="top: 10px; left: 10px;">SALE</span>
                                <img src="assets/images/<?= $rec['foto'] ?>" class="card-img-top"
                                    alt="<?= $rec['nama_barang'] ?>">
                            </div>
                            <div class="card-body text-center">
                                <h6 class="card-title"><?= $rec['nama_barang'] ?></h6>
                                <p>Rp. <?= number_format($rec['harga'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php include 'component/footer.php'; ?>
<script>
    function increaseQuantity(inputId) {
        const input = document.getElementById(inputId);
        let currentValue = parseInt(input.value);
        if (!isNaN(currentValue)) {
            input.value = currentValue + 1;
        }
    }

    function decreaseQuantity(inputId) {
        const input = document.getElementById(inputId);
        let currentValue = parseInt(input.value);
        if (!isNaN(currentValue) && currentValue > 1) {
            input.value = currentValue - 1;
        }
    }

    function validateInput(input) {
        if (input.value < 1) {
            input.value = 1;
        }
    }

    // Check login status when page loads
    document.addEventListener('DOMContentLoaded', function () {
        checkLoginStatus();
    });

    function checkLoginStatus() {
        const isLoggedIn = localStorage.getItem('isLoggedIn');
        const username = localStorage.getItem('username');

        const loginBtn = document.querySelector('.login-btn');
        const registerBtn = document.querySelector('.register-btn');
        const logoutBtn = document.querySelector('.logout-btn');
        const userIcon = document.querySelector('.user-icon');
        const userInfo = document.querySelector('.user-info');

        if (isLoggedIn === 'true') {
            loginBtn.style.display = 'none';
            registerBtn.style.display = 'none';
            logoutBtn.style.display = 'block';
            userIcon.style.display = 'block';
            userInfo.style.display = 'block';
            userInfo.textContent = username;
        } else {
            loginBtn.style.display = 'block';
            registerBtn.style.display = 'block';
            logoutBtn.style.display = 'none';
            userIcon.style.display = 'none';
            userInfo.style.display = 'none';
        }
    }

    function logout() {
        localStorage.removeItem('isLoggedIn');
        localStorage.removeItem('username');
        window.location.href = 'index.php';
    }
    function addToCart(id_produk) {
        const qty = document.getElementById('productQuantity').value;
        window.location.href = `proses/proses_tambah_keranjang.php?id=${id_produk}&qty=${qty}`;
    }
    // Pilih ukuran
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const size = this.getAttribute('data-size');
            document.getElementById('hiddenUkuran').value = size;

            // Style aktif
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Sinkronisasi sebelum submit form
    function syncBeforeSubmit() {
        const qty = document.getElementById('productQuantity').value;
        document.getElementById('hiddenJumlah').value = qty;
    }

    function increaseQuantity(inputId) {
        const input = document.getElementById(inputId);
        let val = parseInt(input.value);
        if (!isNaN(val)) input.value = val + 1;
    }

    function decreaseQuantity(inputId) {
        const input = document.getElementById(inputId);
        let val = parseInt(input.value);
        if (!isNaN(val) && val > 1) input.value = val - 1;
    }

    function validateInput(input) {
        if (input.value < 1) input.value = 1;
    }

   
        function goToBayar() {
        const idProduk = <?= $produk['id_produk'] ?>;
        const ukuran = document.getElementById('hiddenUkuran').value;
        const jumlah = document.getElementById('productQuantity').value;

        // Arahkan ke bayar.php sambil bawa data sebagai parameter GET
        window.location.href = `bayar.php?id=${idProduk}&ukuran=${encodeURIComponent(ukuran)}&jumlah=${jumlah}`;
    }



</script>