<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$text = $data['choice_text'] ?? '';
$value = $data['choice_value'] ?? 0;

if (!$id || empty($text)) {
  echo json_encode(['status' => 'error', 'message' => 'بيانات ناقصة']);
  exit;
}

$stmt = $conn->prepare("UPDATE survey_choice SET choice_text = ?, choice_value = ? WHERE id = ?");
$stmt->bind_param("sii", $text, $value, $id);
if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل التحديث']);
}
?>
