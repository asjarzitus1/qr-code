<?php
/**
 * Aplikasi Review Funnel - Pengadilan Agama Barru
 * Berfungsi untuk memfilter ulasan:
 * - Bintang 4 & 5 diarahkan langsung ke Google Maps Review.
 * - Bintang 1, 2, & 3 dialihkan ke form pengaduan internal (disimpan ke berkas JSON/Database).
 */

// =========================================================================
// KONFIGURASI KUSTOM
// =========================================================================
// TODO: Ganti URL di bawah ini dengan link direktori ulasan Google Maps PA Barru Anda
$google_maps_url = "https://search.google.com/local/writereview?placeid=ChIJ8V9h2pRuXisR2vP2zI5jBGo"; 
$log_file = 'data_aspirasi_internal.json';
// =========================================================================

$sukses_kirim = false;

// Proses penanganan form jika rating rendah dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_kritik'])) {
    $rating_terpilih = isset($_POST['rating_input']) ? intval($_POST['rating_input']) : 0;
    $nama_pengguna  = !empty($_POST['nama']) ? htmlspecialchars(trim($_POST['nama'])) : 'Anonim';
    $kontak_pengguna = !empty($_POST['kontak']) ? htmlspecialchars(trim($_POST['kontak'])) : '-';
    $isi_kritik     = !empty($_POST['keluhan']) ? htmlspecialchars(trim($_POST['keluhan'])) : '';

    if ($rating_terpilih > 0 && $rating_terpilih <= 3) {
        $semua_data = [];
        if (file_exists($log_file)) {
            $semua_data = json_decode(file_get_contents($log_file), true);
            if (!is_array($semua_data)) { $semua_data = []; }
        }

        // Simpan ke array struktur data internal
        $data_baru = [
            'waktu'   => date('Y-m-d H:i:s'),
            'rating'  => $rating_terpilih,
            'nama'    => $nama_pengguna,
            'kontak'  => $kontak_pengguna,
            'keluhan' => $isi_kritik
        ];

        $semua_data[] = $data_baru;
        
        // Simpan ke file JSON (bisa Anda kembangkan menjadi query INSERT ke MySQL jika diperlukan)
        if (file_put_contents($log_file, json_encode($semua_data, JSON_PRETTY_PRINT))) {
            $sukses_kirim = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survei Kepuasan Layanan - Pengadilan Agama Barru</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link熏href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .survey-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 550px;
            width: 100%;
            overflow: hidden;
        }
        .header-accent {
            background: linear-gradient(135deg, #0f5132, #146c43);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .instansi-title {
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .survey-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .star-container {
            display: flex;
            justify-content: center;
            flex-direction: row-reverse;
            margin: 25px 0;
        }
        .star-container input {
            display: none;
        }
        .star-container label {
            font-size: 2.5rem;
            color: #e4e5e9;
            cursor: pointer;
            transition: color 0.2s ease-in-out;
            padding: 0 4px;
        }
        .star-container label:hover,
        .star-container label:hover ~ label,
        .star-container input:checked ~ label {
            color: #ffc107;
        }
        .dynamic-box {
            display: none;
            animation: fadeIn 0.4s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-google {
            background-color: #ffffff;
            color: #4285F4;
            border: 2px solid #4285F4;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-google:hover {
            background-color: #4285F4;
            color: white;
            box-shadow: 0 4px 15px rgba(66, 133, 244, 0.3);
        }
        .btn-success-custom {
            background-color: #146c43;
            border: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
        }
        .btn-success-custom:hover {
            background-color: #0f5132;
        }
    </style>
</head>
<body>

<div class="survey-card">
    <!-- Header Instansi -->
    <div class="header-accent">
        <div class="instansi-title">Pengadilan Agama Barru</div>
        <div class="survey-subtitle">Formulir Digital Penilaian Kepuasan Pelayanan Masyarakat</div>
    </div>

    <div class="card-body p-4">
        
        <?php if ($sukses_kirim): ?>
            <!-- Alert Sukses Mengirim Pengaduan Internal -->
            <div class="text-center py-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 font-weight-bold">Terima Kasih!</h4>
                <p class="text-muted">Kritik dan saran Anda telah kami terima secara internal untuk menjadi bahan evaluasi peningkatan layanan kami ke depan.</p>
                <a href="" class="btn btn-secondary btn-sm mt-2">Kembali</a>
            </div>
        <?php else: ?>
            
            <div id="main_survey_section">
                <h5 class="text-center mb-2 font-weight-bold">Bagaimana pelayanan kami hari ini?</h5>
                <p class="text-center text-muted small">Ketuk pada jumlah bintang untuk memberikan penilaian Anda</p>
                
                <!-- Input Interaktif Rating Bintang -->
                <div class="star-container">
                    <input type="radio" name="rating" id="star5" value="5"><label for="star5" class="bi bi-star-fill" onclick="handleStarClick(5)"></label>
                    <input type="radio" name="rating" id="star4" value="4"><label for="star4" class="bi bi-star-fill" onclick="handleStarClick(4)"></label>
                    <input type="radio" name="rating" id="star3" value="3"><label for="star3" class="bi bi-star-fill" onclick="handleStarClick(3)"></label>
                    <input type="radio" name="rating" id="star2" value="2"><label for="star2" class="bi bi-star-fill" onclick="handleStarClick(2)"></label>
                    <input type="radio" name="rating" id="star1" value="1"><label for="star1" class="bi bi-star-fill" onclick="handleStarClick(1)"></label>
                </div>
                
                <!-- BOX JIKA PILIHAN BINTANG 4 ATAU 5 (FUNNEL KE GOOGLE MAPS) -->
                <div id="box_positif" class="dynamic-box text-center py-3">
                    <div class="alert alert-success d-inline-block px-4 py-2 small mb-3">
                        <i class="bi bi-heart-fill me-2 text-danger"></i> Kami sangat senang Anda puas dengan layanan kami!
                    </div>
                    <p class="text-muted mb-4 px-2">Bantu kami menjangkau masyarakat Barru lebih luas dengan membagikan pengalaman positif Anda ke Google Maps resmi kami.</p>
                    <a href="<?php echo $google_maps_url; ?>" target="_blank" class="btn btn-google w-100" onclick="resetFormAfterRedirect()">
                        <i class="bi bi-google me-2"></i> Tulis Ulasan Bintang 5 di Google Maps
                    </a>
                </div>

                <!-- BOX JIKA PILIHAN BINTANG 1, 2, ATAU 3 (DIALIKKAN KE INTERNAL ASPIRASI) -->
                <div id="box_negatif" class="dynamic-box">
                    <div class="alert alert-warning small mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Mohon Maaf.</strong> Kami berkomitmen untuk selalu berbenah. Bantu kami mengetahui hal apa yang perlu diperbaiki.
                    </div>
                    
                    <!-- Form Pengaduan Masuk Ke PHP Server Internal -->
                    <form action="" method="POST" class="mt-3">
                        <input type="hidden" name="rating_input" id="rating_input_holder" value="">
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label small fw-semibold">Nama Lengkap (Opsional)</label>
                            <input type="text" class="form-control form-control-sm" id="nama" name="nama" placeholder="Masukkan nama Anda">
                        </div>
                        
                        <div class="mb-3">
                            <label for="kontak" class="form-label small fw-semibold">Nomor WA / HP (Opsional)</label>
                            <input type="text" class="form-control form-control-sm" id="kontak" name="kontak" placeholder="Contoh: 08123xxxx">
                        </div>

                        <div class="mb-3">
                            <label for="keluhan" class="form-label small fw-semibold">Uraian Kritik, Saran, atau Keluhan <span class="text-danger">*</span></label>
                            <textarea class="form-control small" id="keluhan" name="keluhan" rows="4" placeholder="Tuliskan kendala atau pelayanan loket mana yang dirasa kurang memuaskan..." required></textarea>
                        </div>

                        <button type="submit" name="kirim_kritik" class="btn btn-success-custom text-white w-100">
                            <i class="bi bi-send-fill me-2"></i> Kirim Masukan Internal
                        </button>
                    </form>
                </div>
            </div>

        <?php endif; ?>

    </div>
    
    <!-- Footer / Watermark Kebijakan -->
    <div class="bg-light text-center py-3 border-top text-muted" style="font-size: 0.75rem;">
        &copy; <?php echo date('Y'); ?> Pengadilan Agama Barru - Wilayah Bebas dari Korupsi.
    </div>
</div>

<!-- Bootstrap 5 JS Bundle (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function handleStarClick(rating) {
        // Simpan nilai rating terpilih ke elemen penampung input hidden form
        document.getElementById('rating_input_holder').value = rating;

        const boxPositif = document.getElementById('box_positif');
        const boxNegatif = document.getElementById('box_negatif');

        if (rating >= 4) {
            // Sembunyikan form komplain internal, tampilkan ajakan ke Google Maps
            boxNegatif.style.display = 'none';
            boxPositif.style.display = 'block';
        } else {
            // Sembunyikan ajakan Google Maps, tampilkan form komplain internal
            boxPositif.style.display = 'none';
            boxNegatif.style.display = 'block';
        }
    }

    function resetFormAfterRedirect() {
        // Opsional: Mereset halaman kembali setelah pengguna diarahkan membuka Google Maps di tab baru
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    }
</script>

</body>
</html>
