<?php
session_start();
require_once 'koneksi.php'; 

// Jika ada session NIK dari login, gunakan itu. Jika belum ada, bisa menggunakan input NIP manual
$nik_login = isset($_SESSION['nik']) ? $_SESSION['nik'] : '';

if (isset($_POST['submit_izin'])) {
    $tgl_mulai    = $_POST['tgl_mulai'];
    $tgl_selesai  = $_POST['tgl_selesai'];
    $nik          = !empty($_POST['nik']) ? $_POST['nik'] : $nik_login; 
    $id_tipe_izin = $_POST['id_tipe_izin'];
    $alasan       = $_POST['alasan'];
    
    // Status awal di-set 'Pending' agar sesuai dengan enum/pilihan di NetBeans
    $status_izin  = 'Pending'; 
    $tgl_dibuat   = date('Y-m-d H:i:s');
    $nama_file    = NULL; 

    // Logika Upload Attachment
    if (isset($_FILES['file_surat_dokter']) && $_FILES['file_surat_dokter']['error'] == 0) {
        $target_dir = "uploads/";
        
        // Buat folder uploads jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $nama_file = time() . "_" . basename($_FILES["file_surat_dokter"]["name"]);
        $target_file = $target_dir . $nama_file;
        
        move_uploaded_file($_FILES["file_surat_dokter"]["tmp_name"], $target_file);
    }

    try {
        // Menggunakan tabel 'transaksi_pengajuan_izin' agar dibaca oleh NetBeans
        $sql = "INSERT INTO transaksi_pengajuan_izin (NIK, id_tipe_izin, tgl_mulai, tgl_selesai, alasan, file_surat_dokter, status_izin, tgl_dibuat) 
                VALUES (:nik, :id_tipe_izin, :tgl_mulai, :tgl_selesai, :alasan, :file_surat_dokter, :status_izin, :tgl_dibuat)";
        
        $stmt = $koneksi->prepare($sql);
        $stmt->bindParam(':nik', $nik);
        $stmt->bindParam(':id_tipe_izin', $id_tipe_izin);
        $stmt->bindParam(':tgl_mulai', $tgl_mulai);
        $stmt->bindParam(':tgl_selesai', $tgl_selesai);
        $stmt->bindParam(':alasan', $alasan);
        $stmt->bindParam(':file_surat_dokter', $nama_file);
        $stmt->bindParam(':status_izin', $status_izin);
        $stmt->bindParam(':tgl_dibuat', $tgl_dibuat);
        
        $stmt->execute();
        
        echo "<script>
                alert('Pengajuan izin berhasil dikirim!'); 
                window.location.href='izin_update.php';
              </script>";
    } catch(PDOException $e) {
        echo "<script>alert('Gagal menyimpan data: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Formulir Pengajuan Izin/Cuti</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'olive': '#7A846E',
          }
        }
      }
    }
  </script>
  <style>
    input[type="date"]::-webkit-calendar-picker-indicator {
      opacity: 0;
      position: absolute;
      right: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }
  </style>
</head>
<body class="min-h-screen bg-[#a9af9e]">

  <div class="w-full bg-white">
    <div class="bg-white flex items-center px-6 py-4 shadow-md shrink-0 gap-3">
      <button onclick="window.location.href='izin_pilihan.html'" class="text-gray-600 hover:text-gray-900 transition-colors mr-1 flex items-center justify-center" aria-label="Kembali">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-7 md:w-7" fill="none" 
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </button>
      <span class="text-gray-900 font-bold text-lg md:text-xl tracking-wider uppercase">Ajukan Izin</span>
    </div>

    <div class="px-5 py-5 max-w-xl mx-auto">
      <h1 class="text-center text-base font-bold uppercase tracking-widest mb-5 text-gray-800">
        Formulir Cuti
      </h1>

      <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-800 mb-1">Tanggal izin :</label>
          <div class="relative">
            <input type="date" name="tgl_mulai" required
              class="w-full bg-gray-200 rounded px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A846E] pr-10" />
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-800 mb-1">Tanggal selesai :</label>
          <div class="relative">
            <input type="date" name="tgl_selesai" required
              class="w-full bg-gray-200 rounded px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A846E] pr-10" />
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-800 mb-1">Nama Karyawan :</label>
          <input type="text" name="nama_karyawan"
            class="w-full bg-gray-200 rounded px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A846E]" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-800 mb-1">NIP :</label>
          <input type="text" name="nik" value="<?= htmlspecialchars($nik_login) ?>" required
            class="w-full bg-gray-200 rounded px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A846E]" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-800 mb-1">ID Tipe Izin :</label>
          <input type="number" name="id_tipe_izin" required placeholder="Contoh: 1"
            class="w-full bg-gray-200 rounded px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A846E]" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-800 mb-1">Alasan :</label>
          <textarea name="alasan" rows="5" required
            class="w-full bg-gray-200 rounded px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A846E] resize-none"></textarea>
        </div>

        <!-- Attachment dengan Icon Paperclip -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-800 mb-1">*Attachment</label>
          <label class="w-full bg-gray-200 rounded px-3 py-4 text-sm text-gray-600 flex justify-center items-center cursor-pointer hover:bg-gray-300 transition-colors">
            
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
            
            <input type="file" id="file_attachment" name="file_surat_dokter" class="hidden" accept="image/*,.pdf" />
          </label>
          
          <div id="file_name_display" class="mt-2 text-sm text-center font-semibold text-[#7A846E]"></div>
        </div>

        <div class="flex justify-end">
          <button type="submit" name="submit_izin"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-sm uppercase px-5 py-2 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-[#7A846E]">
            Kirim
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const fileInput = document.getElementById('file_attachment');
    const fileNameDisplay = document.getElementById('file_name_display');

    fileInput.addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        fileNameDisplay.textContent = "Berhasil diunggah: " + file.name;
      } else {
        fileNameDisplay.textContent = "";
      }
    });
  </script>
</body>
</html>