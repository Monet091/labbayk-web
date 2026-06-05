<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");

    require_once 'koneksi.php'; 

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->nik)) {
        $nik = trim($data->nik);

        // PAKSA PHP MENGGUNAKAN WAKTU JAKARTA
        date_default_timezone_set('Asia/Jakarta');
        $tanggal_hari_ini = date("Y-m-d");
        $jam_sekarang = date("H:i:s");

        try {
            // Gunakan variabel PHP, bukan perintah CURTIME MySQL
            $query = "INSERT INTO transaksi_absensi (NIK, tanggal, waktu_masuk) 
                      VALUES (:nik, :tanggal, :waktu)";
            
            $stmt = $koneksi->prepare($query);
            $stmt->bindParam(':nik', $nik); 
            $stmt->bindParam(':tanggal', $tanggal_hari_ini); 
            $stmt->bindParam(':waktu', $jam_sekarang); 

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Absen masuk berhasil dicatat!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal mencatat ke database."]);
            }

        } catch(PDOException $e) {
            // Menangkap error Duplicate Entry agar lebih manusiawi dibaca karyawan
            if ($e->getCode() == 23000) {
                echo json_encode(["status" => "error", "message" => "Anda sudah melakukan absen masuk hari ini!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error Database: " . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Sistem tidak mendeteksi NIK."]);
    }
?>