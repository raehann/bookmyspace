<?php
session_start();
require 'koneksi.php'; // Include database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: masuk.php");
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

$user_image_url = htmlspecialchars($user['user_image']) . '?' . time();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Profil - BookMySpace</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    body {
        padding-top: 80px; /* Adjust as needed */
        padding-bottom: 80px; /* Adjust as needed */
    }
    .profile-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ddd;
        margin-bottom: 20px;
    }
    .profile-card {
        max-width: 600px;
        margin: auto;
        position: relative;
    }
    .edit-btn {
        position: absolute;
        right: 20px;
        bottom: 20px;
    }
    .profile-container {
        padding: 20px;
    }
    footer {
        height: 50px;
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
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
          </nav><!-- .navbar -->
        </div>
      </div>
    </div>
  </header><!-- End Header -->

  <main id="main" class="d-flex align-items-center justify-content-center" style="margin-top: 40px;">
    <div class="profile-card card p-4 shadow profile-container">
      <div class="text-center">
        <img src="<?php echo $user_image_url; ?>" alt="Profile Image" class="profile-image img-fluid">
      </div>
      <div class="card-body">
        <h5 class="card-title text-center"><b>Profil Pengguna</b></h5><br>
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <th scope="row">Nama</th>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                </tr>
                <tr>
                    <th scope="row">Username</th>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr>
                    <th scope="row">Bergabung</th>
                    <td>:</td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</button>
    </div>
    </div>
  </main><!-- End Main -->

  <!-- Edit Profile Modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel"><b>Edit Profil</b></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="profil-edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
            <div class="mb-3">
              <label for="name" class="form-label">Nama</label>
              <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password Baru</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
              <label for="user_image" class="form-label">Foto Profil</label>
              <input type="file" class="form-control" id="user_image" name="user_image">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div><!-- End Edit Profile Modal -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="fixed-bottom">
  <div class="container d-flex justify-content-center align-items-center h-100">
    <div class="copyright text-center">
      &copy; 2024 <strong><span>BookMySpace</span></strong>. All Rights Reserved
    </div>
  </div>
</footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>