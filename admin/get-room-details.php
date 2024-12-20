<?php
require '../koneksi.php';

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    $query = "SELECT * FROM rooms WHERE room_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    echo json_encode($room);
} else {
    echo json_encode(['error' => 'No room ID provided.']);
}
?>
