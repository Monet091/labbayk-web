<?php
session_start();

// --- [SIMULASI LOGIN] ---
// Set manual NIK yang lagi aktif (nanti hapus kalau udah ada halaman login beneran)
if (!isset($_SESSION['nik'])) {
    $_SESSION['nik'] = '00192'; 
}

header('Content-Type: application/json');
echo json_encode(['nik' => $_SESSION['nik']]);
?>