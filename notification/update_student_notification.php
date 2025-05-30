<?php
include '../config.php';
header('Content-Type: application/json');

// ✅ قراءة JSON
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من القيم
$id           = $data['id'] ?? null;
$title        = $data['title'] ?? '';
$content      = $data['content'] ?? '';
$department_id = $data['department_id'] ?? null;
$major_id      = $data['major_id'] ?? null;
$level_id      = $data['level_id'] ?? null;
$course_id     = $data['course_id'] ?? null;

if (!$id || empty($title) || empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// تحضير الاستعلام
$stmt = $conn->prepare("
    UPDATE student_notification
    SET title = ?, content = ?, department_id = ?, major_id = ?, level_id = ?, course_id = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssiiiii",
    $title,
    $content,
    $department_id,
    $major_id,
    $level_id,
    $course_id,
    $id
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Student notification updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
