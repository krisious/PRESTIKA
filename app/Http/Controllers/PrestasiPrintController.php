<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestasi;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PrestasiPrintController extends Controller
{
    public function print(Request $request)
    {
        $idSiswa = null;

        // Cek apakah user login dan memiliki role 'Siswa'
        if (Auth::check() && Auth::user()->hasRole('Siswa')) {
            $idSiswa = Siswa::where('id_user', Auth::id())->value('id');

            // Ambil hanya prestasi milik siswa tersebut (baik sebagai pelapor utama maupun anggota)
            $prestasi = Prestasi::with([
                    'siswa.user',
                    'siswa.jurusan',
                    'kategoriPrestasi',
                    'subkategoriPrestasi',
                    'tingkatPrestasi',
                    'peringkatPrestasi',
                    'delegasi',
                    'anggotaTim.user',
                    'anggotaTim.jurusan',
                    'tahunAjaran',
                ])
                ->where('status', 'diterima')
                ->where(function ($q) use ($idSiswa) {
                    $q->where('id_siswa', $idSiswa)
                    ->orWhereHas('anggotaTim', function ($q2) use ($idSiswa) {
                        $q2->where('siswa_id', $idSiswa);
                    });
                })
                ->orderBy('tanggal_perolehan', 'asc')
                ->orderBy('nama_lomba', 'asc')
                ->get();
        } else {
            // Admin/guru: ambil semua prestasi diterima
            $prestasi = Prestasi::with([
                    'siswa.user',
                    'siswa.jurusan',
                    'kategoriPrestasi',
                    'subkategoriPrestasi',
                    'tingkatPrestasi',
                    'peringkatPrestasi',
                    'delegasi',
                    'anggotaTim.user',
                    'anggotaTim.jurusan',
                    'tahunAjaran',
                ])
                ->where('status', 'diterima')
                ->orderBy('tanggal_perolehan', 'asc')
                ->orderBy('nama_lomba', 'asc')
                ->get();
        }

        // Kirim juga idSiswa untuk keperluan view (menentukan peran)
        $pdf = PDF::loadView('pdf.prestasi', [
                'prestasi' => $prestasi,
                'idSiswa' => $idSiswa,
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan_prestasi.pdf');
    }
}
