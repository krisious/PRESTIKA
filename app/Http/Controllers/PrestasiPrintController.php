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
        ])->where('id_siswa', $idSiswa)
          ->orderBy('tanggal_perolehan', 'asc')
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
        ])->orderBy('tanggal_perolehan', 'asc')->get();
    }

    $pdf = PDF::loadView('pdf.prestasi', compact('prestasi'))->setPaper('a4', 'landscape');

    return $pdf->stream('laporan_prestasi.pdf');
    }
}
