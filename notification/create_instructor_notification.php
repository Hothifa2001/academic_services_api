<?php
include '../config.php';
header('Content-Type: application/json');

// ✅ قراءة البيانات JSON من الجسم
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من القيم الأساسية
$title       = $data['title'] ?? '';
$content     = $data['content'] ?? '';
$sender_id   = $data['sender_id'] ?? '';
$sender_role = $data['sender_role'] ?? 'supervisor';

$department_id = $data['department_id'] ?? null;
$instructor_id = $data['instructor_id'] ?? null;

if (empty($title) || empty($content) || empty($sender_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// تحويل القيم إلى أرقام صحيحة أو null
$department_id = is_numeric($department_id) ? (int)$department_id : null;
$instructor_id = is_numeric($instructor_id) ? (int)$instructor_id : null;

// تحضير الاستعلام
$stmt = $conn->prepare("
    INSERT INTO instructor_notification 
    (title, content, sender_id, sender_role, department_id, instructor_id) 
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssissi",
    $title,
    $content,
    $sender_id,
    $sender_role,
    $department_id,
    $instructor_id
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Instructor notification created']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
