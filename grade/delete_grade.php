<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

// قراءة البيانات
$data = json_decode(file_get_contents("php://input"), true);

$academic_id = $data['academic_id'] ?? null;
$course_id = $data['course_id'] ?? null;
$academic_year = $data['academic_year'] ?? null;

if (!$academic_id || !$course_id || !$academic_year) {
  echo json_encode(["status" => "error", "message" => "بيانات ناقصة"]);
  exit;
}

// تحويل Academic_ID إلى student_id
$result = $conn->query("SELECT id FROM student WHERE Academic_ID = '$academic_id'");
if (!$result || $result->num_rows == 0) {
  echo json_encode(["status" => "error", "message" => "الطالب غير موجود"]);
  exit;
}
$student_id = $result->fetch_assoc()['id'];

// التأكد من وجود السجل
$check = $conn->prepare("SELECT 1 FROM grade WHERE student_id = ? AND course_id = ? AND academic_year = ?");
$check->bind_param("iis", $student_id, $course_id, $academic_year);
$check->execute();
$existing = $check->get_result();

if ($existing->num_rows === 0) {
  echo json_encode(["status" => "error", "message" => "السجل غير موجود"]);
  exit;
}

// تنفيذ الحذف
$stmt = $conn->prepare("DELETE FROM grade WHERE student_id = ? AND course_id = ? AND academic_year = ?");
$stmt->bind_param("iis", $student_id, $course_id, $academic_year);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "تم حذف الدرجات بنجاح"]);
} else {
  echo json_encode(["status" => "error", "message" => "فشل في الحذف", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
