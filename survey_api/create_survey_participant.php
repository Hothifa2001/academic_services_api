<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$distributionId = $data['distribution_id'] ?? null;
$respondentId = $data['respondent_id'] ?? null;

if (!$distributionId || !$respondentId) {
  echo json_encode(['status' => 'error', 'message' => 'بيانات ناقصة']);
  exit;
}

$stmt = $conn->prepare("INSERT IGNORE INTO survey_participant (distribution_id, respondent_id) VALUES (?, ?)");
$stmt->bind_param("ii", $distributionId, $respondentId);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل الإدخال']);
}
?>
