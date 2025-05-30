<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$questions = $data['questions'] ?? [];

if (empty($questions)) {
  echo json_encode(['status' => 'error', 'message' => 'لا يوجد أسئلة لإدخالها']);
  exit;
}

$stmt = $conn->prepare("INSERT INTO survey_question (survey_id, question) VALUES (?, ?)");

foreach ($questions as $q) {
  $surveyId = $q['survey_id'] ?? null;
  $question = $q['question'] ?? '';

  if (!$surveyId || empty($question)) {
    echo json_encode(['status' => 'error', 'message' => 'survey_id أو السؤال مفقود']);
    exit;
  }

  $stmt->bind_param("is", $surveyId, $question);
  $stmt->execute();
}

echo json_encode(['status' => 'success']);
?>
