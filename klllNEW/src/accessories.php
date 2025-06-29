<?php include 'component/header.php'; ?>
<?php include 'proses/connect.php'; ?>
<!-- Main Content -->
<section class="new-product py-5">
    <div class="container" style="font-family: Macondo; color: #814603;">
        <h2 class="text-center mb-4">Accessories</h2>

        <!-- Filter Bar -->
        <div class="filter-bar mb-4">
            <h6>Filter Produk:</h6>
            <select id="productFilter" class="form-select" style="width: 200px;">
                <option value="all">Semua</option>
            </select>
        </div>
        <!-- Produk -->
        <div class="row g-4">
            <?php
            $produk = mysqli_query($conn, "SELECT * FROM tb_produk WHERE katagori = 6 ORDER BY id_produk DESC");

            while ($p = mysqli_fetch_assoc($produk)) {
                $kategori = strtolower($p['katagori']);
                ?>
                <div class="col-md-3 col-sm-6 product-card" data-category="<?php echo $kategori; ?>">
                    <a href="detail.php?id=<?php echo $p['id_produk']; ?>" class="text-dark text-decoration-none">
                        <img src="assets/images/<?php echo htmlspecialchars($p['foto']); ?>"
                            alt="<?php echo htmlspecialchars($p['nama_barang']); ?>" class="img-fluid">
                        <h5 class="mt-2"><?php echo htmlspecialchars($p['nama_barang']); ?></h5>
                        <p><span class="product-price">Rp. <?php echo number_format($p['harga'], 0, ',', '.'); ?></span></p>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php include 'component/footer.php'; ?>
<script>
    // Filter Produk
    document.getElementById('productFilter').addEventListener('change', function () {
        const selectedCategory = this.value;
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            if (selectedCategory === 'all' || card.dataset.category === selectedCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>