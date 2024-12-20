<?php
session_start();
require 'koneksi.php'; // Include database connection file
require 'assets/vendor/tcpdf/tcpdf.php'; // Include the TCPDF library

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

// Fetch booking details
$query = "SELECT b.booking_id, b.created_at, b.invoice, b.status, b.total_price, b.payment_status, b.payment_date, r.room_name, r.image, b.start_time, b.end_time 
          FROM bookings b
          JOIN rooms r ON b.room_id = r.room_id
          WHERE b.user_id = ? AND b.booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    header("Location: riwayat.php");
    exit();
}

// Create a new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('BookMySpace');
$pdf->SetTitle('Invoice Booking');
$pdf->SetSubject('Invoice Booking');
$pdf->SetKeywords('TCPDF, PDF, invoice, booking, bookmyspace');

// Remove default header
$pdf->setPrintHeader(false);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font for header
$pdf->SetFont('helvetica', 'B', 20);

// Add header text
$pdf->Cell(0, 15, 'BookMySpace', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

// Set font for content
$pdf->SetFont('helvetica', '', 12);

// Add content
$html = '
<h1>Invoice Booking</h1>
<table border="1" cellpadding="5">
    <tr>
        <td><strong>Invoice ID:</strong></td>
        <td>' . $booking['invoice'] . '</td>
    </tr>
    <tr>
        <td><strong>Room Name:</strong></td>
        <td>' . htmlspecialchars($booking['room_name']) . '</td>
    </tr>
    <tr>
        <td><strong>Booking Date:</strong></td>
        <td>' . date('d M Y', strtotime($booking['created_at'])) . '</td>
    </tr>
    <tr>
        <td><strong>Start Time:</strong></td>
        <td>' . date('d M Y H:i', strtotime($booking['start_time'])) . '</td>
    </tr>
    <tr>
        <td><strong>End Time:</strong></td>
        <td>' . date('d M Y H:i', strtotime($booking['end_time'])) . '</td>
    </tr>
    <tr>
        <td><strong>Status:</strong></td>
        <td>' . ucfirst($booking['status']) . '</td>
    </tr>
    <tr>
        <td><strong>Total Price:</strong></td>
        <td>Rp' . number_format($booking['total_price'], 0, ',', '.') . '</td>
    </tr>
    <tr>
        <td><strong>Payment Status:</strong></td>
        <td>' . ($booking['payment_date'] ? ucfirst(str_replace('_', ' ', $booking['payment_status'])) : 'Belum Dibayar') . '</td>
    </tr>
</table>
';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// Close and output PDF document
$pdf->Output('invoice_' . $booking['booking_id'] . '.pdf', 'I');
?>
