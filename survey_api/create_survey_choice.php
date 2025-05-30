<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data    = json_decode(file_get_contents("php://input"), true);
$choices = $data['choices'] ?? [];

if (empty($choices)) {
  echo json_encode(['status'=>'error','message'=>'لا يوجد خيارات لإدخالها']);
  exit;
}

$stmt = $conn->prepare("
  INSERT INTO survey_choice (survey_id, choice_text, choice_value)
  VALUES (?, ?, ?)
");

foreach ($choices as $c) {
  $surveyId    = $c['survey_id']    ?? null;
  $choiceText  = $c['choice_text']  ?? '';
  $choiceValue = $c['choice_value'] ?? 0;

  if (!$surveyId || $choiceText === '') {
    echo json_encode(['status'=>'error','message'=>'بيانات خيارات ناقصة']);
    exit;
  }

  $stmt->bind_param("isi", $surveyId, $choiceText, $choiceValue);
  $stmt->execute();
}

echo json_encode(['status'=>'success']);
