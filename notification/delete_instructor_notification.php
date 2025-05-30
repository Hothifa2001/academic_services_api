<?php
include '../config.php';
header('Content-Type: application/json');

// ✅ قراءة البيانات بصيغة JSON
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing notification ID']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM instructor_notification WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Instructor notification deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
