<?php
$pageTitle = 'Keranjang - Batik Alomani';
include 'component/header.php';

if (!isset($_SESSION['id_alomani'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='login.php';</script>";
    exit();
}
include "proses/connect.php";

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $id_keranjang = $_POST['id_keranjang'];
    $new_quantity = max(1, intval($_POST['new_quantity']));

    $update = mysqli_query($conn, "UPDATE tb_keranjang SET jumlah = $new_quantity WHERE id_keranjang = $id_keranjang AND status = 0");

    if ($update) {
        header("Location: keranjang.php");
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui jumlah');</script>";
    }
}

// Handle delete item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {
    $id_keranjang = $_POST['id_keranjang'];

    $delete = mysqli_query($conn, "DELETE FROM tb_keranjang WHERE id_keranjang = $id_keranjang AND status = 0");

    if ($delete) {
        header("Location: keranjang.php");
        exit();
    } else {
        echo "<script>alert('Gagal menghapus item');</script>";
    }
}
?>
<style>
    .cart-modern-qty-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f3f0e9 !important;
        color: #814603 !important;
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
        cursor: pointer;
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
        cursor: pointer;
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
        cursor: pointer;
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

    /* Loading spinner */
    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Add delete button style */
    .cart-modern-delete-btn {
        background: #f8d7da;
        color: #721c24;
        border: none;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        margin-left: 12px;
    }

    .cart-modern-delete-btn:hover {
        background: #f5c6cb;
        color: #491217;
    }

    .cart-modern-delete-btn i {
        font-size: 1.1rem;
    }

    .cart-modern-price-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Loading spinner for delete */
    .delete-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(114, 28, 36, 0.3);
        border-radius: 50%;
        border-top-color: #721c24;
        animation: spin 1s ease-in-out infinite;
    }
</style>
<div class="cart-modern-box">
    <div class="cart-modern-title">Keranjang Belanja</div>
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
                <img src="assets/images/<?= $row['foto'] ?>" class="cart-modern-img" alt="<?= $row['nama_barang'] ?>">
                <div class="cart-modern-info">
                    <div class="cart-modern-name"><?= strtoupper($row['nama_barang']) ?> (<?= $row['ukuran'] ?>)</div>
                    <form method="post" class="quantity-form">
                        <input type="hidden" name="id_keranjang" value="<?= $row['id_keranjang'] ?>">
                        <div class="cart-modern-qty-row">
                            <button type="button" class="cart-modern-qty-btn"
                                onclick="updateQuantity(<?= $row['id_keranjang'] ?>, -1)">-</button>
                            <span id="quantity-<?= $row['id_keranjang'] ?>"><?= $jumlah ?></span>
                            <button type="button" class="cart-modern-qty-btn"
                                onclick="updateQuantity(<?= $row['id_keranjang'] ?>, 1)">+</button>
                        </div>
                    </form>
                </div>
                <div class="cart-modern-price-container">
                    <div class="cart-modern-price" id="price-<?= $row['id_keranjang'] ?>">
                        Rp<?= number_format($subtotal, 0, ',', '.') ?>
                    </div>
                    <button type="button" class="cart-modern-delete-btn" onclick="deleteItem(<?= $row['id_keranjang'] ?>)">
                        <i class="fas fa-trash-alt"></i>
                        <div class="delete-spinner" id="delete-spinner-<?= $row['id_keranjang'] ?>"></div>
                    </button>
                </div>
            </div>
            <?php $i++;
        }
    } else {
        echo '<p style="text-align:center;padding:20px;">Keranjang belanja Anda kosong</p>';
    }
    ?>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <div class="cart-modern-summary">
            <div class="cart-modern-summary-row total">
                <span>Total</span>
                <span id="cart-total">Rp<?= number_format($total, 0, ',', '.') ?></span>
            </div>
        </div>

        <button class="cart-modern-btn-main" onclick="window.location.href='bayar.php'">
            <span id="checkout-text">CHECK OUT</span>
            <div class="loading-spinner" id="checkout-spinner"></div>
        </button>
    <?php endif; ?>
    <button class="cart-modern-btn-secondary" onclick="window.location.href='index.php'">LANJUT BELANJA</button>
</div>
<script>
    function updateQuantity(cartId, change) {
        const quantityElement = document.getElementById(`quantity-${cartId}`);
        const currentQuantity = parseInt(quantityElement.textContent);
        const newQuantity = Math.max(1, currentQuantity + change);

        // Jika quantity tidak berubah, tidak perlu request ke server
        if (newQuantity === currentQuantity) return;

        const priceElement = document.getElementById(`price-${cartId}`);

        // Tampilkan loading
        quantityElement.innerHTML = `<span class="loading-spinner"></span>`;
        priceElement.style.opacity = '0.5';

        fetch('proses/proses_update_quantity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_keranjang=${cartId}&new_quantity=${newQuantity}`
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update UI
                    quantityElement.textContent = newQuantity;
                    priceElement.textContent = `Rp${data.new_price.toLocaleString('id-ID')}`;
                    document.getElementById('cart-total').textContent = `Rp${data.total.toLocaleString('id-ID')}`;
                } else {
                    // Kembalikan ke nilai sebelumnya
                    quantityElement.textContent = currentQuantity;
                    console.error('Error:', data.message);
                }
                priceElement.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                quantityElement.textContent = currentQuantity;
                priceElement.style.opacity = '1';
            });
    }

    function deleteItem(cartId) {
        if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
            return;
        }

        const deleteBtn = document.querySelector(`button[onclick="deleteItem(${cartId})"]`);
        const spinner = document.getElementById(`delete-spinner-${cartId}`);
        const productElement = document.getElementById(`product-${cartId}`);

        // Show loading
        deleteBtn.innerHTML = '';
        deleteBtn.disabled = true;
        spinner.style.display = 'block';

        fetch('proses/proses_delete_keranjang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_keranjang=${cartId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove product element
                    productElement.remove();

                    // Update totals
                    document.getElementById('cart-subtotal').textContent = `Rp${data.new_subtotal.toLocaleString('id-ID')}`;
                    document.getElementById('cart-total').textContent = `Rp${data.new_subtotal.toLocaleString('id-ID')}`;

                    // Show message if cart is empty
                    if (data.new_subtotal === 0) {
                        document.querySelector('.cart-modern-product-list').innerHTML =
                            '<p style="text-align:center;padding:20px;">Keranjang belanja Anda kosong</p>';
                    }
                } else {
                    alert('Gagal menghapus item: ' + data.message);
                    deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    deleteBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus item');
                deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
                deleteBtn.disabled = false;
            });
    }
</script>

<!-- Add Font Awesome for trash icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<?php include 'component/footer.php'; ?>