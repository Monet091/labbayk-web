<?php
session_start();

// --- [SIMULASI LOGIN] ---
// Disesuaikan dengan data akun lu yang lagi aktif
if (!isset($_SESSION['nik'])) {
    $_SESSION['nik'] = 'PPOK001'; 
}
// ------------------------

$nik_login = $_SESSION['nik'];

header('Content-Type: application/json');
$koneksi = mysqli_connect("localhost", "root", "", "labbayk_hris");

if (!$koneksi) { 
    echo json_encode([]); 
    exit(); 
}

$query = "SELECT * FROM transaksi_laporan_harian WHERE NIK = '$nik_login' ORDER BY id_laporan ASC";
$result = mysqli_query($koneksi, $query);
$data = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $nama_file = $row['file_laporan'];
        $file_url = "";

        if (!empty($nama_file)) {
            // YANG DIGANTI: Menggunakan rawurlencode() agar spasi berubah jadi %20 (bukan +)
            if (file_exists("uploads/laporan/" . $nama_file)) {
                $file_url = "uploads/laporan/" . rawurlencode($nama_file);
            } 
            else if (file_exists("uploads/" . $nama_file)) {
                $file_url = "uploads/" . rawurlencode($nama_file);
            } 
            else {
                $file_url = "uploads/laporan/" . rawurlencode($nama_file); 
            }
        }
        
        $data[] = [
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