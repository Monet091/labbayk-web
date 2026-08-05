<?php
// Mengaktifkan mode exception untuk MySQLi (standar PHP modern)
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

try {
    // 1. Buka Koneksi
    $koneksi = mysqli_connect("localhost", "root", "", "labbayk_hris");

    // 2. Tangkap data
    $nik       = $_POST['nik'];
    $tanggal   = $_POST['tanggal']; 
    $deskripsi = $_POST['deskripsi_pekerjaan'];
    $nama_file = NULL;

    // 3. Tangani Upload File
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === 0) {
        $nama_file = $_FILES['file_laporan']['name'];
        $tmp_name  = $_FILES['file_laporan']['tmp_name'];
        $folder_upload = "uploads/";
        
        if (!is_dir($folder_upload)) { mkdir($folder_upload, 0777, true); }
        move_uploaded_file($tmp_name, $folder_upload . $nama_file);
    }

    // 4. Query Insert
    $query = "INSERT INTO transaksi_laporan_harian (NIK, tanggal, deskripsi_pekerjaan, file_laporan) 
              VALUES ('$nik', '$tanggal', '$deskripsi', '$nama_file')";

    // Eksekusi query
    mysqli_query($koneksi, $query);

    // 5. Jika sukses tanpa error, langsung pindah ke history
    header("Location: laporan_history.html");
    exit();

} catch (mysqli_sql_exception $e) {
    // 6. TANGKAP ERROR (Agar tidak muncul layar putih Fatal Error)
    
    // Cek apakah pesan errornya mengandung kata 'foreign key' (masalah NIK tidak terdaftar)
    if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
        echo "<script>
                alert('GAGAL: NIK ($nik) belum terdaftar di tabel Master Karyawan! Silakan gunakan NIK yang valid.'); 
                window.history.back();
              </script>";
    } else {
        // Jika ada error database lain di luar masalah NIK
        echo "<script>
                alert('Terjadi kesalahan database!'); 
                window.history.back();
              </script>";
    }
}

// Tutup koneksi jika koneksi berhasil terbuka sebelumnya
if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>