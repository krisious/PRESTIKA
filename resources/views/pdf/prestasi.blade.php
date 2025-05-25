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
    </style>
</head>
<body>
    <h2>Laporan Prestasi Siswa</h2>
    <table>
        <thead>
            <tr>
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
            @foreach ($prestasi as $item)
                <tr>
                    <td>{{ $item->siswa->nis ?? '-' }}</td>
                    <td>{{ $item->siswa->user->name ?? '-' }}</td>
                    <td>{{ $item->siswa->jurusan->jurusan ?? '-' }}</td>
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
        </tbody>
    </table>
</body>
</html>
