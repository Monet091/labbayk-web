<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once 'koneksi.php'; 

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nik)) {
    $nik = trim($data->nik);

    date_default_timezone_set('Asia/Jakarta');
    $tanggal_hari_ini = date("Y-m-d");
    $jam_sekarang = date("H:i:s");

    try {
        // UPDATE record absensi hari ini, isi bagian waktu_keluar
        $query = "UPDATE transaksi_absensi SET waktu_keluar = :waktu 
                  WHERE NIK = :nik AND tanggal = :tanggal AND waktu_keluar IS NULL";
        
        $stmt = $koneksi->prepare($query);
        $stmt->bindParam(':nik', $nik); 
        $stmt->bindParam(':tanggal', $tanggal_hari_ini); 
        $stmt->bindParam(':waktu', $jam_sekarang); 

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Absen keluar berhasil dicatat!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal absen keluar. Pastikan Anda sudah absen masuk dan belum absen keluar hari ini."]);
        }

    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error Database: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Sistem tidak mendeteksi NIK."]);
}
?>