<?php
session_start();
require_once 'koneksi.php';

// Cek apakah ada parameter ID yang dikirim melalui URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>ID Izin tidak ditemukan.</h3>
            <a href='izin_update.php'>Kembali</a>
         </div>");
}

$id_izin = $_GET['id'];
$data_izin = null;

try {
    // Ambil semua data dari tabel berdasarkan id_izin
    $sql = "SELECT * FROM transaksi_pengajuan_izin WHERE id_izin = :id LIMIT 1";
    $stmt = $koneksi->prepare($sql);
    $stmt->bindParam(':id', $id_izin);
    $stmt->execute();
    
    $data_izin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data_izin) {
        die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h3>Data pengajuan tidak ditemukan di database.</h3>
                <a href='izin_update.php'>Kembali</a>
             </div>");
    }
} catch(PDOException $e) {
    die("Error Database: " . $e->getMessage());
}

// Logika warna status
$status_tampil = isset($data_izin['status_izin']) ? $data_izin['status_izin'] : 'Pending';
$warna_status = "text-yellow-600"; // Menunggu
if(strtolower($status_tampil) == 'disetujui') $warna_status = "text-[#4CAF50]";
if(strtolower($status_tampil) == 'ditolak') $warna_status = "text-red-600";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Izin - PT. Labbayk Haramain Travel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-[#a9af9e]">

  <div class="w-full bg-white min-h-screen">
    <!-- Header -->
    <div class="bg-white flex items-center px-6 py-4 shadow-md shrink-0 gap-3">
      <button onclick="window.location.href='izin_update.php'" class="text-gray-600 hover:text-gray-900 transition-colors mr-1 flex items-center justify-center" aria-label="Kembali">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-7 md:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </button>
      <span class="text-gray-900 font-bold text-lg md:text-xl tracking-wider uppercase">Detail Izin</span>
    </div>

    <!-- Tampilan Data (Desain menyatu/flush dengan latar putih) -->
    <div class="px-6 py-8 max-w-xl mx-auto space-y-5">
      <h1 class="text-center text-base font-bold uppercase tracking-widest mb-6 text-gray-800">
        Informasi Pengajuan
      </h1>

      <div class="border-b border-gray-200 pb-3">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Tanggal Dibuat</label>
        <div class="text-sm text-gray-800 mt-1 font-medium">
            <?= date('d M Y - H:i', strtotime($data_izin['tgl_dibuat'])) ?> WIB
        </div>
      </div>

      <div class="border-b border-gray-200 pb-3">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">NIK Karyawan</label>
        <div class="text-sm text-gray-800 mt-1 font-medium">
            <?= htmlspecialchars($data_izin['NIK']) ?>
        </div>
      </div>

      <div class="border-b border-gray-200 pb-3">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">ID Tipe Izin</label>
        <div class="text-sm text-gray-800 mt-1 font-medium">
            <?= htmlspecialchars($data_izin['id_tipe_izin']) ?>
        </div>
      </div>

      <div class="flex gap-4 border-b border-gray-200 pb-3">
        <div class="flex-1">
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Tgl Mulai</label>
          <div class="text-sm text-gray-800 mt-1 font-medium">
              <?= date('d M Y', strtotime($data_izin['tgl_mulai'])) ?>
          </div>
        </div>
        <div class="flex-1">
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Tgl Selesai</label>
          <div class="text-sm text-gray-800 mt-1 font-medium">
              <?= date('d M Y', strtotime($data_izin['tgl_selesai'])) ?>
          </div>
        </div>
      </div>

      <div class="border-b border-gray-200 pb-3">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Status Saat Ini</label>
        <div class="text-sm font-bold mt-1 <?= $warna_status ?>">
          <?= htmlspecialchars($status_tampil) ?>
        </div>
      </div>

      <div class="border-b border-gray-200 pb-3">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Alasan</label>
        <div class="text-sm text-gray-800 mt-2 whitespace-pre-wrap leading-relaxed"><?= htmlspecialchars($data_izin['alasan']) ?></div>
      </div>

      <div class="pb-3 pt-2">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Lampiran Bukti</label>
        <?php if (!empty($data_izin['file_surat_dokter'])): ?>
            <div class="bg-gray-100 p-4 rounded flex items-center justify-between border border-gray-200">
                <span class="text-sm text-gray-700 truncate mr-4 font-medium">
                    <?= htmlspecialchars($data_izin['file_surat_dokter']) ?>
                </span>
                <a href="uploads/<?= htmlspecialchars($data_izin['file_surat_dokter']) ?>" target="_blank" 
                   class="bg-[#7A846E] hover:bg-[#666f5c] text-white text-xs font-bold py-2 px-4 rounded transition-colors whitespace-nowrap shadow-sm">
                    Lihat Dokumen
                </a>
            </div>
        <?php else: ?>
            <div class="text-sm text-gray-500 italic bg-gray-50 p-4 rounded border border-gray-200 text-center">
                Tidak ada dokumen yang dilampirkan.
            </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

</body>
</html>