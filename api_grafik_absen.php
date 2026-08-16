<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'koneksi.php';

$nik = isset($_GET['nik']) ? trim($_GET['nik']) : '';

if (!empty($nik)) {
    // Ambil bulan dan tahun saat ini
    $bulan_ini = date('m');
    $tahun_ini = date('Y');

    try {
        // 1. Hitung Total Hadir (Berdasarkan absen masuk di bulan ini)
        $queryHadir = "SELECT COUNT(*) as total_hadir FROM transaksi_absensi 
                       WHERE NIK = :nik AND MONTH(tanggal) = :bulan AND YEAR(tanggal) = :tahun";
        $stmtHadir = $koneksi->prepare($queryHadir);
        $stmtHadir->bindParam(':nik', $nik);
        $stmtHadir->bindParam(':bulan', $bulan_ini);
        $stmtHadir->bindParam(':tahun', $tahun_ini);
        $stmtHadir->execute();
        $hadir = $stmtHadir->fetch(PDO::FETCH_ASSOC)['total_hadir'];

        // 2. Hitung Total Izin/Sakit (Yang statusnya 'Disetujui' di bulan ini)
        $queryIzin = "SELECT COUNT(*) as total_izin FROM transaksi_pengajuan_izin 
                      WHERE NIK = :nik AND LOWER(status_izin) = 'disetujui' 
                      AND MONTH(tgl_mulai) = :bulan AND YEAR(tgl_mulai) = :tahun";
        $stmtIzin = $koneksi->prepare($queryIzin);
        $stmtIzin->bindParam(':nik', $nik);
        $stmtIzin->bindParam(':bulan', $bulan_ini);
        $stmtIzin->bindParam(':tahun', $tahun_ini);
        $stmtIzin->execute();
        $izin = $stmtIzin->fetch(PDO::FETCH_ASSOC)['total_izin'];

        // 3. Kalkulasi Alpa (Simulasi: Asumsi total hari kerja efektif sebulan adalah 22 hari)
        $hari_kerja_sebulan = 22;
        $alpa = $hari_kerja_sebulan - ($hadir + $izin);
        if ($alpa < 0) $alpa = 0; // Mencegah angka menjadi minus jika rajin masuk

        // 4. Hitung Persentase Kehadiran
        $total_tercatat = $hadir + $izin + $alpa;
        $persentase = ($total_tercatat > 0) ? round(($hadir / $hari_kerja_sebulan) * 100) : 0;
        if ($persentase > 100) $persentase = 100;

        // Kirimkan data kembali dalam format JSON
        echo json_encode([
            "status" => "success",
            "data" => [
                "hadir" => (int)$hadir,
                "izin" => (int)$izin,
                "alpa" => (int)$alpa,
                "persentase" => $persentase
            ]
        ]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "NIK tidak ditemukan."]);
}
?>