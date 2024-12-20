<?php
session_start();
require('koneksi.php');

// Get room ID from URL
$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;

// Query to get room details
$room_query = "SELECT * FROM rooms WHERE room_id = $room_id";
$room_result = $conn->query($room_query);
$room = $room_result->fetch_assoc();

// Query to get room images
$image_query = "SELECT * FROM room_images WHERE room_id = $room_id";
$image_result = $conn->query($image_query);
$images = $image_result->fetch_all(MYSQLI_ASSOC);

// Determine total stock based on room type
$room_type = $room['category']; // Assuming you have a column `category` in your `rooms` table
switch ($room_type) {
    case 'reguler':
        $total_stock = 50;
        break;
    case 'vip':
        $total_stock = 25;
        break;
    case 'vvip':
        $total_stock = 10;
        break;
    default:
        $total_stock = 0; // Or handle the default case as needed
        break;
}

$available_stock = $room['stock']; // Assuming you have a column `stock` in your `rooms` table
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Detail Ruangan - BookMySpace</title>
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
    #main {
      padding-top: 100px; /* Adjust this value to add space below the navbar */
    }

    .main-image {
      object-fit: cover;
      width: 100%;
      height: 300px; /* Adjust height as needed */
    }

    .additional-image {
      object-fit: cover;
      width: 100%;
      height: 150px; /* Adjust height as needed */
    }

    @media (max-width: 768px) {
      .main-image {
        height: 200px; /* Reduce height for smaller screens */
      }

      .additional-image {
        height: 100px; /* Reduce height for smaller screens */
      }

      .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start;
      }

      .d-flex.justify-content-between p {
        margin-bottom: 10px;
      }
    }

    .stock-info {
      font-weight: bold;
      margin-right: 15px;
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

  <main id="main" class="container my-5">
    <div class="row">
      <!-- Main Image -->
      <div class="col-12 mb-3">
        <img src="<?php echo $room['image']; ?>" class="img-fluid main-image" alt="<?php echo $room['room_name']; ?>">
      </div>
      <!-- Additional Images -->
      <div class="col-12">
        <div class="row">
          <?php foreach ($images as $image) : ?>
            <div class="col-6 col-md-3 mb-3">
              <img src="<?php echo $image['image_path']; ?>" class="img-fluid additional-image" alt="Room Image">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <!-- Room Details -->
      <div class="col-12 mt-4">
        <h2 class="title" style="font-family: 'Montserrat', sans-serif; color: #18d26e;"><?php echo $room['room_name']; ?></h2>
      </div>
      <p class="stock-info mb-0" style="color: #18d26e; font-size: 17px;">Tersedia <?php echo $available_stock; ?> / <?php echo $total_stock; ?> ruangan</p>
      <!-- Room Description and Facilities -->
      <div class="col-12 mt-3">
        <div class="row">
          <!-- Description -->
          <div class="col-md-9 mb-3 mb-md-0">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><b>Deskripsi</b></h5>
                <p class="card-text" style="text-align: justify;"><?php echo $room['description']; ?></p>
              </div>
            </div>
          </div>
          <!-- Facilities -->
          <div class="col-md-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><b>Fasilitas</b></h5>
                <ul>
                  <?php foreach (explode(',', $room['facilities']) as $facility) : ?>
                    <li><?php echo $facility; ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Stock and Book Now Button -->
      <div class="col-12 mt-4">
        <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
          <!-- <p class="stock-info mb-0">Tersedia <?php echo $available_stock; ?> / <?php echo $total_stock; ?></p> -->
          <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($available_stock > 0): ?>
              <a href="checkout.php?room_id=<?php echo $room['room_id']; ?>" class="btn btn-primary btn-lg">Pesan Sekarang</a>
            <?php else: ?>
              <button class="btn btn-secondary btn-lg" disabled>Ruangan Tidak Tersedia</button>
            <?php endif; ?>
          <?php else: ?>
            <button class="btn btn-primary btn-lg" onclick="alert('Anda harus login terlebih dahulu!'); window.location.href='masuk.php';">Pesan Sekarang</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright 2024 <strong>BookMySpace</strong>. All Rights Reserved
      </div>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>

<?php
$conn->close();
?>
