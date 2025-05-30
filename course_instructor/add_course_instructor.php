<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$course_id = $data['course_id'];
$instructor_id = $data['instructor_id'];
$academic_year = $data['academic_year'];

// التحقق قبل الإضافة: منع التكرار
$checkQuery = $conn->prepare("SELECT * FROM course_instructor WHERE course_id = ? AND instructor_id = ? AND academic_year = ?");
$checkQuery->bind_param("iis", $course_id, $instructor_id, $academic_year);
$checkQuery->execute();
$result = $checkQuery->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "هذا المدرس مرتبط بالفعل بهذه المادة في هذه السنة الأكاديمية"]);
} else {
    $stmt = $conn->prepare("INSERT INTO course_instructor (instructor_id, course_id, academic_year) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $instructor_id, $course_id, $academic_year);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "تم ربط المدرس بالمادة بنجاح"]);
    } else {
        echo json_encode(["status" => "error", "message" => "فشل في إضافة الربط", "error" => $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
