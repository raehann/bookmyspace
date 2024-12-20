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

// Query to fetch rooms data
$query = "SELECT * FROM rooms";
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

  <title>Manajemen Ruangan - BookMySpace</title>
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
    .center-text {
        text-align: center;
    }
    .description-text {
        text-align: justify;
        white-space: pre-wrap;
    }
    .profile-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .table-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    .modal-img {
        width: 200px;
        height: 200px;
        object-fit: cover;
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
            <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle profile-img">
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
        <a class="nav-link collapsed" href="pembayaran.php">
          <i class="bi bi-credit-card"></i>
          <span>Pembayaran</span>
        </a>
      </li><!-- End Pembayaran Nav -->
      <li class="nav-item">
      <a class="nav-link" href="rooms.php">
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
      <h1>Manajemen Ruangan</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Ruangan</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Daftar Ruangan</h5>
              <div class="table-responsive">
              <table id="roomsTable" class="table table-striped">
                <thead>
                  <tr>
                    <th class="center-text">No</th>
                    <th class="center-text">Nama Ruangan</th>
                    <th class="center-text">Kategori</th>
                    <th class="center-text">Deskripsi</th>
                    <th class="center-text">Fasilitas</th>
                    <th class="center-text">Harga Perjam</th>
                    <th class="center-text">Harga Perhari</th>
                    <th class="center-text">Harga Perbulan</th>
                    <th class="center-text">Stock</th>
                    <th class="center-text">Gambar</th>
                    <th class="center-text">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while ($row = $result->fetch_assoc()) {
                    $facilitiesList = '<ul>';
                    $facilitiesArray = explode(',', $row['facilities']);
                    foreach ($facilitiesArray as $facility) {
                      $facilitiesList .= "<li>{$facility}</li>";
                    }
                    $facilitiesList .= '</ul>';
                    $formattedBasePriceHour = 'Rp ' . number_format($row['base_price_hour'], 0, ',', '.');
                    $formattedBasePriceDay = 'Rp ' . number_format($row['base_price_day'], 0, ',', '.');
                    $formattedBasePriceMonth = 'Rp ' . number_format($row['base_price_month'], 0, ',', '.');

                    echo "<tr>";
                    echo "<td class='center-text'>{$no}</td>";
                    echo "<td class='center-text'>{$row['room_name']}</td>";
                    echo "<td class='center-text'>{$row['category']}</td>";
                    echo "<td class='description-text'>{$row['description']}</td>";
                    echo "<td>{$facilitiesList}</td>";
                    echo "<td class='center-text'>{$formattedBasePriceHour}</td>";
                    echo "<td class='center-text'>{$formattedBasePriceDay}</td>";
                    echo "<td class='center-text'>{$formattedBasePriceMonth}</td>";
                    echo "<td class='center-text'>{$row['stock']}</td>";
                    echo "<td class='center-text'><img src='../{$row['image']}' alt='Image' class='table-img'></td>";
                    echo "<td class='center-text'><button class='btn btn-warning btn-sm' onclick='showEditModal({$row['room_id']})'>Edit</button></td>";
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

  <!-- Edit Room Modal -->
  <div class="modal fade" id="editRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Ruangan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editRoomForm">
            <div class="row mb-3">
              <label for="modalRoomName" class="col-md-4 col-lg-3 col-form-label">Nama Ruangan</label>
              <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" id="modalRoomName" disabled>
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalCategory" class="col-md-4 col-lg-3 col-form-label">Kategori</label>
              <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" id="modalCategory" disabled>
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalDescription" class="col-md-4 col-lg-3 col-form-label">Deskripsi</label>
              <div class="col-md-8 col-lg-9">
                <textarea class="form-control" id="modalDescription"></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalFacilities" class="col-md-4 col-lg-3 col-form-label">Fasilitas</label>
              <div class="col-md-8 col-lg-9">
                <textarea class="form-control" id="modalFacilities"></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalBasePriceHour" class="col-md-4 col-lg-3 col-form-label">Harga Perjam</label>
              <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" id="modalBasePriceHour">
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalBasePriceDay" class="col-md-4 col-lg-3 col-form-label">Harga Perhari</label>
              <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" id="modalBasePriceDay">
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalBasePriceMonth" class="col-md-4 col-lg-3 col-form-label">Harga Perbulan</label>
              <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" id="modalBasePriceMonth">
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalStock" class="col-md-4 col-lg-3 col-form-label">Stock</label>
              <div class="col-md-8 col-lg-9">
                <input type="text" class="form-control" id="modalStock">
              </div>
            </div>
            <div class="row mb-3">
              <label for="modalImage" class="col-md-4 col-lg-3 col-form-label">Gambar</label>
              <div class="col-md-8 col-lg-9">
                <img src="" alt="Image" class="modal-img" id="modalImagePreview">
                <input type="file" class="form-control" id="modalImage">
              </div>
            </div>
            <input type="hidden" id="modalRoomId">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveChangesButton">Simpan Perubahan</button>
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
      const dataTable = new simpleDatatables.DataTable('#roomsTable');
    });

    // Show Edit Modal with room details
    function showEditModal(roomId) {
      fetch(`get-room-details.php?room_id=${roomId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('modalRoomName').value = data.room_name;
          document.getElementById('modalCategory').value = data.category;
          document.getElementById('modalDescription').value = data.description;
          document.getElementById('modalFacilities').value = data.facilities;
          document.getElementById('modalBasePriceHour').value = data.base_price_hour;
          document.getElementById('modalBasePriceDay').value = data.base_price_day;
          document.getElementById('modalBasePriceMonth').value = data.base_price_month;
          document.getElementById('modalStock').value = data.stock;
          document.getElementById('modalImagePreview').src = `../${data.image}`;
          document.getElementById('modalRoomId').value = data.room_id;
          const editRoomModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
          editRoomModal.show();
        })
        .catch(error => console.error('Error fetching room details:', error));
    }

    // Save Changes
    document.getElementById('saveChangesButton').onclick = function() {
      const roomId = document.getElementById('modalRoomId').value;
      const description = document.getElementById('modalDescription').value;
      const facilities = document.getElementById('modalFacilities').value;
      const basePriceHour = document.getElementById('modalBasePriceHour').value;
      const basePriceDay = document.getElementById('modalBasePriceDay').value;
      const basePriceMonth = document.getElementById('modalBasePriceMonth').value;
      const stock = document.getElementById('modalStock').value;
      const imageInput = document.getElementById('modalImage');
      let image = '';
      if (imageInput.files.length > 0) {
        image = imageInput.files[0];
      }
      
      const formData = new FormData();
      formData.append('room_id', roomId);
      formData.append('description', description);
      formData.append('facilities', facilities);
      formData.append('base_price_hour', basePriceHour);
      formData.append('base_price_day', basePriceDay);
      formData.append('base_price_month', basePriceMonth);
      formData.append('stock', stock);
      if (image) {
        formData.append('image', image);
      }

      fetch('update-room-details.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Room details updated successfully.');
          location.reload();
        } else {
          alert('Failed to update room details.');
        }
      })
      .catch(error => console.error('Error updating room details:', error));
    };
  </script>
</body>

</html>
