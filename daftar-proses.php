<?php
require('koneksi.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Prevent SQL injection
    $nama = mysqli_real_escape_string($conn, $nama);
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);
    $email = mysqli_real_escape_string($conn, $email);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username sudah ada di database
    $check_username_query = "SELECT username FROM users WHERE username = '$username'";
    $result = $conn->query($check_username_query);

    if ($result->num_rows > 0) {
        // Username sudah ada, tampilkan pesan error dan arahkan kembali ke halaman daftar
        echo "<script>alert('Username sudah ada. Silakan coba lagi dengan username lain.'); location='daftar.php'; </script>";
        exit();
    }

    // Upload gambar
    $target_dir = "assets/img/user/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi ekstensi file
    if ($imageFileType != "jpg" && $imageFileType != "png") {
        echo "<script>alert('Hanya file JPG dan PNG yang diizinkan.'); location='daftar.php'; </script>";
        exit();
    }

    // Ubah nama file sesuai dengan username
    $target_file = $target_dir . $username . "." . $imageFileType;

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "<script>alert('Username sudah ada. Silakan coba lagi dengan username lain.'); location='daftar.php'; </script>";
        exit();
    }

    // Check file size (max 5MB)
    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        echo "<script>alert('Maaf, file terlalu besar.'); location='daftar.php'; </script>";
        exit();
    }

    // Upload file
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "<script>alert('Maaf, terjadi kesalahan saat mengunggah file.'); location='daftar.php'; </script>";
        exit();
    }

    // Query untuk insert data ke database dengan role default "user"
    $sql = "INSERT INTO users (name, username, password, email, user_image, created_at, role)
        VALUES ('$nama', '$username', '$hashed_password', '$email', '$target_file', NOW(), 'user')";

    if ($conn->query($sql) === TRUE) {
        // Jika registrasi berhasil
        echo "<script>alert('Registrasi berhasil!'); location='masuk.php'; </script>";
    } else {
        // Jika terjadi kesalahan saat registrasi
        echo "<script>alert('Registrasi gagal. Silakan coba lagi.'); location='daftar.php'; </script>";
    }

    $conn->close();
}
?>
