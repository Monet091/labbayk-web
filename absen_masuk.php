<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");

    require_once 'koneksi.php'; 

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->nik)) {
        $nik = trim($data->nik);
        $foto_base64 = isset($data->foto) ? $data->foto : ''; 

        date_default_timezone_set('Asia/Jakarta');
        $tanggal_hari_ini = date("Y-m-d");
        $jam_sekarang = date("H:i:s");
        
        $nama_file = null;

        if (!empty($foto_base64)) {
            $image_parts = explode(";base64,", $foto_base64);
            $image_base64 = base64_decode($image_parts[1]);
            
            $nama_file = $nik . "_" . date("Ymd_His") . "_masuk.png";
            
            $target_dir = "uploads/absensi/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_path = $target_dir . $nama_file;
            file_put_contents($file_path, $image_base64);
        }

        try {
            // PERUBAHAN DI SINI: Menggunakan kolom foto_bukti
            $query = "INSERT INTO transaksi_absensi (NIK, tanggal, waktu_masuk, foto_bukti) 
                      VALUES (:nik, :tanggal, :waktu, :foto)";
            
            $stmt = $koneksi->prepare($query);
            $stmt->bindParam(':nik', $nik); 
            $stmt->bindParam(':tanggal', $tanggal_hari_ini); 
            $stmt->bindParam(':waktu', $jam_sekarang); 
            $stmt->bindParam(':foto', $nama_file); 

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Absen masuk berhasil dicatat!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal mencatat ke database."]);
            }

        } catch(PDOException $e) {
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