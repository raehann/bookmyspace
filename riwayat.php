<?php
session_start();
require 'koneksi.php'; // Include database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: masuk.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch booking history
$query = "SELECT b.booking_id, b.created_at, b.invoice, b.status, b.total_price, b.payment_status,b.payment_date, r.room_name, r.image, b.start_time, b.end_time 
          FROM bookings b
          JOIN rooms r ON b.room_id = r.room_id
          WHERE b.user_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $stmt->error);
}

$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Riwayat - BookMySpace</title>
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
        .small-footer {
            padding: 10px 0;
            font-size: 14px;
            bottom: 0;
            width: 100%;
        }

        #main {
            padding-top: 120px; /* Adjust this value to add more space below the navbar */
            padding-bottom: 60px; /* Adjust this value to add more space above the footer */
        }

        .gray-line {
            border-top: 1px solid #ccc;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .invoice-button {
            float: right;
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
                            <li><a class="nav-link scrollto" href="index.php#workspace">Work Space</a></li>
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

    <main id="main" class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><strong>Riwayat Pemesanan</strong></h5>
                        <div class="row mb-3" style="margin-top: 20px;">
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="search" placeholder="Cari Riwayat Pemesanan">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="category">
                                    <option value="">Semua Kategori</option>
                                    <option value="menunggu">Menunggu</option>
                                    <option value="diterima">Diterima</option>
                                    <option value="ditolak">Ditolak</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                        <div id="booking-container">
                            <?php if (count($bookings) > 0): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <div class="booking-item card mb-3" data-room-name="<?php echo strtolower(htmlspecialchars($booking['room_name'])); ?>" data-status="<?php echo $booking['status']; ?>">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted"><?php echo date('d M Y', strtotime($booking['created_at'])); ?></h6>
                                                    <span class="badge bg-<?php echo $booking['status'] === 'menunggu' ? 'warning' : ($booking['status'] === 'diterima' || $booking['status'] === 'selesai' ? 'success' : 'danger'); ?>">
                                                        <?php echo ucfirst($booking['status']); ?>
                                                    </span>
                                                        <div class="gray-line"></div>
                                                </div>
                                                <div class="text-muted">
                                                   No Invoice: <?php echo $booking['invoice']; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex mt-3">
                                                <img src="<?php echo $booking['image']; ?>" alt="Room Image" class="img-thumbnail me-3" style="width: 100px; height: 100px;">
                                                <div>
                                                    <h5><?php echo htmlspecialchars($booking['room_name']); ?></h5>
                                                    <p class="text-muted"><?php echo date('d M Y H:i', strtotime($booking['start_time'])); ?> - <?php echo date('d M Y H:i', strtotime($booking['end_time'])); ?></p>
                                                </div>
                                                <div class="ms-auto">
                                                    <h5>Total Harga: Rp<?php echo number_format($booking['total_price'], 0, ',', '.'); ?></h5>
                                                    <button class="btn btn-primary invoice-button" onclick="downloadInvoice(<?php echo $booking['booking_id']; ?>)">Invoice</button>
                                                    <?php if ($booking['status'] === 'menunggu'): ?>
                                                        <button style="margin-left: 70px;" class="btn btn-danger" onclick="cancelBooking(<?php echo $booking['booking_id']; ?>)">Batal</button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if ($booking['status'] !== 'dibatalkan' && $booking['payment_status'] !== null && $booking['status'] !== 'ditolak'): ?>
                                            <div class="text-end">
                                                <span class="badge bg-<?php echo $booking['payment_status'] === 'belum_dibayar' ? 'warning' : 'success'; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $booking['payment_status'])); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center">Belum ada riwayat pemesanan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer id="" class="small-footer">
        <div class="container justify-content-center align-items-center">
            <div class="copyright text-center">
                &copy; Copyright 2024 <strong>BookMySpace</strong>. All Rights Reserved
            </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="assets/vendor/purecounter/purecounter.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // JavaScript for automatic search
        document.getElementById('search').addEventListener('input', function() {
            filterBookings();
        });

        document.getElementById('category').addEventListener('change', function() {
            filterBookings();
        });

        function filterBookings() {
            const searchInput = document.getElementById('search').value.toLowerCase();
            const category = document.getElementById('category').value;
            const bookingItems = document.querySelectorAll('.booking-item');

            bookingItems.forEach(item => {
                const roomName = item.getAttribute('data-room-name');
                const status = item.getAttribute('data-status');

                const matchesSearch = roomName.includes(searchInput);
                const matchesCategory = category === '' || status === category;

                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function downloadInvoice(bookingId) {
            window.location.href = `invoice.php?booking_id=${bookingId}`;
        }

        function cancelBooking(bookingId) {
            if (confirm("Apakah Anda yakin ingin membatalkan pemesanan ini?")) {
                window.location.href = `cancel-booking.php?booking_id=${bookingId}`;
            }
        }
    </script>
</body>
</html>
