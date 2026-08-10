<?php
session_start();
require_once 'koneksi.php';


$nik_karyawan = $_SESSION['nik']; 
$riwayat_izin = [];

try {
    // Kueri langsung ke tabel utama karena NetBeans meng-update tabel ini secara langsung
    $sql = "SELECT * FROM transaksi_pengajuan_izin WHERE NIK = :nik ORDER BY tgl_dibuat DESC";
    
    $stmt = $koneksi->prepare($sql);
    $stmt->bindParam(':nik', $nik_karyawan);
    $stmt->execute();
    
    $riwayat_izin = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Izin - PT. Labbayk Haramain Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#a4ad9f] min-h-screen flex flex-col justify-between">

    <div class="w-full flex flex-col flex-grow">
        
       <div class="bg-white flex items-center px-6 py-4 shadow-md shrink-0 gap-3">
        <button onclick="window.location.href='izin_pilihan.html'" class="text-gray-600 hover:text-gray-900 transition-colors mr-1 flex items-center justify-center" aria-label="Kembali">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-7 md:w-7" fill="none" 
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </button>
        <span class="text-gray-900 font-bold text-lg md:text-xl tracking-wider uppercase">Update Izin</span>
        </div>

        <h2 class="text-[#2a2a2a] text-lg md:text-xl font-bold tracking-normal px-6 pt-6 pb-2 md:px-12 md:pt-8">
            DAFTAR PENGAJUAN IZINKU
        </h2>

        <div class="bg-white mx-6 my-2 p-4 md:mx-12 md:p-6 rounded shadow-sm overflow-x-auto">
            <table class="w-full border-collapse text-[11px] md:text-sm text-left text-[#4c4c4c]">
                <thead>
                    <tr class="border border-[#7c7c7c] bg-gray-50/50">
                        <th class="border border-[#7c7c7c] p-2 md:p-3 font-bold">Tgl Mulai</th>
                        <th class="border border-[#7c7c7c] p-2 md:p-3 font-bold">Tgl Selesai</th>
                        <th class="border border-[#7c7c7c] p-2 md:p-3 font-bold">Status</th>
                        <th class="border border-[#7c7c7c] p-2 md:p-3 font-bold text-center">Detail</th>
                    </tr>
                </thead>
                
                <tbody id="tabel-absen">
                    <?php if(empty($riwayat_izin)): ?>
                        <tr>
                            <td colspan="4" class="text-center p-4 text-gray-500 italic">
                                Belum ada riwayat pengajuan izin.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($riwayat_izin as $row): ?>
                            <?php 
                                // PERBAIKAN: Membaca langsung dari kolom status_izin
                                // Menggunakan pengecekan isset untuk memastikan tidak ada pesan error jika kolom kosong
                                $status_tampil = isset($row['status_izin']) ? $row['status_izin'] : 'Menunggu';
                                
                                $warna_status = "text-yellow-600"; // Warna default untuk Menunggu / Pending
                                if(strtolower($status_tampil) == 'disetujui') {
                                    $warna_status = "text-[#4CAF50]";
                                } else if(strtolower($status_tampil) == 'ditolak') {
                                    $warna_status = "text-red-600";
                                }
                            ?>

                            <tr class="border border-[#7c7c7c] hover:bg-gray-50/50 transition-colors">
                                <td class="border border-[#7c7c7c] p-2 md:p-3 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($row['tgl_mulai'])) ?>
                                </td>
                                <td class="border border-[#7c7c7c] p-2 md:p-3 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($row['tgl_selesai'])) ?>
                                </td>
                                <td class="border border-[#7c7c7c] p-2 md:p-3 font-semibold whitespace-nowrap <?= $warna_status ?>">
                                    <span class="inline-flex items-center">
                                        <?= htmlspecialchars($status_tampil) ?> 
                                    </span>
                                </td>
                                <td class="border border-[#7c7c7c] p-2 md:p-3 text-center">
                                    <a href="izin_detail_form.php?id=<?= isset($row['id_izin']) ? $row['id_izin'] : '' ?>" class="text-[#0000ee] underline font-medium hover:text-blue-800">LIHAT</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="h-16 md:h-24"></div>

</body>
</html>