<?php
require '../koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['booking_id'])) {
    $booking_id = $data['booking_id'];

    $query = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false]);
}
$conn->close();
?>
