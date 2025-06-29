<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showSection(sectionId) {
        // Sembunyikan semua section
        document.querySelectorAll('[id^="section-"]').forEach(section => {
            section.style.display = 'none';
        });

        // Tampilkan section yang dipilih
        document.getElementById('section-' + sectionId).style.display = 'block';

        // Update active menu
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    
</script>
<!-- YouTube IFrame Player API -->
<script src="https://www.youtube.com/iframe_api"></script>

<!-- Div tempat player akan dimuat (hidden) -->
<div id="youtubePlayer" style="display:none;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var player;

    // Fungsi ini dipanggil oleh YouTube API ketika siap
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('youtubePlayer', {
            height: '0',
            width: '0',
            videoId: 'phk5CULrDFg',
            playerVars: {
                'autoplay': 0,
                'controls': 0,
                'disablekb': 1,
                'fs': 0,
                'modestbranding': 1,
                'rel': 0
            },
            events: {
                'onReady': onPlayerReady
            }
        });
    }

    function onPlayerReady(event) {
        <?php if ($_SESSION['first_login']): ?>
            event.target.playVideo();
            // Set volume menjadi 30% agar tidak terlalu keras
            event.target.setVolume(30);
            <?php $_SESSION['first_login'] = false; ?>
        <?php endif; ?>
    }

    // Fungsi untuk memainkan lagu manual (opsional)
    function playYouTubeMusic() {
        if (player) {
            player.playVideo();
            player.setVolume(30);
        }
    }

    // Chart pie untuk produk terlaris
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [/* nama produk */],
            datasets: [{
                data: [/* total terjual */],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalStok = document.getElementById('ModalStok');
        modalStok.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Tombol yang memicu modal
            var kode = button.getAttribute('data-kode');
            var nama = button.getAttribute('data-nama');
            var stok = button.getAttribute('data-stok');
            var id = button.getAttribute('data-id');

            // Update konten modal
            var modal = this;
            modal.querySelector('#kodeProduk').value = kode;
            modal.querySelector('#namaProduk').value = nama;
            modal.querySelector('#stokSekarang').value = stok;
            modal.querySelector('[name="id_produk"]').value = id;
        });
    });
</script>