<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$surveyId = $_GET['survey_id'] ?? null;
if (!$surveyId) {
  echo json_encode(['status' => 'error', 'message' => 'survey_id مطلوب']);
  exit;
}

$result = $conn->query("SELECT * FROM survey_question WHERE survey_id = $surveyId ORDER BY id ASC");
$questions = [];
while ($row = $result->fetch_assoc()) {
  $questions[] = [
    'id' => (int)$row['id'],
    'survey_id' => (int)$row['survey_id'],
    'question' => $row['question'],
  ];
}
echo json_encode(['status' => 'success', 'data' => $questions], JSON_UNESCAPED_UNICODE);
?>
