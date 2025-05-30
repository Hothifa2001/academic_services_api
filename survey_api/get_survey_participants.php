<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$distributionId = $_GET['distribution_id'] ?? null;
if (!$distributionId) {
  echo json_encode(['status' => 'error', 'message' => 'distribution_id مفقود']);
  exit;
}

$result = $conn->query("SELECT * FROM survey_participant WHERE distribution_id = $distributionId");

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = [
    'id' => (int)$row['id'],
    'distribution_id' => (int)$row['distribution_id'],
    'respondent_id' => (int)$row['respondent_id'],
  ];
}
echo json_encode(['status' => 'success', 'data' => $data]);
?>
