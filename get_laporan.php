<?php
header('Content-Type: application/json');
$koneksi = mysqli_connect("localhost", "root", "", "labbayk_hris");

if (!$koneksi) { echo json_encode([]); exit(); }

// Menarik data dari database labbayk_hris
$query = "SELECT * FROM transaksi_laporan_harian ORDER BY id_laporan ASC";
$result = mysqli_query($koneksi, $query);
$data = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Cek apakah ada file yang dilampirkan
        $file_url = !empty($row['file_laporan']) ? "uploads/" . $row['file_laporan'] : "";
        
        $data[] = [
            // Membuat format ID menjadi A001, A002, dst.
            "idLaporan" => "A" . str_pad($row['id_laporan'], 3, "0", STR_PAD_LEFT), 
            "tanggal"   => $row['tanggal'],
            "nik"       => $row['NIK'],
            "fileUrl"   => $file_url
        ];
    }
}

echo json_encode($data);
mysqli_close($koneksi);
?>