<?php
require '../koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['booking_id']) && isset($data['status'])) {
    $booking_id = $data['booking_id'];
    $status = $data['status'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Update booking status
        $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $booking_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            if ($status == 'ditolak') {
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
            }

            // Commit transaction
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            // Rollback transaction if update failed
            $conn->rollback();
            echo json_encode(['success' => false]);
        }

        $stmt->close();
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false]);
}
$conn->close();
?>
