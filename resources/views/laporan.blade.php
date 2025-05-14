<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Prestasi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px; text-align: left; }
    </style>
</head>
<body>
    <h2>Laporan Data Prestasi</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th>Jurusan</th>
                <th>Nama Lomba</th>
                <th>Kategori</th>
                <th>Subkategori</th>
                <th>Tingkat</th>
                <th>Peringkat</th>
                <th>Delegasi</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $record->siswa->user->nama ?? '-' }}</td>
                    <td>{{ $record->siswa->jurusan->jurusan ?? '-' }}</td>
                    <td>{{ $record->nama_lomba }}</td>
                    <td>{{ $record->kategoriPrestasi->kategori ?? '-' }}</td>
                    <td>{{ $record->subkategoriPrestasi->subkategori ?? '-' }}</td>
                    <td>{{ $record->tingkatPrestasi->tingkat ?? '-' }}</td>
                    <td>{{ $record->peringkatPrestasi->peringkat ?? '-' }}</td>
                    <td>{{ $record->delegasi->delegasi ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->tanggal_perolehan)->format('d-m-Y') }}</td>
                    <td>{{ $record->lokasi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
