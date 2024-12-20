<?php
require '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $description = $_POST['description'];
    $facilities = $_POST['facilities'];
    $base_price_hour = $_POST['base_price_hour'];
    $base_price_day = $_POST['base_price_day'];
    $base_price_month = $_POST['base_price_month'];
    $stock = $_POST['stock'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../assets/img/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image = "assets/img/" . basename($_FILES['image']['name']);
    }

    $query = "UPDATE rooms SET description = ?, facilities = ?, base_price_hour = ?, base_price_day = ?, base_price_month = ?, stock = ?";
    if ($image !== '') {
        $query .= ", image = ?";
    }
    $query .= " WHERE room_id = ?";

    $stmt = $conn->prepare($query);
    if ($image !== '') {
        $stmt->bind_param("ssssissi", $description, $facilities, $base_price_hour, $base_price_day, $base_price_month, $stock, $image, $room_id);
    } else {
        $stmt->bind_param("ssssisi", $description, $facilities, $base_price_hour, $base_price_day, $base_price_month, $stock, $room_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}
?>
