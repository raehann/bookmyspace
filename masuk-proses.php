<?php
require('koneksi.php');
session_start();

// Ambil data dari form login
if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    // Query untuk mencari user berdasarkan username
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // User ditemukan
        $row = $result->fetch_assoc();

        // Verifikasi password menggunakan password_verify
        if (password_verify($password, $row['password'])) {
            // Password benar, set session berdasarkan role
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_image'] = $row['user_image'];
            $_SESSION['created_at'] = $row['created_at'];
            
            // Redirect sesuai role
            if ($_SESSION['role'] == 'admin') {
                header("Location: admin/index.php");
                exit(); // Pastikan exit setelah header redirect
            } else if ($_SESSION['role'] == 'user') {
                header("Location: index.php");
                exit(); // Pastikan exit setelah header redirect
            }
        } else {
            // Password salah
            $_SESSION['login_error'] = "Username atau Password salah!";
            header("Location: masuk.php");
            exit(); // Pastikan exit setelah header redirect
        }
    } else {
        // Jika user tidak ditemukan
        $_SESSION['login_error'] = "Username atau Password salah!";
        header("Location: masuk.php");
        exit(); // Pastikan exit setelah header redirect
    }
} else {
    // Jika $_POST['username'] atau $_POST['password'] tidak di-set
    $_SESSION['login_error'] = "Mohon masukkan username dan password!";
    header("Location: masuk.php");
    exit(); // Pastikan exit setelah header redirect
}

$conn->close();
?>
