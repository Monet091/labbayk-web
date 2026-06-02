<?php
    // 1. Aturan keamanan biar HTML lo bisa ngakses file ini
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");

    // 2. MEMANGGIL KODE KONEKSI DARI TEMAN LO
    require_once 'koneksi.php'; 

    // 3. Menerima data NIK & Password dari HTML lo
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->nik) && !empty($data->password)) {
        $nik = trim($data->nik);
        $password = trim($data->password);

    try {
            // =========================================================================
            // QUERY DENGAN NIK DAN MASTER_KARYAWAN
            // =========================================================================
            $query = "SELECT * FROM master_karyawan WHERE NIK = :nik AND password = :password LIMIT 1";
            
            $stmt = $koneksi->prepare($query);
            
            // Kita ikat kantong :nik dengan data NIK/User ID dari HTML lo
            $stmt->bindParam(':nik', $nik); 
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Mencari nama karyawan di database. 
                // Kalau nama kolomnya di database teman lo 'nama_karyawan' atau 'nama', tinggal sesuaikan di kanan ini ya.
                // Untuk sementara kita coba pakai data 'nama' dulu atau field pertama yang ada.
                $namaUser = isset($user['nama']) ? $user['nama'] : (isset($user['nama_karyawan']) ? $user['nama_karyawan'] : 'Karyawan');

                echo json_encode([
                    "status" => "success",
                    "nama" => $namaUser
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "NIK atau password salah."
                ]);
            }

        } catch(PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Terjadi kesalahan database: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Data tidak lengkap."
        ]);
    }
?>