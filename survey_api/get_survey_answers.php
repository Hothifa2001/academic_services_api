<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$distributionId = $_GET['distribution_id'] ?? null;
if (!$distributionId) {
  echo json_encode(['status' => 'error', 'message' => 'distribution_id مفقود']);
  exit;
}

$result = $conn->query("
  SELECT a.id, a.distribution_id, a.question_id, q.question, a.choice_id, c.choice_text
  FROM survey_answer a
  JOIN survey_question q ON a.question_id = q.id
  JOIN survey_choice c ON a.choice_id = c.id
  WHERE a.distribution_id = $distributionId
");

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = [
    'id' => (int)$row['id'],
    'distribution_id' => (int)$row['distribution_id'],
    'question_id' => (int)$row['question_id'],
    'question' => $row['question'],
    'choice_id' => (int)$row['choice_id'],
    'choice_text' => $row['choice_text']
  ];
}
echo json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
?>
