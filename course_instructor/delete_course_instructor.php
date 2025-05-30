<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$course_id = $data['course_id'];
$instructor_id = $data['instructor_id'];
$academic_year = $data['academic_year'];

$stmt = $conn->prepare("DELETE FROM course_instructor WHERE course_id = ? AND instructor_id = ? AND academic_year = ?");
$stmt->bind_param("iis", $course_id, $instructor_id, $academic_year);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم حذف الربط بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في حذف الربط", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
