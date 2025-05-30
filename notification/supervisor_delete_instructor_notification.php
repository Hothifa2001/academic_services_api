<?php
include '../config.php';
header('Content-Type: application/json');

// قراءة بيانات JSON من البودي
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing notification ID']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM supervisor_to_instructor_notifications WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Notification deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
