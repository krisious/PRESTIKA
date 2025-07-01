<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PrestasiPrintController extends Controller
{
    public function print(Request $request)
    {
        // Cek apakah user login dan memiliki role 'Siswa'
    if (Auth::check() && Auth::user()->hasRole('Siswa')) {
        $idSiswa = \App\Models\Siswa::where('id_user', Auth::user()->id)->value('id');

        // Ambil hanya prestasi milik siswa tersebut
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
        ])->where('id_siswa', $idSiswa)
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
        // Jika admin/guru atau role lain, ambil semua data
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
        ])->where('status', 'diterima')
        ->orderBy('tanggal_perolehan', 'asc')
        ->orderBy('nama_lomba', 'asc')
        ->get();
    }

    $pdf = PDF::loadView('pdf.prestasi', compact('prestasi'))->setPaper('a4', 'landscape');

    return $pdf->stream('laporan_prestasi.pdf');
    }
}
