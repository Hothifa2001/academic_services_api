<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$distributionId = $data['distribution_id'] ?? null;
$questionId = $data['question_id'] ?? null;
$choiceId = $data['choice_id'] ?? null;

if (!$distributionId || !$questionId || !$choiceId) {
  echo json_encode(['status' => 'error', 'message' => 'البيانات ناقصة']);
  exit;
}

$stmt = $conn->prepare("INSERT INTO survey_answer (distribution_id, question_id, choice_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $distributionId, $questionId, $choiceId);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل حفظ الإجابة']);
}
?>
