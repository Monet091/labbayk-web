<?php
    $host = "labbayk-test-monet091.d.aivencloud.com";
    $port = "17757";
    $dbName = "labbayk_hris";
    $user = "avnadmin";
    $password = "AVNS_lzXx8ASzqwuo1jMlLef";

    try {
        $koneksi = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $user, $password, array(
            PDO::MYSQL_ATTR_SSL_CA => true,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ));
        $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Koneksi Web ke Labbayk Berhasil!";
    } catch(PDOException $e) {
        die("Koneksi Database Gagal: " . $e->getMessage());
    }
?>