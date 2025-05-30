<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

if (!$course_id) {
  echo json_encode(["status" => "error", "message" => "course_id مطلوب"]);
  exit;
}

$stmt = $conn->prepare("SELECT DISTINCT academic_year FROM course_instructor WHERE course_id = ? ORDER BY academic_year DESC");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$years = [];
while ($row = $result->fetch_assoc()) {
  $years[] = $row['academic_year'];
}

echo json_encode(["status" => "success", "data" => $years]);

$stmt->close();
$conn->close();
