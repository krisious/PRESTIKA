<style>
    .navbar-transparent {
        background-color: transparent;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .navbar-scrolled {
        background-color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Warna teks saat transparan */
    .navbar-transparent .navbar-brand span {
        color: white;
    }

    /* Warna teks saat scroll */
    .navbar-scrolled .navbar-brand span {
        color: #000;
    }
</style>


<nav id="mainNavbar" class="navbar navbar-expand-lg fixed-top navbar-transparent">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src="{{ asset('logosmk1.png') }}" alt="Logo" width="40" height="40" class="rounded-circle">
        </a>

        <div class="ms-auto">
            <a href="{{ url(config('filament.path') ?? 'admin') }}" class="btn btn-warning fw-semibold px-4 py-2 rounded-pill shadow-sm">
                Login
            </a>
        </div>
    </div>

    <script>
    window.addEventListener('scroll', function () {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
            navbar.classList.remove('navbar-transparent');
        } else {
            navbar.classList.add('navbar-transparent');
            navbar.classList.remove('navbar-scrolled');
        }
    });

    // Jalankan saat halaman dimuat (untuk posisi reload tengah halaman)
    document.addEventListener("DOMContentLoaded", function () {
        window.dispatchEvent(new Event('scroll'));
    });
</script>

</nav>
