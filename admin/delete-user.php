<?php
session_start();
require '../koneksi.php'; // Include database connection file

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

// Array to collect debug messages
$debug_messages = [];

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$user_id = $data['user_id'];

// Get the user's image file path
$query = "SELECT user_image FROM users WHERE user_id = ? AND role = 'user'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_image);
$stmt->fetch();
$stmt->close();

if (!$user_image) {
    echo json_encode(['success' => false, 'message' => 'User not found or not authorized to delete']);
    exit();
}

// Debugging: add user image path to debug messages
$debug_messages[] = "User image path: " . $user_image;

// Make sure the path is correct
$user_image_path = "../" . $user_image;

// Debugging: add user image full path to debug messages
$debug_messages[] = "User image full path: " . $user_image_path;

// Delete the user image file
if (file_exists($user_image_path)) {
    if (!unlink($user_image_path)) {
        $debug_messages[] = "Failed to delete user image: " . $user_image_path;
        echo json_encode(['success' => false, 'message' => 'Failed to delete user image', 'debug' => $debug_messages]);
        exit();
    } else {
        $debug_messages[] = "User image deleted: " . $user_image_path;
    }
} else {
    $debug_messages[] = "User image file does not exist: " . $user_image_path;
    echo json_encode(['success' => false, 'message' => 'User image file does not exist', 'debug' => $debug_messages]);
    exit();
}

// Prepare the delete query
$query = "DELETE FROM users WHERE user_id = ? AND role = 'user'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);

// Debugging: log before executing delete query
$debug_messages[] = "Executing delete query for user_id: " . $user_id;

if ($stmt->execute()) {
    // Check the affected rows to confirm deletion
    if ($stmt->affected_rows > 0) {
        $debug_messages[] = "User deleted successfully: " . $user_id;
        echo json_encode(['success' => true, 'debug' => $debug_messages]);
    } else {
        $debug_messages[] = "No rows affected, user not deleted: " . $user_id;
        echo json_encode(['success' => false, 'message' => 'No rows affected, user not deleted', 'debug' => $debug_messages]);
    }
} else {
    $debug_messages[] = "Failed to execute delete query: " . $stmt->error;
    echo json_encode(['success' => false, 'message' => 'Failed to delete user', 'debug' => $debug_messages]);
}

$stmt->close();
$conn->close();
?>
