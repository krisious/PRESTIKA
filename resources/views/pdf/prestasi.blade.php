<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Prestasi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
        }

        .ta-header {
            background-color: #dcdcdc;
            font-weight: bold;
            text-align: center;
            padding: 6px;
        }

        .signature-block {
            margin-top: 50px;
            text-align: right;
            width: 300px;
            margin-left: auto;
            padding-right: 20px;
        }

        .signature-block p {
            margin: 0;
            line-height: 1.5;
        }

        .signature-block .name {
            margin-top: 60px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Laporan Prestasi Siswa</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Jurusan</th>
                <th>Peran</th>
                <th>Nama Lomba</th>
                <th>Kategori</th>
                <th>Subkategori</th>
                <th>Tingkat</th>
                <th>Peringkat</th>
                <th>Delegasi</th>
                <th>Tanggal</th>
                <th>Penyelenggara</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grouped = $prestasi->groupBy(fn($p) => $p->tahunAjaran->tahun ?? 'Tanpa Tahun Ajaran');
                $no = 1;
            @endphp

            @foreach ($grouped as $tahun => $items)
                <tr>
                    <td colspan="14" class="ta-header">Tahun Ajaran: {{ $tahun }}</td>
                </tr>

                @foreach ($items as $item)
                    @php
                        $baris = [];

                        if (isset($idSiswa)) {
                            // Jika siswa login → tampilkan hanya dirinya
                            if ($item->id_siswa == $idSiswa) {
                                $baris[] = ['siswa' => $item->siswa, 'peran' => $item->is_kelompok ? 'Ketua' : 'Individu'];
                            }

                            foreach ($item->anggotaTim as $m) {
                                if ($m->id == $idSiswa) {
                                    $baris[] = ['siswa' => $m, 'peran' => 'Anggota'];
                                }
                            }
                        } else {
                            // Jika admin/guru → tampilkan semua siswa
                            if ($item->is_kelompok) {
                                $baris[] = ['siswa' => $item->siswa, 'peran' => 'Ketua'];
                                foreach ($item->anggotaTim as $m) {
                                    $baris[] = ['siswa' => $m, 'peran' => 'Anggota'];
                                }
                            } else {
                                $baris[] = ['siswa' => $item->siswa, 'peran' => 'Individu'];
                            }
                        }
                    @endphp

                    @foreach ($baris as $row)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $row['siswa']->nis ?? '-' }}</td>
                            <td>{{ $row['siswa']->user->name ?? '-' }}</td>
                            <td>{{ $row['siswa']->jurusan->jurusan ?? '-' }}</td>
                            <td>{{ $row['peran'] }}</td>
                            <td>{{ $item->nama_lomba }}</td>
                            <td>{{ $item->kategoriPrestasi->kategori ?? '-' }}</td>
                            <td>{{ $item->subkategoriPrestasi->subkategori ?? '-' }}</td>
                            <td>{{ $item->tingkatPrestasi->tingkat ?? '-' }}</td>
                            <td>{{ $item->peringkatPrestasi->peringkat ?? '-' }}</td>
                            <td>{{ $item->delegasi->delegasi ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') }}</td>
                            <td>{{ $item->penyelenggara }}</td>
                            <td>{{ $item->lokasi }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>

    @if(Auth::check() && Auth::user()->hasRole('Siswa'))
        <div class="signature-block">
            <p>Mengetahui,</p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p class="name">[Nama Kepala Sekolah]</p>
            <p>NIP: [NIP Kepala Sekolah]</p>
        </div>
    @endif
</body>
</html>
