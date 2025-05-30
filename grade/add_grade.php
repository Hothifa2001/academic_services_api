<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

// قراءة البيانات من Flutter
$data = json_decode(file_get_contents("php://input"), true);

$academic_id = $data['academic_id'] ?? null;
$course_id = $data['course_id'] ?? null;
$academic_year = $data['academic_year'] ?? null;
$attendance_grade = $data['attendance_grade'] ?? 0;
$midterm_grade = $data['midterm_grade'] ?? 0;
$assignment_grade = $data['assignment_grade'] ?? 0;

// التحقق من وجود الحقول المطلوبة
if (!$academic_id || !$course_id || !$academic_year) {
  echo json_encode(["status" => "error", "message" => "بيانات ناقصة"]);
  exit;
}

// جلب معرف الطالب بناءً على الرقم الأكاديمي
$result = $conn->query("SELECT id FROM student WHERE Academic_ID = '$academic_id'");
if (!$result || $result->num_rows == 0) {
  echo json_encode(["status" => "error", "message" => "رقم الطالب الأكاديمي غير موجود"]);
  exit;
}
$student_id = $result->fetch_assoc()['id'];

// التحقق من وجود السجل مسبقًا
$check = $conn->prepare("SELECT 1 FROM grade WHERE student_id = ? AND course_id = ? AND academic_year = ?");
$check->bind_param("iis", $student_id, $course_id, $academic_year);
$check->execute();
$existing = $check->get_result();

if ($existing->num_rows > 0) {
  echo json_encode(["status" => "error", "message" => "السجل موجود مسبقًا، استخدم التعديل"]);
  exit;
}

// إدخال الدرجات
$stmt = $conn->prepare("INSERT INTO grade (student_id, course_id, academic_year, attendance_grade, midterm_grade, assignment_grade) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissdd", $student_id, $course_id, $academic_year, $attendance_grade, $midterm_grade, $assignment_grade);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "تمت إضافة الدرجات بنجاح"]);
} else {
  echo json_encode(["status" => "error", "message" => "فشل في الإضافة", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
