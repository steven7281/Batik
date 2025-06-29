<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user = null;
if (!empty($_SESSION['id_alomani'])) {
    include_once __DIR__ . '/../proses/connect.php';
    $user_id = $_SESSION['id_alomani'];
    $q = mysqli_query($conn, "SELECT foto FROM tb_user WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($q);
}
$foto = $user && !empty($user['foto']) ? $user['foto'] : 'avatar.png';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Batik Alomani'; ?></title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/nav-styles.css">
    <style>
        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Dropdown Fix */
        .nav-icons .dropdown-toggle {
            cursor: pointer;
            background: transparent !important;
            border: none;
            padding: 5px 10px;
        }

        .nav-icons .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 5px;
        }

        .nav-icons .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
        }

        .nav-icons .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }

        .nav-icons .dropdown-divider {
            margin: 5px 0;
        }

        .modal {
            z-index: 1060 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }
        .modal {
            z-index: 99999 !important;
            pointer-events: auto !important;
        }

        .modal-backdrop {
            z-index: 9999 !important;
        }
    </style>
</head>

<body>
    <header>
        <!-- Top Header -->
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Logo -->
                    <div class="col-lg-2 col-md-3 col-12 logo-container">
                        <a href="index.php">
                            <img src="assets/images/logo.png" alt="Batik Alomani Logo" class="img-fluid">
                        </a>
                    </div>
                    <!-- Search Bar -->
                    <div class="col-lg-7 col-md-5 col-12">
                        <div class="search-container">
                            <form action="./proses_cari.php" method="get" class="d-flex">
                                <input class="form-control me-2" type="search" name="q"
                                    placeholder="Cari produk batik..." aria-label="Search">
                                <button class="fas fa-search" type="submit"></button>
                            </form>
                        </div>
                    </div>
                    <!-- Navigation Icons -->
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="nav-icons d-flex align-items-center justify-content-end">
                            <a href="index.php" class="nav-icon me-3">
                                <i class="fas fa-home"></i>
                            </a>
                            <a href="keranjang.php" class="nav-icon me-3">
                                <i class="fas fa-shopping-cart"></i>
                            </a>

                            <?php if (isset($_SESSION['id_alomani'])): ?>
                                <div class="dropdown ms-3">
                                    <a class="dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="assets/images/<?= $foto ?>" alt="avatar" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="profil.php">Profile</a></li>
                                        <?php if ($_SESSION['level_alomani'] == 'admin'): ?>
                                            <li><a class="dropdown-item" href="admin/dashboard.php">
                                                    <i class="fas fa-cog me-2"></i>Dashboard Admin</a></li>
                                        <?php endif; ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="logout.php">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-masuk me-2">MASUK</a>
                                <a href="register.php" class="btn btn-daftar">DAFTAR</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Navigation -->
        <nav class="main-nav">
            <div class="container">
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="new.php">NEW Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="pria.php">Baju Pria</a></li>
                    <li class="nav-item"><a class="nav-link" href="wanita.php">Baju Wanita</a></li>
                    <li class="nav-item"><a class="nav-link" href="anak.php">Baju Anak</a></li>
                    <li class="nav-item"><a class="nav-link" href="accessories.php">Accessories</a></li>
                    <li class="nav-item"><a class="nav-link" href="dress.php">Dress</a></li>
                    <?php if (isset($_SESSION['level_alomani']) && $_SESSION['level_alomani'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Dashboard Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>


        <!-- Modal Ubah Profile -->
        <div class="modal fade" id="ModalUbahProfile" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="proses/proses_ubah_profile.php">
                        <div class="modal-body">
                            <!-- Isi form di sini -->
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" class="form-control" value="<?= $_SESSION['username_alomani'] ?>"
                                    disabled>
                            </div>
                            <!-- ... field lainnya ... -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal akhir ubah profile -->
    </header>