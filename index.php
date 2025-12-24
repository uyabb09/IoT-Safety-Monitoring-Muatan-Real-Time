<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Perahu</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            
            /* BACKGROUND IMAGE DENGAN OVERLAY (Pastikan nama file bg.jpg ada) */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            
            /* Mengubah arah layout jadi kolom (atas ke bawah) */
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            color: #333;
        }

        /* Styling Judul Utama di Atas */
        .main-title {
            color: white;
            font-size: 36px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px; /* Jarak antara judul dan kartu */
            text-shadow: 0 4px 10px rgba(0,0,0,0.5); /* Bayangan teks agar terbaca jelas */
            text-align: center;
        }

        /* Pembungkus Kartu */
        .main-wrapper {
            display: flex;
            gap: 40px; 
            align-items: flex-start;
            padding: 20px;
        }

        .container { 
            background: linear-gradient(to right, #dae2f8, #d6a4a4);
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
            text-align: center; 
            backdrop-filter: blur(5px);
        }

        .card-monitor { width: 350px; } /* Sedikit diperkecil agar proporsional */
        .card-table { width: 600px; }

        h2, h3 { color: #2c3e50; margin-top: 0; margin-bottom: 20px; }
        
        /* Judul kecil di dalam kartu */
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .jarak-display { font-size: 80px; font-weight: bold; color: #2c3e50; margin: 10px 0; }
        .unit { font-size: 24px; color: #7f8c8d; }
        
        .status-card { padding: 15px; border-radius: 12px; margin-top: 20px; font-weight: bold; font-size: 18px; transition: 0.5s; color: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .aman { background-color: #2ecc71; }    
        .normal { background-color: #f1c40f; color: #333; } 
        .bahaya { background-color: #e74c3c; animation: blink 1s infinite; } 
        
        @keyframes blink { 0% {opacity: 1;} 50% {opacity: 0.7;} 100% {opacity: 1;} }
        
        .info { margin-top: 25px; font-size: 14px; color: #95a5a6; border-top: 1px solid #eee; padding-top: 15px; }
        .device-status { display: flex; justify-content: space-around; margin-top: 10px; }
        .dot { height: 12px; width: 12px; border-radius: 50%; display: inline-block; margin-right: 5px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        th { 
    /* GRADASI MODERN (Cyan ke Biru Cerah) */
    background: linear-gradient(45deg, #182022ff, #0072ff); 
    color: white;
    
    padding: 15px;
    text-align: center;
    font-size: 14px;
    font-weight: 700; /* Lebih tebal biar kebaca */
    text-transform: uppercase;
    letter-spacing: 1px;
    
    /* Sudut membulat */
    border-radius: 15px; /* Sedikit lebih bulat biar modern */
    
    /* Efek Glow/Bayangan Biru Muda */
    box-shadow: 0 5px 15px rgba(0, 198, 255, 0.4); 
    
    /* Jarak antar kotak header */
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    background-clip: padding-box;
}
        
        td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; color: #555; }
        tr:last-child td { border-bottom: none; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <div class="main-title">
        🚢 SISTEM MONITORING MUATAN PERAHU 🚢
    </div>

    <div class="main-wrapper">
        <div class="container card-monitor">
            <div class="card-title">Status Perahu Saat Ini</div>
            
            <div class="jarak-display">
                <span id="jarak">0</span><span class="unit">cm</span>
            </div>

            <div id="status-box" class="status-card aman">
                MENUNGGU DATA...
            </div>

            <div class="device-status">
                <span>LED: <span id="led-indicator" class="dot" style="background-color: #bbb;"></span> <b id="led-text">OFF</b></span>
                <span>Buzzer: <span id="buzzer-indicator" class="dot" style="background-color: #bbb;"></span> <b id="buzzer-text">OFF</b></span>
            </div>

            <div class="info">
                Waktu Update: <span id="waktu">-</span>
            </div>
        </div>

        <div class="container card-table">
            <div class="card-title">Riwayat Log Terakhir</div>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Jarak (cm)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tabel-log">
                    </tbody>
            </table>
        </div>
    </div>

<script>
    function loadData() {
        $.ajax({
            url: 'ambil_data.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log("Data diterima:", data);
                
                $('#jarak').text(data.jarak);
                $('#waktu').text(data.waktu);
                
                let statusText = "";
                let statusClass = "";
                let jarak = parseFloat(data.jarak);
                
                if (jarak <= 5) {
                    statusText = "BAHAYA (OVERLOAD)";
                    statusClass = "bahaya";
                } else if (jarak <= 8) {
                    statusText = "NORMAL";
                    statusClass = "normal";
                } else {
                    statusText = "AMAN / KOSONG";
                    statusClass = "aman";
                }

                $('#status-box').text(statusText).attr('class', 'status-card ' + statusClass);
                
                let ledState = data.led || data.status_led;
                let buzzerState = data.buzzer || data.status_buzzer;

                if (ledState == 1) {
                    $('#led-indicator').css('background-color', '#2ecc71'); // Hijau
                    $('#led-text').text("ON");
                } else {
                    $('#led-indicator').css('background-color', '#bbb');    // Abu-abu
                    $('#led-text').text("OFF");
                }
                if (buzzerState == 1) {
                    $('#buzzer-indicator').css('background-color', '#e74c3c'); // Merah
                    $('#buzzer-text').text("ON");
                } else {
                    $('#buzzer-indicator').css('background-color', '#bbb');    // Abu-abu
                    $('#buzzer-text').text("OFF");
                }
                
                let badgeHtml = "";
                if (jarak <= 5) {
                    badgeHtml = '<span class="badge badge-bahaya">OVERLOAD</span>';
                } else if (jarak <= 8) {
                    badgeHtml = '<span class="badge badge-normal">NORMAL</span>';
                } else {
                    badgeHtml = '<span class="badge badge-aman">AMAN</span>';
                }
                
                let newRow = `<tr>
                <td style="color: #8898aa;">${data.waktu}</td>
                <td style="font-size: 16px;">${data.jarak} <small>cm</small></td>
                <td>${badgeHtml}</td>
                </tr>`;
                
                $('#tabel-log').prepend(newRow);
                if ($('#tabel-log tr').length > 10) {
                    $('#tabel-log tr:last').remove();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    setInterval(loadData, 2000);
</script>

</body>
</html>