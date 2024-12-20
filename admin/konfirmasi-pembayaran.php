<?php
session_start();
require '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  $booking_id = $data['booking_id'];

  $payment_date = date('Y-m-d H:i:s'); // Get current date and time

  $query = "
    UPDATE bookings 
    SET payment_status = 'sudah_dibayar', 
        payment_date = ?, 
        status = 'selesai' 
    WHERE booking_id = ?
  ";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('si', $payment_date, $booking_id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false]);
  }

  $stmt->close();
  $conn->close();
} else {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
}
?>
