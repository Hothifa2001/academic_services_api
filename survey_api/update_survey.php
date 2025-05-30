<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$title = $data['title'] ?? '';
$targetIsStudent = $data['target_is_student'] ?? 1;
$supervisorId = $data['supervisor_id'] ?? null;

if (!$id || empty($title)) {
  echo json_encode(['status' => 'error', 'message' => 'بيانات غير مكتملة']);
  exit;
}

$stmt = $conn->prepare("UPDATE survey SET title = ?, target_is_student = ?, supervisor_id = ? WHERE id = ?");
$stmt->bind_param("siii", $title, $targetIsStudent, $supervisorId, $id);
if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل التحديث']);
}
?>
