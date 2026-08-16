<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once 'koneksi.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nik)) {
    $nik = trim($data->nik);
    $email = isset($data->email) ? trim($data->email) : '';
    $no_telp = isset($data->no_telp) ? trim($data->no_telp) : '';
    $password_lama = isset($data->password_lama) ? trim($data->password_lama) : '';
    $password_baru = isset($data->password_baru) ? trim($data->password_baru) : '';

    try {
        $update_pass_query = "";
        
        // Logika Ganti Password
        if (!empty($password_baru)) {
            // Cek kebenaran password lama terlebih dahulu
            $cek = "SELECT password FROM master_karyawan WHERE NIK = :nik LIMIT 1";
            $stmtCek = $koneksi->prepare($cek);
            $stmtCek->bindParam(':nik', $nik);
            $stmtCek->execute();
            $user = $stmtCek->fetch(PDO::FETCH_ASSOC);
            
            if ($user['password'] !== $password_lama) {
                echo json_encode(["status" => "error", "message" => "Password lama salah! Perubahan dibatalkan."]);
                exit; // Hentikan eksekusi jika password salah
            }
            // Jika benar, siapkan kueri tambahan untuk mengubah password
            $update_pass_query = ", password = :password_baru";
        }

        // Kueri Update Utama (Email dan No Telp)
        $query = "UPDATE master_karyawan SET email = :email, no_telp = :no_telp" . $update_pass_query . " WHERE NIK = :nik";
        
        $stmt = $koneksi->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':no_telp', $no_telp);
        $stmt->bindParam(':nik', $nik);
        
        if (!empty($password_baru)) {
            $stmt->bindParam(':password_baru', $password_baru);
        }
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Profil berhasil diperbarui!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui database."]);
        }

    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error DB: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap."]);
}
?>