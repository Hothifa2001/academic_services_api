<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$title = $data['title'] ?? '';
$targetIsStudent = $data['target_is_student'] ?? 1;
$supervisorId = $data['supervisor_id'] ?? null;

if (empty($title)) {
  echo json_encode(['status' => 'error', 'message' => 'العنوان مطلوب']);
  exit;
}

$stmt = $conn->prepare("INSERT INTO survey (title, target_is_student, supervisor_id) VALUES (?, ?, ?)");
$stmt->bind_param("sii", $title, $targetIsStudent, $supervisorId);
if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل في إنشاء الاستبيان']);
}
?>
