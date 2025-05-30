<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$course_id = $_GET['course_id'];
$academic_year = $_GET['academic_year'];

$sql = \"SELECT i.full_name
        FROM course_instructor ci
        JOIN instructor i ON ci.instructor_id = i.id
        WHERE ci.course_id = ? AND ci.academic_year = ?
        LIMIT 1\";

$stmt = $conn->prepare($sql);
$stmt->bind_param(\"is\", $course_id, $academic_year);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode([\"status\" => \"success\", \"data\" => $row['full_name']]);
} else {
  echo json_encode([\"status\" => \"error\", \"message\" => \"لا يوجد مدرس لهذه المادة في السنة المحددة\"]);
}

$stmt->close();
$conn->close();
