<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>BookMySpace</title>
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

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top d-flex align-items-center header-transparent">
        <div class="container-fluid">

            <div class="row justify-content-center align-items-center">
                <div class="col-xl-11 d-flex align-items-center justify-content-between">
                    <h1 class="logo"><a href="index.php">BookMySpace</a></h1>

                    <nav id="navbar" class="navbar">
                        <ul>
                            <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
                            <li><a class="nav-link scrollto" href="#workspace">Work Space</a></li>
                            <li><a class="nav-link scrollto" href="#layanan">Layanan</a></li>
                            <li><a class="nav-link scrollto" href="#testimonials">Testimoni</a></li>
                            <?php if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') : ?>
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

  <!-- ======= hero Section ======= -->
  <section id="hero">
    <div class="hero-container">
      <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        <ol id="hero-carousel-indicators" class="carousel-indicators"></ol>

        <div class="carousel-inner" role="listbox">

          <div class="carousel-item active" style="background-image: url(assets/img/hero-carousel/1.jpg)">
            <div class="carousel-container">
              <div class="container">
                <h2 class="animate__animated animate__fadeInDown">Selamat Datang di BookMySpace!</h2>
                <p class="animate__animated animate__fadeInUp">Nikmati kemudahan pemesanan ruang kerja hanya dengan beberapa klik. BookMySpace menghadirkan antarmuka yang user-friendly untuk pengalaman yang lebih cepat dan efisien.</p>
              </div>
            </div>
          </div>

          <div class="carousel-item" style="background-image: url(assets/img/hero-carousel/2.jpg)">
            <div class="carousel-container">
              <div class="container">
                <h2 class="animate__animated animate__fadeInDown">Beragam Pilihan Ruangan</h2>
                <p class="animate__animated animate__fadeInUp">Temukan berbagai pilihan ruangan mulai dari reguler, VIP, hingga VVIP sesuai kebutuhan Anda. Setiap ruangan dilengkapi dengan fasilitas terbaik untuk mendukung produktivitas kerja Anda.</p>
              </div>
            </div>
          </div>

          <div class="carousel-item" style="background-image: url(assets/img/hero-carousel/3.jpg)">
            <div class="carousel-container">
              <div class="container">
                <h2 class="animate__animated animate__fadeInDown">Harga Fleksibel dan Terjangkau</h2>
                <p class="animate__animated animate__fadeInUp">Dapatkan penawaran harga yang fleksibel dengan pilihan per jam, harian, atau bulanan. BookMySpace menyediakan opsi harga yang terjangkau agar sesuai dengan anggaran dan kebutuhan Anda.</p>
              </div>
            </div>
          </div>
        </div>

        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        </a>

        <a class="carousel-control-next" href="#heroCarousel" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        </a>

      </div>
    </div>
  </section><!-- End Hero Section -->

  <main id="main">

    <!-- ======= Work Space Section ======= -->
    <section id="workspace">
      <div class="container" data-aos="fade-up" style="margin-top: 20px;">
        <header class="section-header">
          <h3>Work Space</h3>
          <p>Temukan berbagai pilihan ruangan mulai dari reguler, VIP, hingga VVIP sesuai kebutuhan Anda. Setiap ruangan dilengkapi dengan fasilitas terbaik untuk mendukung produktivitas kerja Anda.</p>
        </header>

        <div class="row about-cols">
          <?php
          require('koneksi.php');
          // SQL query to fetch room data
          $sql = "SELECT * FROM rooms";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
              echo '<div class="col-md-4" data-aos="fade-up" data-aos-delay="100">';
              echo '<div class="about-col">';
              echo '<div class="img">';
              echo '<a href="rooms.php?room_id=' . $row["room_id"] . '"><img src="' . $row["image"] . '" alt="' . $row["room_name"] . '" class="img-fluid"></a>';
              echo '</div>';
              echo '<h2 class="title"><a href="#">' . $row["room_name"] . '</a></h2>';
              echo '<p><b>Fasilitas</b>: ' . $row["facilities"] . '</p>';
              echo '<p><b>Tersedia</b>: ' . $row["stock"] . ' ruangan</p>';
              echo '<a href="rooms.php?room_id=' . $row["room_id"] . '" class="btn-get-started scrollto">Lihat Selengkapnya</a>';
              echo '</div>';
              echo '</div>';
            }
          } else {
            echo "0 results";
          }

          // Close connection
          $conn->close();
          ?>
        </div>
      </div>
    </section>

    <!-- ======= Services Section ======= -->
    <section id="layanan">
      <div class="container" data-aos="fade-up" style="margin-top: 20px;">

        <header class="section-header wow fadeInUp">
          <h3>Layanan</h3>
        </header>

        <div class="row">

          <div class="col-lg-4 col-md-6 box" data-aos="fade-up" data-aos-delay="100">
            <div class="icon"><i class="bi bi-briefcase"></i></div>
            <h4 class="title"><a href="">Pemesanan Ruang Kerja Online</a></h4>
            <p class="description">Temukan dan pesan ruang kerja yang ideal dengan mudah melalui platform kami. Pilih dari berbagai tipe ruangan yang sesuai dengan kebutuhan dan preferensi Anda.</p>
          </div>
          <div class="col-lg-4 col-md-6 box" data-aos="fade-up" data-aos-delay="200">
            <div class="icon"><i class="bi bi-card-checklist"></i></div>
            <h4 class="title"><a href="">Pilihan Ruangan Premium</a></h4>
            <p class="description">Manjakan diri Anda dengan fasilitas premium di ruangan VIP dan VVIP. Kami menyediakan ruang kerja dengan kenyamanan dan layanan ekstra untuk pengalaman bekerja yang lebih mewah.</p>
          </div>
          <div class="col-lg-4 col-md-6 box" data-aos="fade-up" data-aos-delay="300">
            <div class="icon"><i class="bi bi-cash-coin"></i></div>
            <h4 class="title"><a href="">Paket Harga Fleksibel</a></h4>
            <p class="description">Dapatkan paket harga fleksibel dengan pilihan per jam, harian, atau bulanan. Kami menawarkan harga yang kompetitif untuk memastikan Anda mendapatkan nilai terbaik.</p>
          </div>
          <div class="col-lg-4 col-md-6 box" data-aos="fade-up" data-aos-delay="200">
            <div class="icon"><i class="bi bi-check2-square"></i></div>
            <h4 class="title"><a href="">Fasilitas Lengkap</a></h4>
            <p class="description">Setiap ruangan dilengkapi dengan fasilitas modern seperti Wi-Fi cepat, ruang meeting, area santai, dan lainnya untuk mendukung produktivitas Anda.</p>
          </div>
          <div class="col-lg-4 col-md-6 box" data-aos="fade-up" data-aos-delay="300">
            <div class="icon"><i class="bi bi-person-gear"></i></div>
            <h4 class="title"><a href="">Dukungan Pelanggan 24/7</a></h4>
            <p class="description">Tim dukungan pelanggan kami siap membantu Anda kapan saja. Kami berkomitmen untuk memberikan layanan terbaik dan solusi cepat untuk setiap masalah yang Anda hadapi.</p>
          </div>
          <div class="col-lg-4 col-md-6 box" data-aos="fade-up" data-aos-delay="400">
            <div class="icon"><i class="bi bi-shield-check"></i></i></div>
            <h4 class="title"><a href="">Keamanan dan Privasi Terjamin</a></h4>
            <p class="description">Kami memastikan setiap ruangan dilengkapi dengan sistem keamanan dan privasi yang canggih. Nikmati bekerja tanpa khawatir tentang keamanan data dan privasi Anda.</p>
          </div>

        </div>

      </div>
    </section><!-- End Services Section -->

    <!-- ======= Testimonials Section ======= -->
    <section id="testimonials" class="section-bg">
      <div class="container" data-aos="fade-up">

        <header class="section-header">
          <h3>Testimoni</h3>
        </header>

        <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100">
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonial-1.jpg" class="testimonial-img" alt="">
                <h3>Andi Pratama</h3>
                <h4>Programmer</h4>
                <p>
                  <img src="assets/img/quote-sign-left.png" class="quote-sign-left" alt="">
                  BookMySpace benar-benar memudahkan saya menemukan ruang kerja yang nyaman dan profesional. Fasilitasnya lengkap dan pemesanan sangat mudah. Saya bisa fokus bekerja tanpa gangguan.
                  <img src="assets/img/quote-sign-right.png" class="quote-sign-right" alt="">
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonial-2.jpg" class="testimonial-img" alt="">
                <h3>Rina Setiawan</h3>
                <h4>Desainer Grafis</h4>
                <p>
                  <img src="assets/img/quote-sign-left.png" class="quote-sign-left" alt="">
                  Saya sangat terkesan dengan pilihan ruang kerja yang ditawarkan BookMySpace. Dari ruangan reguler hingga VVIP, semuanya dilengkapi dengan fasilitas modern yang mendukung produktivitas saya.
                  <img src="assets/img/quote-sign-right.png" class="quote-sign-right" alt="">
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonial-3.jpg" class="testimonial-img" alt="">
                <h3>Siti Nurhaliza</h3>
                <h4>Penulis</h4>
                <p>
                  <img src="assets/img/quote-sign-left.png" class="quote-sign-left" alt="">
                  Keamanan dan privasi di ruang kerja BookMySpace sangat terjamin. Saya bisa bekerja dengan tenang dan fokus penuh pada tulisan saya tanpa khawatir tentang gangguan atau masalah keamanan.
                  <img src="assets/img/quote-sign-right.png" class="quote-sign-right" alt="">
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonial-4.jpg" class="testimonial-img" alt="">
                <h3>Budi Santoso</h3>
                <h4>Konsultan Bisnis</h4>
                <p>
                  <img src="assets/img/quote-sign-left.png" class="quote-sign-left" alt="">
                  BookMySpace memberikan fleksibilitas yang saya butuhkan. Dengan berbagai pilihan harga dan durasi pemesanan, saya bisa mengatur anggaran dan waktu saya dengan lebih efisien.
                  <img src="assets/img/quote-sign-right.png" class="quote-sign-right" alt="">
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonial-5.jpg" class="testimonial-img" alt="">
                <h3>Agus Wibowo</h3>
                <h4>Manajer Pemasaran</h4>
                <p>
                  <img src="assets/img/quote-sign-left.png" class="quote-sign-left" alt="">
                  BookMySpace benar-benar mengerti kebutuhan profesional modern. Dari ruangan yang nyaman hingga fasilitas lengkap, semuanya mendukung produktivitas saya. Dukungan pelanggan yang responsif juga sangat membantu.
                  <img src="assets/img/quote-sign-right.png" class="quote-sign-right" alt="">
                </p>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>
    </section><!-- End Testimonials Section -->

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