<?php
$pageTitle = 'Home - Batik Alomani';
include 'component/header.php';
include "proses/connect.php";
?>
<main>
    <!-- Hero Section -->
    <section id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero text-center text-white d-flex align-items-center justify-content-center"
                    style="background-image: url('assets/images/3.jpg');">
                    <div>
                        <h1 class="display-4 fw-bold">New Arrival</h1>
                        <a href="new.php" class="btn btn-warning mt-3">Shop Now!</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero text-center text-white d-flex align-items-center justify-content-center"
                    style="background-image: url('assets/images/9.avif');">
                    <div>
                        <h1 class="display-4 fw-bold">Exclusive Collection</h1>
                        <a href="new.php" class="btn btn-warning mt-3">Explore Now!</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero text-center text-white d-flex align-items-center justify-content-center"
                    style="background-image: url('assets/images/10.jpg');">
                    <div>
                        <h1 class="display-4 fw-bold">Best Deals</h1>
                        <a href="new.php" class="btn btn-warning mt-3">Shop Today!</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </section>

    <!-- New Product Section -->
    <section class="new-product">
        <div class="container">
            <h2 class="text-center mb-5">NEW PRODUCT</h2>
            <div class="row justify-content-center g-4">
                <?php
                include 'proses/connect.php';

               
                $produk = mysqli_query($conn, "SELECT * FROM tb_produk WHERE katagori = 4 ORDER BY id_produk DESC LIMIT 4");

                while ($p = mysqli_fetch_assoc($produk)) {
                    ?>
                    <div class="col-10 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center">
                        <a href="detail.php?id=<?php echo $p['id_produk']; ?>" class="text-decoration-none text-dark w-100">
                            <div class="card h-100 text-center">
                                <img src="assets/images/<?php echo htmlspecialchars($p['foto']); ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($p['nama_barang']); ?>"
                                    style="object-fit: cover; height: 250px;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($p['nama_barang']); ?></h5>
                                    <p class="card-text fw-bold text-black">Rp.
                                        <?php echo number_format($p['harga'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>


    <!-- Categories Section -->
    <section class="categories-section py-5">
        <div class="container-fluid">
            <div class="categories-container">
                <div class="category-item">
                    <a href="new.php" class="text-decoration-none">
                        <img src="assets/images/4.png" alt="TERBARU" class="category-img">
                        <div class="category-text">
                            <p>NEW</p>
                            <h2>Product</h2>
                        </div>
                    </a>
                </div>
                <div class="category-item">
                    <a href="pria.php" class="text-decoration-none">
                        <img src="assets/images/2.png" alt="PRIA" class="category-img">
                        <div class="category-text">
                            <p>Shop</p>
                            <h2>PRIA</h2>
                        </div>
                    </a>
                </div>
                <div class="category-item">
                    <a href="dress.php" class="text-decoration-none">
                        <img src="assets/images/3.jpg" alt="DRESS" class="category-img">
                        <div class="category-text">
                            <p>Shop</p>
                            <h2>DRESS</h2>
                        </div>
                    </a>
                </div>
                <div class="category-item">
                    <a href="wanita.php" class="text-decoration-none">
                        <img src="assets/images/1.png" alt="Wanita" class="category-img">
                        <div class="category-text">
                            <p>Shop</p>
                            <h2>Wanita</h2>
                        </div>
                    </a>
                </div>
                <div class="category-item">
                    <a href="anak.php" class="text-decoration-none">
                        <img src="assets/images/5.png" alt="Anak" class="category-img">
                        <div class="category-text">
                            <p>Shop</p>
                            <h2>Anak</h2>
                        </div>
                    </a>
                </div>
                <div class="category-item">
                    <a href="accessories.php" class="text-decoration-none">
                        <img src="assets/images/6.png" alt="Accessories" class="category-img">
                        <div class="category-text">
                            <p>Shop</p>
                            <h2>Accessories</h2>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include 'component/footer.php'; ?>
