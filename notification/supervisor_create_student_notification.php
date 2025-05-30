<?php
include '../config.php';
header('Content-Type: application/json');

// قراءة بيانات JSON
$data = json_decode(file_get_contents('php://input'), true);

$title          = $data['title'] ?? '';
$content        = $data['content'] ?? '';
$supervisor_id  = $data['supervisor_id'] ?? '';
$department_id  = $data['department_id'] ?? null;
$major_id       = $data['major_id'] ?? null;
$level_id       = $data['level_id'] ?? null;

if (empty($title) || empty($content) || empty($supervisor_id) || empty($department_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO supervisor_to_student_notifications 
(title, content, supervisor_id, department_id, major_id, level_id) 
VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssiiii", $title, $content, $supervisor_id, $department_id, $major_id, $level_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Notification created successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
