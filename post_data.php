<?php
include 'koneksi.php';

// Cek apakah ada data yang dikirim dari ESP32
if (isset($_POST['jarak'])) {
    
    // 1. Ambil data dari Arduino (Namanya harus persis dengan di Arduino)
    $jarak = $_POST['jarak'];
    
    // Arduino kirim dengan nama "led" dan "buzzer"
    // Kalau kosong/error, kita anggap 0
    $led = isset($_POST['led']) ? $_POST['led'] : 0;
    $buzzer = isset($_POST['buzzer']) ? $_POST['buzzer'] : 0;

    // 2. Masukkan ke Database
    // Pastikan nama kolom di dalam kurung (...) sesuai dengan di phpMyAdmin kamu
    // Biasanya: jarak, status_led, status_buzzer
    $sql = "INSERT INTO logs (jarak, status_led, status_buzzer) VALUES ('$jarak', '$led', '$buzzer')";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil disimpan";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Tidak ada data POST yang diterima.";
}

$conn->close();
?>