<?php
session_start();

// --- [SIMULASI LOGIN] ---
if (!isset($_SESSION['nik'])) {
    $_SESSION['nik'] = '00192'; // Sesuaikan dengan NIK yang lu pakai buat testing
}
// ------------------------

$nik_login = $_SESSION['nik'];
$nik_input = trim($_POST['nik']); 

// BENTENG TERAKHIR: Tolak keras jika NIK input beda dari NIK login
if ($nik_input !== $nik_login) {
    echo "<script>
            alert('Akses Ilegal: NIK tidak sesuai dengan sesi Anda.'); 
            window.history.back();
          </script>";
    exit();
}

mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
try {
    $koneksi = mysqli_connect("localhost", "root", "", "labbayk_hris");
    
    $tanggal   = $_POST['tanggal']; 
    $deskripsi = $_POST['deskripsi_pekerjaan'];
    $nama_file_final = NULL; // Default jika tidak ada file

    // ==========================================
    // PROSES UPLOAD & RENAME FILE OTOMATIS
    // ==========================================
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === 0) {
        $nama_asli = $_FILES['file_laporan']['name'];
        $tmp_name  = $_FILES['file_laporan']['tmp_name'];
        
        // 1. Arahkan target masuk ke dalam sub-folder "laporan"
        $folder_upload = "uploads/laporan/";
        if (!is_dir($folder_upload)) { 
            mkdir($folder_upload, 0777, true); 
        }
        
        // 2. Buat format nama file baru (Contoh: 00192_20260811_153000_LAPORAN_namafile.jpg)
        $waktu = date('Ymd_His');
        $nama_file_final = $nik_input . "_" . $waktu . "_LAPORAN_" . $nama_asli;
        
        // 3. Pindahkan file dari memori sementara ke folder target dengan nama baru
        move_uploaded_file($tmp_name, $folder_upload . $nama_file_final);
    }

    // Query untuk menyimpan nama file yang SUDAH DIRAPIKAN ke database
    $query = "INSERT INTO transaksi_laporan_harian (NIK, tanggal, deskripsi_pekerjaan, file_laporan) 
              VALUES ('$nik_input', '$tanggal', '$deskripsi', '$nama_file_final')";

    mysqli_query($koneksi, $query);
    
    // Kalau sukses, langsung pindah ke halaman history
    header("Location: laporan_history.html");
    exit();

} catch (mysqli_sql_exception $e) {
    echo "<script>alert('Terjadi kesalahan database!'); window.history.back();</script>";
}

if (isset($koneksi)) { 
    mysqli_close($koneksi); 
}
?>