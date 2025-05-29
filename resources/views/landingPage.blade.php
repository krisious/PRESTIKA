@extends('layout.master')

@section('content')
    <section class="hero-section d-flex align-items-center" style="min-height: 100vh; background: url('{{ asset('smkn1.png') }}') center center / cover no-repeat; position: relative;">
        <!-- Overlay gelap agar teks kontras -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); z-index: 1;"></div>

        <div class="container position-relative z-2 text-white">
            <div class="row align-items-center">
                <!-- Teks bagian kiri -->
                <div class="col-md-6">
                    <h1 class="display-5 fw-bold mb-4">Pusat Prestasi<br>SMKN 1 Kota Bekasi</h1>
                    <p class="lead mb-4 fw-normal">Selamat datang di Pusat Prestasi SMKN 1 Kota Bekasi, tempat di mana prestasi siswa kami bersinar.</p>
                    <a href="{{ url(config('filament.path') ?? 'admin') }}" class="btn btn-warning fw-semibold px-4 py-2 rounded-pill shadow-sm">
                        Data Prestasimu!
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection