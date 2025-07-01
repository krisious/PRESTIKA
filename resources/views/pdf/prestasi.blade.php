<!DOCTYPE html>
<html>
<head>
    <title>Laporan Prestasi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        h2 { text-align: center; margin-bottom: 0; }
        .signature-block {
            margin-top: 50px; /* Jarak dari tabel */
            text-align: right;
            width: 300px; /* Sesuaikan lebar blok tanda tangan */
            margin-left: auto; /* Memastikan blok berada di kanan */
            margin-right: 0;
            padding-right: 20px; /* Sedikit padding dari tepi kanan */
        }
        .signature-block p {
            margin: 0;
            line-height: 1.5;
        }
        .signature-block .name {
            margin-top: 60px; /* Jarak untuk tanda tangan */
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
                <th>Nama Lomba</th>
                <th>Kategori</th>
                <th>Subkategori</th>
                <th>Tingkat</th>
                <th>Peringkat</th>
                <th>Delegasi</th>
                <th>Tanggal Perolehan</th>
                <th>Penyelenggara</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>

            @php
    $no = 1;
@endphp
    @foreach ($prestasi as $item)
        @php
            $anggota = [];
            if ($item->is_kelompok) {
                $anggota[] = $item->siswa; // ketua dulu
                foreach ($item->anggotaTim as $member) {
                    $anggota[] = $member;
                }
            } else {
                $anggota[] = $item->siswa; // individu
            }
            $rowspan = count($anggota);
        @endphp

        @foreach($anggota as $index => $siswa)
            <tr>
                @if($index == 0)
                    <td rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                @endif
                <td>{{ $siswa->nis ?? '-' }}</td>
                <td>{{ $siswa->user->name ?? '-' }}</td>
                <td>{{ $siswa->jurusan->jurusan ?? '-' }}</td>
                @if($index == 0)
                    <td rowspan="{{ $rowspan }}">{{ $item->nama_lomba }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $item->kategoriPrestasi->kategori ?? '-' }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $item->subkategoriPrestasi->subkategori ?? '-' }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $item->tingkatPrestasi->tingkat ?? '-' }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $item->peringkatPrestasi->peringkat ?? '-' }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $item->delegasi->delegasi ?? '-' }}</td>
                    <td rowspan="{{ $rowspan }}">{{ \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $item->penyelenggara }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $item->lokasi }}</td>
                @endif
            </tr>
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
