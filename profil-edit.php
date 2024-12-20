<?php
session_start();
require 'koneksi.php'; // Include database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Query to get the current password from the database
    $query = "SELECT password FROM users WHERE user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_password);
    $stmt->fetch();
    $stmt->close();

    // Check if password field is empty
    if (empty($_POST['password'])) {
        $password = $current_password; // Use the existing password if no new password is provided
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the new password
    }

    // Handle profile image upload
    if (!empty($_FILES['user_image']['name'])) {
        $target_dir = "assets/img/user/";
        $target_file = $target_dir . $username . ".jpg"; // Set the target file name
        move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file);
        $user_image = $target_dir . $username . ".jpg"; // Set the user image file name

        $query = "UPDATE users SET name=?, username=?, email=?, password=?, user_image=? WHERE user_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $name, $username, $email, $password, $user_image, $user_id);
    } else {
        $query = "UPDATE users SET name=?, username=?, email=?, password=? WHERE user_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $name, $username, $email, $password, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profil berhasil diperbarui.'); location='profil.php'; </script>";
    } else {
        echo "<script>alert('Terjadi kesalahan. Coba lagi.'); location='profil.php'; </script>";
    }

    $stmt->close();
    $conn->close();

    exit();
}
?>
