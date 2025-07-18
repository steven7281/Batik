<?php
include "proses/connect.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Batik Alomani'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/nav-styles.css">
</head>

<style>
    .site-footer {
        background: #814603;
        color: #fff;
        padding: 40px 0 0 0;
        font-family: 'Lato', sans-serif;
        margin-top: 40px;
    }

    .site-footer h5 {
        font-weight: bold;
        margin-bottom: 18px;
        letter-spacing: 1px;
    }

    .site-footer p,
    .site-footer li,
    .site-footer a {
        color: #fff;
        font-size: 1rem;
        opacity: 0.95;
    }

    .site-footer ul {
        padding-left: 0;
        list-style: none;
    }

    .site-footer .social-icons a {
        font-size: 1.6rem;
        margin-right: 18px;
        color: #fff;
        transition: color 0.2s;
    }

    .site-footer .social-icons a:hover {
        color: #ffd700;
    }

    .site-footer hr {
        border-top: 1.5px solid #fff;
        opacity: 0.2;
    }

    .site-footer .footer-bottom {
        background: #6a3702;
        padding: 18px 0 10px 0;
        margin-top: 18px;
        font-size: 0.97rem;
    }

    .site-footer .footer-bottom ul li a {
        color: #ffffff;
        font-weight: 500;
        margin-left: 18px;
        text-decoration: none;
    }

    .site-footer .footer-bottom ul li a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .site-footer {
            padding: 30px 0 0 0;
        }

        .site-footer .col-md-4 {
            margin-bottom: 24px;
        }

        .site-footer .footer-bottom {
            font-size: 0.9rem;
        }
    }
</style>

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>Batik Alomani</h5>
                <p>Fashion Berkualitas, Harga Bersahabat<br>Tampil Stylish Setiap Hari!</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Kontak Kami</h5>
                <ul class="list-unstyled">
                    <li>
                        <a href="https://www.google.com/maps/place/Jl.+Mangga+Besar+IV+I+No.31+9,+RT.9%2FRW.1,+Taman+Sari,+Kec.+Taman+Sari,+Kota+Jakarta+Barat,+Daerah+Khusus+Ibukota+Jakarta+11150"
                            target="_blank" style="color: inherit; text-decoration: none;">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Jl. Mangga Besar IV I No.31 9, RT.9/RW.1, Taman Sari, Kec. Taman Sari, Kota Jakarta Barat,
                            Daerah Khusus Ibukota Jakarta 11150
                        </a>
                    </li>
                    <li><i class="fas fa-phone me-2"></i>+6285694593634</li>
                    <li><i class="fas fa-envelope me-2"></i> batikAlomani@gmail.com</li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Ikuti Kami</h5>
                <div class="social-icons">
                    <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://instagram.com/jakmania_telukgong" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/6285694593634" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <hr>
        <div class="row footer-bottom align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <p class="mb-0">&copy; 2025 Batik Alomani. All rights reserved.</p>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled d-flex justify-content-center justify-content-md-end mb-0 gap-3">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Library JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/scripts.js"></script>

    <script type="text/javascript">
        window.$crisp = []; window.CRISP_WEBSITE_ID = "30f679d5-2481-41ff-852f-4aa3945d54ec";
        (function () {
            d = document; s = d.createElement("script");
            s.src = "https://client.crisp.chat/l.js";
            s.async = 1; d.getElementsByTagName("head")[0].appendChild(s);
        })();
    </script>

    <!-- YouTube Player Script -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <div id="youtubePlayer" style="display:none;"></div>
    <script>
        var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtubePlayer', {
                height: '0',
                width: '0',
                videoId: 'phk5CULrDFg',
                playerVars: {
                    'autoplay': 0,
                    'controls': 0,
                    'disablekb': 1
                },
                events: {
                    'onReady': onPlayerReady
                }
            });
        }

        function onPlayerReady(event) {
            <?php if (isset($_SESSION['first_login']) && $_SESSION['first_login']): ?>
                event.target.playVideo();
                event.target.setVolume(30);
                <?php $_SESSION['first_login'] = false; ?>
            <?php endif; ?>
        }
    </script>
</footer>
</div>
<!-- Scripts JS -->
</body>

</html>