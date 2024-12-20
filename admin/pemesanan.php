<?php
session_start();
require '../koneksi.php'; // Include database connection file

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

// Query to fetch booking data
$query = "
    SELECT 
        bookings.booking_id,
        bookings.invoice,
        users.name AS customer_name,
        rooms.room_name AS room_name,
        bookings.start_time,
        bookings.end_time,
        bookings.duration_type,
        bookings.status
    FROM 
        bookings
    JOIN 
        users ON bookings.user_id = users.user_id
    JOIN 
        rooms ON bookings.room_id = rooms.room_id
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

  <title>Pemesanan - BookMySpace</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    .status-diterima {
        color: green;
    }
    .status-ditolak {
        color: red;
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
        <a class="nav-link" href="pemesanan.php">
          <i class="bi bi-list-check"></i>
          <span>Pemesanan</span>
        </a>
      </li><!-- End Pemesanan Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="pembayaran.php">
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
      <h1>Pemesanan</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Pemesanan</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Daftar Pemesanan</h5>
              <div class="table-responsive">
                <table id="pemesananTable" class="table table-striped">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>No Invoice</th>
                      <th class="center-text">Nama Pelanggan</th>
                      <th class="center-text">Tipe Ruangan</th>
                      <th>Waktu Mulai</th>
                      <th>Waktu Selesai</th>
                      <th class="center-text">Tipe Durasi</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                      $statusClass = '';
                      if ($row['status'] == 'diterima') {
                        $statusClass = 'status-diterima';
                      } elseif ($row['status'] == 'ditolak') {
                        $statusClass = 'status-ditolak';
                      }

                      echo "<tr>";
                      echo "<td>{$no}</td>";
                      echo "<td>{$row['invoice']}</td>";
                      echo "<td class='center-text'>{$row['customer_name']}</td>";
                      echo "<td class='center-text'>{$row['room_name']}</td>";
                      echo "<td>{$row['start_time']}</td>";
                      echo "<td>{$row['end_time']}</td>";
                      echo "<td class='center-text'>{$row['duration_type']}</td>";
                      echo "<td class='{$statusClass}' id='status-{$row['booking_id']}'>{$row['status']}</td>";
                      echo "<td id='actions-{$row['booking_id']}'>";
                      if ($row['status'] == 'menunggu') {
                        echo "<button class='btn btn-success btn-sm' onclick='updateStatus({$row['booking_id']}, \"diterima\")'><i class='bi bi-check-circle'></i></button>";
                        echo "<button class='btn btn-warning btn-sm' onclick='updateStatus({$row['booking_id']}, \"ditolak\")'><i class='bi bi-x-circle'></i></button>";
                      }
                      echo "<button class='btn btn-danger btn-sm' onclick='deleteBooking({$row['booking_id']})'><i class='bi bi-trash'></i></button>";
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
      &copy; Copyright 4 <strong><span>BookMySpace</span></strong>. All Rights Reserved
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- DataTables -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const pemesananTable = new simpleDatatables.DataTable("#pemesananTable");
    });

    function updateStatus(bookingId, status) {
        if (confirm(`Apakah Anda yakin ingin ${status} pemesanan ini?`)) {
            fetch(`update-booking-status.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ booking_id: bookingId, status: status }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusCell = document.getElementById(`status-${bookingId}`);
                    const actionsCell = document.getElementById(`actions-${bookingId}`);

                    statusCell.className = status === 'diterima' ? 'status-diterima' : 'status-ditolak';
                    statusCell.textContent = status;

                    actionsCell.innerHTML = `
                        <button class='btn btn-danger btn-sm' onclick='deleteBooking(${bookingId})'><i class='bi bi-trash'></i></button>
                    `;
                } else {
                    alert('Gagal memperbarui status pemesanan');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function deleteBooking(bookingId) {
        if (confirm('Apakah Anda yakin ingin menghapus pemesanan ini?')) {
            fetch(`delete-booking.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ booking_id: bookingId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus pemesanan');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
  </script>

</body>
</html>
