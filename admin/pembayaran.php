<?php
session_start();
require '../koneksi.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$query = "SELECT name, username, user_image FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Query to fetch booking data with status 'diterima'
$query = "
    SELECT 
        bookings.booking_id,
        bookings.invoice,
        users.name AS customer_name,
        rooms.room_name AS room_name,
        DATE_FORMAT(bookings.start_time, '%d %b %Y %H:%i') AS start_time,
        DATE_FORMAT(bookings.end_time, '%d %b %Y %H:%i') AS end_time,
        bookings.total_price,
        bookings.status,
        bookings.payment_status
    FROM 
        bookings
    JOIN 
        users ON bookings.user_id = users.user_id
    JOIN 
        rooms ON bookings.room_id = rooms.room_id
    WHERE 
        bookings.status = 'diterima' OR bookings.payment_status = 'sudah_dibayar'
";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pembayaran - BookMySpace</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    .status-belum_dibayar {
        color: red;
    }
    .status-sudah_dibayar {
        color: green;
    }
    .center-text {
        text-align: center;
    }
    .table-responsive {
        overflow-x: auto;
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">BookMySpace</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $admin['name']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <span><?php echo $admin['username']; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="keluar.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="pemesanan.php">
          <i class="bi bi-list-check"></i>
          <span>Pemesanan</span>
        </a>
      </li><!-- End Pemesanan Nav -->
      <li class="nav-item">
        <a class="nav-link" href="pembayaran.php">
          <i class="bi bi-credit-card"></i>
          <span>Pembayaran</span>
        </a>
      </li><!-- End Pembayaran Nav -->
      <li class="nav-item">
      <a class="nav-link collapsed" href="rooms.php">
        <i class="bi bi-door-open"></i>
        <span>Ruangan</span>
      </a>
    </li><!-- End Ruangan Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php">
          <i class="bi bi-people"></i>
          <span>Pengguna</span>
        </a>
      </li><!-- End Pengguna Nav -->
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Pembayaran</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Pembayaran</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Daftar Pembayaran</h5>
              <div class="table-responsive">
              <table id="pembayaranTable" class="table table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>No Invoice</th>
                    <th class="center-text">Nama Pelanggan</th>
                    <th class="center-text">Tipe Ruangan</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while ($row = $result->fetch_assoc()) {
                    $statusClass = $row['payment_status'] == 'belum_dibayar' ? 'status-belum_dibayar' : 'status-sudah_dibayar';
                    $statusText = str_replace('_', ' ', $row['payment_status']);
                    
                    echo "<tr>";
                    echo "<td>{$no}</td>";
                    echo "<td>{$row['invoice']}</td>";
                    echo "<td class='center-text'>{$row['customer_name']}</td>";
                    echo "<td class='center-text'>{$row['room_name']}</td>";
                    echo "<td>{$row['start_time']}</td>";
                    echo "<td>{$row['end_time']}</td>";
                    echo "<td class='{$statusClass}' id='status-{$row['booking_id']}'>{$statusText}</td>";
                    echo "<td id='actions-{$row['booking_id']}'>";
                    if ($row['payment_status'] == 'belum_dibayar') {
                      echo "<button class='btn btn-success btn-sm' onclick='showPaymentModal({$row['booking_id']})'>Bayar</button>";
                    } else {
                      echo "<i class='bi bi-check-circle text-success'></i>";
                    }
                    echo "</td>";
                    echo "</tr>";
                    $no++;
                  }
                  ?>
                </tbody>
              </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright 2024 <strong><span>BookMySpace</span></strong>. All Rights Reserved
    </div>
  </footer><!-- End Footer -->

  <!-- Payment Modal -->
  <div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Rincian Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table class="table table-borderless">
            <tbody>
              <tr>
                <th>Invoice:</th>
                <td id="modalInvoice"></td>
              </tr>
              <tr>
                <th>Nama Pelanggan:</th>
                <td id="modalCustomerName"></td>
              </tr>
              <tr>
                <th>Tipe Ruangan:</th>
                <td id="modalRoomName"></td>
              </tr>
              <tr>
                <th>Waktu Mulai:</th>
                <td id="modalStartTime"></td>
              </tr>
              <tr>
                <th>Waktu Selesai:</th>
                <td id="modalEndTime"></td>
              </tr>
              <tr>
                <th>Total Harga:</th>
                <td id="modalTotalPrice"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="confirmPaymentButton">Konfirmasi Pembayaran</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Custom Script -->
  <script>
    // Initialize DataTables
    document.addEventListener('DOMContentLoaded', () => {
      const dataTable = new simpleDatatables.DataTable('#pembayaranTable');
    });
    // Show Payment Modal with details
    function showPaymentModal(bookingId) {
      fetch(`get-booking-details.php?booking_id=${bookingId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('modalInvoice').textContent = data.invoice;
          document.getElementById('modalCustomerName').textContent = data.customer_name;
          document.getElementById('modalRoomName').textContent = data.room_name;
          document.getElementById('modalStartTime').textContent = data.start_time;
          document.getElementById('modalEndTime').textContent = data.end_time;
          document.getElementById('modalTotalPrice').textContent = data.total_price;
          document.getElementById('confirmPaymentButton').onclick = function() {
            confirmPayment(bookingId);
          };
          const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
          paymentModal.show();
        })
        .catch(error => console.error('Error fetching booking details:', error));
    }

    // Confirm Payment
    function confirmPayment(bookingId) {
      fetch('konfirmasi-pembayaran.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById(`status-${bookingId}`).textContent = 'sudah dibayar';
          document.getElementById(`status-${bookingId}`).classList.remove('status-belum_dibayar');
          document.getElementById(`status-${bookingId}`).classList.add('status-sudah_dibayar');
          document.getElementById(`actions-${bookingId}`).innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
          const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
          paymentModal.hide();
        } else {
          alert('Failed to confirm payment.');
        }
      })
      .catch(error => console.error('Error confirming payment:', error));
    }
  </script>
</body>

</html>