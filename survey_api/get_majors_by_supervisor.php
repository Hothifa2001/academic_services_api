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

// جلب التخصصات الخاصة بهذا القسم
$stmt = $conn->prepare("SELECT id, name FROM major WHERE department_id = ?");
$stmt->bind_param("i", $departmentId);
$stmt->execute();
$res = $stmt->get_result();

$majors = [];
while ($row = $res->fetch_assoc()) {
  $majors[] = $row;
}

echo json_encode(["status" => "success", "data" => $majors], JSON_UNESCAPED_UNICODE);
