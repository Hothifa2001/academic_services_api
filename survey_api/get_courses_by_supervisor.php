<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$supervisorId = $_GET['supervisor_id'] ?? null;

if (!$supervisorId) {
  echo json_encode(["status" => "error", "message" => "supervisor_id مفقود"]);
  exit;
}

// جلب القسم المرتبط بالمشرف
$stmt = $conn->prepare("SELECT department_id FROM supervisor WHERE id = ?");
$stmt->bind_param("i", $supervisorId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
  echo json_encode(["status" => "error", "message" => "لم يتم العثور على المشرف"]);
  exit;
}

$departmentId = $row['department_id'];

// جلب المواد المرتبطة بالتخصصات في القسم
$query = "
  SELECT c.id, c.name, c.major_id, c.level_id, c.term_id
  FROM course c
  INNER JOIN major m ON c.major_id = m.id
  WHERE m.department_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $departmentId);
$stmt->execute();
$res = $stmt->get_result();

$courses = [];
while ($row = $res->fetch_assoc()) {
  $courses[] = $row;
}

echo json_encode(["status" => "success", "data" => $courses], JSON_UNESCAPED_UNICODE);
