<?php
require '../koneksi.php';

// Get the booking ID from the query parameters
$booking_id = $_GET['booking_id'];

// Prepare the SQL query to fetch booking details
$query = "
    SELECT 
        bookings.invoice,
        users.name AS customer_name,
        rooms.room_name AS room_name,
        DATE_FORMAT(bookings.start_time, '%d %b %Y %H:%i') AS start_time,
        DATE_FORMAT(bookings.end_time, '%d %b %Y %H:%i') AS end_time,
        bookings.total_price,
        bookings.payment_status
    FROM 
        bookings
    JOIN 
        users ON bookings.user_id = users.user_id
    JOIN 
        rooms ON bookings.room_id = rooms.room_id
    WHERE 
        bookings.booking_id = ?
";

// Prepare and execute the statement
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

// Return the booking details as JSON
echo json_encode($booking);
?>
