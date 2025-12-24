<?php
include 'koneksi.php';

// Mengambil 1 data terbaru berdasarkan ID terbesar
$sql = "SELECT * FROM logs ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Mengirim data dalam format JSON agar bisa dibaca oleh JavaScript di index.php
    echo json_encode($row);
} else {
    // Jika database masih kosong
    echo json_encode([
        "jarak" => 0, 
        "status_led" => 0, 
        "status_buzzer" => 0, 
        "waktu" => "-"
    ]);
}

$conn->close();
?>