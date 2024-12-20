<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu Anda
require('koneksi.php');

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: masuk.php');
    exit();
}

// Get room ID from URL
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

// Query to get room details
$room_query = "SELECT * FROM rooms WHERE room_id = $room_id";
$room_result = $conn->query($room_query);
$room = $room_result->fetch_assoc();

if (!$room) {
    echo "<script>alert('Ruangan tidak ditemukan.'); location='index.php#workspace'; </script>";
    exit();
}

// Query to get room images
$image_query = "SELECT * FROM room_images WHERE room_id = $room_id";
$image_result = $conn->query($image_query);
$images = $image_result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $start_time = $_POST['start_time'];
    $duration_type = $_POST['duration_type'];
    $duration = intval($_POST['duration']);
    
    // Make sure $start_time is in the correct format
    $start_time_dt = new DateTime($start_time, new DateTimeZone('Asia/Jakarta'));
    
    // Calculate end_time based on duration
    $end_time_dt = clone $start_time_dt;
    switch ($duration_type) {
        case 'jam':
            $end_time_dt->modify("+$duration hours");
            break;
        case 'hari':
            $end_time_dt->modify("+$duration days");
            break;
        case 'bulan':
            $end_time_dt->modify("+$duration months");
            break;
    }
    
    // Convert end_time to string format for database
    $end_time = $end_time_dt->format('Y-m-d H:i:s');
    
    // Generate custom invoice ID
    $date = date('Ymd');
    $category = strtoupper($room['category']);
    $random_number = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
    $invoice = "INV/$date/$category/$random_number";

    // Calculate total price
    $base_price = 0;
    switch ($duration_type) {
        case 'jam':
            $base_price = $room['base_price_hour'];
            break;
        case 'hari':
            $base_price = $room['base_price_day'];
            break;
        case 'bulan':
            $base_price = $room['base_price_month'];
            break;
    }
    $total_price = $base_price * $duration;

    // Start transaction
    $conn->begin_transaction();

    // Insert booking into the database
    $insert_query = "INSERT INTO bookings (user_id, room_id, start_time, end_time, duration_type, total_price, status, payment_status, created_at, invoice)
                     VALUES ($user_id, $room_id, '$start_time', '$end_time', '$duration_type', $total_price, 'menunggu', 'belum_dibayar', NOW(), '$invoice')";

    if ($conn->query($insert_query) === TRUE) {
        // Update room stock
        $update_stock_query = "UPDATE rooms SET stock = stock - 1 WHERE room_id = $room_id AND stock > 0";
        if ($conn->query($update_stock_query) === TRUE) {
            // Commit transaction
            $conn->commit();
            echo "<script>alert('Pemesanan berhasil.'); location='riwayat.php?user_id=$user_id'; </script>";
        } else {
            // Rollback transaction
            $conn->rollback();
            echo "<script>alert('Pemesanan gagal. Silakan coba lagi.'); location='checkout.php?room_id=$room_id'; </script>";
        }
    } else {
        // Rollback transaction
        $conn->rollback();
        echo "<script>alert('Pemesanan gagal. Silakan coba lagi.'); location='checkout.php?room_id=$room_id'; </script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Checkout - BookMySpace</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700" rel="stylesheet">
    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        #main {
            padding-top: 100px;
        }
        .main-image {
            object-fit: cover;
            width: 100%;
            height: 300px;
        }
        .additional-image {
            object-fit: cover;
            width: 100%;
            height: 150px;
        }
    </style>
</head>
<body>
    <header id="header" class="fixed-top d-flex align-items-center">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col-xl-11 d-flex align-items-center justify-content-between">
                    <h1 class="logo"><a href="index.php">BookMySpace</a></h1>
                    <nav id="navbar" class="navbar">
                        <ul>
                            <li><a class="nav-link scrollto" href="index.php#hero">Home</a></li>
                            <li><a class="nav-link scrollto active" href="index.php#workspace">Work Space</a></li>
                            <li><a class="nav-link scrollto" href="index.php#layanan">Layanan</a></li>
                            <li><a class="nav-link scrollto" href="index.php#testimoni">Testimoni</a></li>
                            <?php if (!isset($_SESSION['user_id'])) : ?>
                                <li><a class="nav-link" href="daftar.php">Daftar</a></li>
                                <li><a class="nav-link" href="masuk.php">Masuk</a></li>
                            <?php else : ?>
                                <li class="dropdown"><a href="#"><span>Akun</span> <i class="bi bi-chevron-down"></i></a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <li><a class="dropdown-item" href="profil.php">Profil</a></li>
                                        <li><a class="dropdown-item" href="riwayat.php">Riwayat</a></li>
                                        <li><a class="dropdown-item" href="keluar.php">Keluar</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <i class="bi bi-list mobile-nav-toggle"></i>
                    </nav>
                </div>
            </div>
        </div>
    </header>
<main id="main" class="container my-5">
    <div class="row">
        <div class="col-lg-3">
            <img src="<?php echo $room['image']; ?>" alt="Room Image" class="main-image mb-3">
            <h4><?php echo ucfirst($room['room_name']); ?></h4>
            <p><strong>Fasilitas:</strong></p>
            <p style="text-align: justify; margin-top: -30px;"><?php echo nl2br($room['facilities']); ?></p>
        </div>
        <div class="col-lg-9">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="room_name" class="form-label">Nama Ruangan</label>
                    <input type="text" class="form-control" id="room_name" value="<?php echo $room['room_name']; ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="duration_type" class="form-label">Tipe Peminjaman</label>
                    <select class="form-select" id="duration_type" name="duration_type" required>
                        <option value="" selected disabled>Pilih Tipe Peminjaman</option>
                        <option value="jam">Jam</option>
                        <option value="hari">Hari</option>
                        <option value="bulan">Bulan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">Durasi Peminjaman</label>
                    <select class="form-select" id="duration" name="duration" required>
                        <option value="" selected disabled>Pilih Tipe Peminjaman Terlebih Dahulu</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="start_time" class="form-label">Mulai Peminjaman</label>
                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                </div>
                <div class="mb-3">
                    <label for="end_time" class="form-label">Selesai Peminjaman</label>
                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" disabled>
                </div>
                <div class="mb-3">
                    <label for="total_price" class="form-label">Total Harga</label>
                    <input type="text" class="form-control" id="total_price" name="total_price" value="Rp0,00" disabled>
                </div>
                <button type="submit" class="btn btn-primary">Pesan</button>
                <button type="reset" class="btn btn-secondary" id="reset_button">Reset</button>
            </form>
        </div>
    </div>
</main>

<footer id="footer">
    <div class="container">
        <div class="copyright">
            &copy; Copyright 2024 <strong>BookMySpace</strong>. All Rights Reserved
        </div>
    </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<div id="preloader"></div>
<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const durationTypeSelect = document.getElementById('duration_type');
    const durationSelect = document.getElementById('duration');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const totalPriceInput = document.getElementById('total_price');
    const resetButton = document.getElementById('reset_button');

    function updateDurationOptions() {
        const durationType = durationTypeSelect.value;
        let maxDuration;
        switch (durationType) {
            case 'jam':
                maxDuration = 24;
                break;
            case 'hari':
                maxDuration = 30;
                break;
            case 'bulan':
                maxDuration = 12;
                break;
        }
        durationSelect.innerHTML = '<option value="" disabled selected>Pilih Durasi Peminjaman</option>';
        for (let i = 1; i <= maxDuration; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.text = i;
            durationSelect.appendChild(option);
        }
    }

    function updateEndTime() {
        const startTime = new Date(startTimeInput.value);
        const durationType = durationTypeSelect.value;
        const duration = parseInt(durationSelect.value);
        let endTime = new Date(startTime);

        switch (durationType) {
            case 'jam':
                endTime.setHours(endTime.getHours() + duration);
                break;
            case 'hari':
                endTime.setDate(endTime.getDate() + duration);
                break;
            case 'bulan':
                endTime.setMonth(endTime.getMonth() + duration);
                break;
        }

        // Convert the endTime to the local time string in the format YYYY-MM-DDTHH:MM
        const localISOTime = new Date(endTime.getTime() - (endTime.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
        endTimeInput.value = localISOTime;
    }

    function updateTotalPrice() {
        const durationType = durationTypeSelect.value;
        const duration = parseInt(durationSelect.value);
        let basePrice = 0;
        switch (durationType) {
            case 'jam':
                basePrice = <?php echo $room['base_price_hour']; ?>;
                break;
            case 'hari':
                basePrice = <?php echo $room['base_price_day']; ?>;
                break;
            case 'bulan':
                basePrice = <?php echo $room['base_price_month']; ?>;
                break;
        }
        const totalPrice = basePrice * duration;
        totalPriceInput.value = isNaN(totalPrice) ? 'Rp0,00' : totalPrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
    }

    durationTypeSelect.addEventListener('change', () => {
        updateDurationOptions();
        updateEndTime();
        updateTotalPrice();
    });

    durationSelect.addEventListener('change', () => {
        updateEndTime();
        updateTotalPrice();
    });

    startTimeInput.addEventListener('input', () => {
        updateEndTime();
        updateTotalPrice();
    });

    resetButton.addEventListener('click', () => {
        durationTypeSelect.value = "";
        durationSelect.innerHTML = '<option value="">Pilih Durasi Peminjaman Terlebih Dahulu</option>';
        startTimeInput.value = "";
        endTimeInput.value = "";
        totalPriceInput.value = "Rp0,00";
    });

    // Initialize duration options and total price on page load
    updateDurationOptions();
    updateTotalPrice();
});
</script>
</body>
</html>
