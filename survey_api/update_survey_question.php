<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$question = $data['question'] ?? '';

if (!$id || empty($question)) {
  echo json_encode(['status' => 'error', 'message' => 'بيانات غير مكتملة']);
  exit;
}

$stmt = $conn->prepare("UPDATE survey_question SET question = ? WHERE id = ?");
$stmt->bind_param("si", $question, $id);
if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل في التحديث']);
}
?>
