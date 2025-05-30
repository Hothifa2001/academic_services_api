<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$surveyId = $_GET['survey_id'] ?? null;
if (!$surveyId) {
  echo json_encode(['status' => 'error', 'message' => 'survey_id مفقود']);
  exit;
}

$result = $conn->query("
  SELECT COUNT(DISTINCT participant.respondent_id) AS participants_count
  FROM survey_participant participant
  JOIN survey_distribution dist ON dist.id = participant.distribution_id
  WHERE dist.survey_id = $surveyId
");

$row = $result->fetch_assoc();
$count = $row['participants_count'] ?? 0;

echo json_encode(['status' => 'success', 'count' => (int)$count]);
?>
