<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

require_once 'koneksi.php'; 

$nik = isset($_GET['nik']) ? trim($_GET['nik']) : '';

if (!empty($nik)) {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal_hari_ini = date("Y-m-d");

    try {
        // Cek data absensi hari ini berdasarkan NIK
        $query = "SELECT waktu_masuk, waktu_keluar FROM transaksi_absensi 
                  WHERE NIK = :nik AND tanggal = :tanggal";
        
        $stmt = $koneksi->prepare($query);
        $stmt->bindParam(':nik', $nik);
        $stmt->bindParam(':tanggal', $tanggal_hari_ini);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            if (!empty($data['waktu_keluar'])) {
                // Sudah absen masuk DAN sudah absen keluar
                echo json_encode(["sudah_masuk" => true, "sudah_keluar" => true]);
            } else {
                // Sudah absen masuk TAPI belum absen keluar (Kondisi Normal untuk Keluar)
                echo json_encode(["sudah_masuk" => true, "sudah_keluar" => false]);
            }
        } else {
            // Belum absen masuk sama sekali hari ini
            echo json_encode(["sudah_masuk" => false, "sudah_keluar" => false]);
        }

    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error Database: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "NIK tidak dilampirkan."]);
}
?>