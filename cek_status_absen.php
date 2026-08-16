<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once 'koneksi.php'; 

// 1. TANGKAP NIK DARI SEGALA ARAH (JSON, POST, atau GET)
$nik = "";
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, TRUE);

if (isset($data['nik'])) {
    $nik = $data['nik'];
} elseif (isset($_POST['nik'])) {
    $nik = $_POST['nik'];
} elseif (isset($_GET['nik'])) {
    $nik = $_GET['nik'];
}

$nik = trim($nik);

if (!empty($nik)) {
    date_default_timezone_set('Asia/Jakarta');
    $tanggal_hari_ini = date("Y-m-d");

    try {
        // 2. QUERY KEBAL TIPE DATA (Berfungsi untuk tipe kolom DATE maupun DATETIME)
        $query = "SELECT waktu_masuk, waktu_pulang FROM transaksi_absensi 
                  WHERE NIK = :nik AND DATE(tanggal) = :tanggal
                  ORDER BY waktu_masuk DESC LIMIT 1";
        
        $stmt = $koneksi->prepare($query);
        $stmt->bindParam(':nik', $nik);
        $stmt->bindParam(':tanggal', $tanggal_hari_ini);
        $stmt->execute();
        
        $hasil = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hasil) {
            if (!empty($hasil['waktu_pulang'])) {
                echo json_encode(["sudah_masuk" => true, "sudah_keluar" => true]);
            } else {
                echo json_encode(["sudah_masuk" => true, "sudah_keluar" => false]);
            }
        } else {
            // 3. PESAN DEBUG JIKA GAGAL
            echo json_encode([
                "sudah_masuk" => false, 
                "sudah_keluar" => false,
                "debug_pesan" => "Database kosong untuk NIK [$nik] pada tanggal [$tanggal_hari_ini]."
            ]);
        }

    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error DB: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Sistem tidak menerima NIK sama sekali."]);
}
?>