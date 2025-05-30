<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$surveyId = $_GET['survey_id'] ?? null;
if (!$surveyId) {
  echo json_encode(['status' => 'error', 'message' => 'survey_id مطلوب']);
  exit;
}

$result = $conn->query("SELECT * FROM survey_choice WHERE survey_id = $surveyId");
$choices = [];
while ($row = $result->fetch_assoc()) {
  $choices[] = [
    'id' => (int)$row['id'],
    'survey_id' => (int)$row['survey_id'],
    'choice_text' => $row['choice_text'],
    'choice_value' => (int)$row['choice_value'],
  ];
}
echo json_encode(['status' => 'success', 'data' => $choices], JSON_UNESCAPED_UNICODE);
?>
