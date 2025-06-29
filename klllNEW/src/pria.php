<?php include 'component/header.php'; ?>
<?php include 'proses/connect.php'; ?>
<!-- Main Content -->
<section class="new-product py-5">
    <div class="container" style="font-family: Macondo; color: #814603;">
        <h2 class="text-center mb-4">Pria</h2>

        <!-- Filter Bar -->
        <?php

        // Ambil data jenis unik dari produk dengan katagori = 4
        $query = mysqli_query($conn, "SELECT DISTINCT jenis FROM tb_produk WHERE katagori = 1");

        $jenisProdukList = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $jenisProdukList[] = $row['jenis'];
        }
        ?>
        <div class="filter-bar mb-4">
            <h6>Filter Produk:</h6>
            <select id="productFilter" class="form-select" style="width: 200px;">
                <option value="all">Semua</option>
                <?php foreach ($jenisProdukList as $jenis): ?>
                    <option value="<?= strtolower($jenis) ?>"><?= ucfirst($jenis) ?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <!-- Produk -->
        <div class="row g-4">
            <?php
            $produk = mysqli_query($conn, "SELECT * FROM tb_produk WHERE katagori = 1 ORDER BY id_produk DESC");

            while ($p = mysqli_fetch_assoc($produk)) {
                $jenis = strtolower($p['jenis']);
                ?>
                <div class="col-md-3 col-sm-6 product-card" data-category="<?php echo $jenis; ?>">
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