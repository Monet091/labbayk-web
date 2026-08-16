<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once 'koneksi.php';

$nik = isset($_GET['nik']) ? $_GET['nik'] : '';

if (!empty($nik)) {
    try {
        // Asumsi nama tabel adalah master_karyawan
        $query = "SELECT * FROM master_karyawan WHERE NIK = :nik LIMIT 1";
        $stmt = $koneksi->prepare($query);
        $stmt->bindParam(':nik', $nik);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode(["status" => "success", "data" => $user]);
        } else {
            echo json_encode(["status" => "error", "message" => "Data karyawan tidak ditemukan."]);
        }
    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error DB: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "NIK tidak valid."]);
}
?>