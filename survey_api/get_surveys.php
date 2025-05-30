<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$result = $conn->query("
  SELECT s.*, sup.full_name AS supervisor_name
  FROM survey s
  LEFT JOIN supervisor sup ON s.supervisor_id = sup.id
  ORDER BY s.id DESC
");

$surveys = [];
while ($row = $result->fetch_assoc()) {
  $surveys[] = [
    'id' => (int)$row['id'],
    'title' => $row['title'],
    'target_is_student' => (int)$row['target_is_student'],
    'supervisor_id' => $row['supervisor_id'],
    'supervisor_name' => $row['supervisor_name']
  ];
}

echo json_encode(['status' => 'success', 'data' => $surveys], JSON_UNESCAPED_UNICODE);
?>
