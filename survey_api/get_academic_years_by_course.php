<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$courseId = $_GET['course_id'] ?? null;

if (!$courseId) {
  echo json_encode(['status' => 'error', 'message' => 'course_id مفقود']);
  exit;
}

$result = $conn->query("
  SELECT DISTINCT academic_year 
  FROM course_instructor 
  WHERE course_id = $courseId
");

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row['academic_year'];
}

echo json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
?>
