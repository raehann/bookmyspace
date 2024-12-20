<?php
session_start();
require 'koneksi.php'; // Include database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

if (!isset($_GET['booking_id'])) {
    header("Location: riwayat.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

// Begin transaction
$conn->begin_transaction();

try {
    // Update booking status to 'dibatalkan'
    $query = "UPDATE bookings SET status = 'dibatalkan' WHERE booking_id = ? AND user_id = ? AND status = 'menunggu'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Get room_id from bookings table
        $query = "SELECT room_id FROM bookings WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();
        $room_id = $booking['room_id'];

        // Update stock in rooms table
        $query = "UPDATE rooms SET stock = stock + 1 WHERE room_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        $_SESSION['success_message'] = "Pemesanan berhasil dibatalkan dan stok ruangan telah diperbarui.";
    } else {
        $_SESSION['error_message'] = "Gagal membatalkan pemesanan atau pemesanan sudah tidak bisa dibatalkan.";
    }

    $stmt->close();
} catch (Exception $e) {
    // Rollback transaction if something goes wrong
    $conn->rollback();
    $_SESSION['error_message'] = "Terjadi kesalahan: " . $e->getMessage();
}

header("Location: riwayat.php");
exit();
?>