<?php
include '../config.php';
header('Content-Type: application/json');

// ✅ قراءة بيانات JSON من الجسم
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من الحقول
$title       = $data['title'] ?? '';
$content     = $data['content'] ?? '';
$sender_id   = $data['sender_id'] ?? '';
$sender_role = $data['sender_role'] ?? 'supervisor';

$department_id = $data['department_id'] ?? null;
$major_id      = $data['major_id'] ?? null;
$level_id      = $data['level_id'] ?? null;
$course_id     = $data['course_id'] ?? null;

if (empty($title) || empty($content) || empty($sender_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO student_notification 
    (title, content, sender_id, sender_role, department_id, major_id, level_id, course_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssissiii",
    $title,
    $content,
    $sender_id,
    $sender_role,
    $department_id,
    $major_id,
    $level_id,
    $course_id
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Student notification created']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
