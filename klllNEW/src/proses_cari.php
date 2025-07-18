<?php
include 'component/header.php';
include "proses/connect.php";

$search_query = isset($_GET['q']) ? mysqli_real_escape_string($conn, trim($_GET['q'])) : '';
$products = [];

if (strlen($search_query) >= 3) {
    // Jalankan query pencarian
    $query = "SELECT * FROM tb_produk 
              WHERE nama_barang LIKE '%$search_query%' 
              OR deskripsi LIKE '%$search_query%' 
              OR katagori LIKE '%$search_query%'
              ORDER BY nama_barang";
    
    $result = mysqli_query($conn, $query);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Hasil Pencarian - Batik Alomani</title>
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-body {
            padding: 15px;
        }

        .product-title {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        .product-price {
            color: #814603;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .product-button {
            background-color: #814603;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        .product-button:hover {
            background-color: #663702;
        }

        .no-results {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #666;
        }

        .search-box {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .search-input {
            width: 60%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }

        .search-button {
            background-color: #814603;
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="search-container">
        <!-- Search Box -->


        <div class="search-header">
            <h2>Hasil Pencarian: <?= htmlspecialchars($search_query) ?></h2>
        </div>

        <?php if (count($products) > 0): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="assets/images/<?= $product['foto'] ?>" alt="<?= $product['nama_barang'] ?>"
                            class="product-image">
                        <div class="product-body">
                            <h3 class="product-title"><?= highlight_search($product['nama_barang'], $search_query) ?></h3>
                            <p class="product-price">Rp <?= number_format($product['harga'], 0, ',', '.') ?></p>
                            <a href="detail.php?id=<?= $product['id_produk'] ?>" class="product-button">Lihat Detail</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <p>Maaf, Produk Tidak Ditemukan </p>
                <a href="index.php" class="product-button"
                    style="width: auto; display: inline-block; padding: 10px 20px;">Lihat Semua Produk</a>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // Fungsi untuk highlight teks pencarian
    function highlight_search($text, $search)
    {
        if (empty($search))
            return $text;
        return preg_replace("/($search)/i", '<span style="background-color: #FFF9C4;">$1</span>', $text);
    }
    ?>

</body>

</html>

<?php include 'component/footer.php'; ?>