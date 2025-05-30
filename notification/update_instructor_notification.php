<?php
include '../config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$id           = $data['id'] ?? null;
$title        = $data['title'] ?? '';
$content      = $data['content'] ?? '';
$department_id = $data['department_id'] ?? null;
$instructor_id = $data['instructor_id'] ?? null;

if (!$id || empty($title) || empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE instructor_notification
    SET title = ?, content = ?, department_id = ?, instructor_id = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssiii",
    $title,
    $content,
    $department_id,
    $instructor_id,
    $id
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Instructor notification updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
