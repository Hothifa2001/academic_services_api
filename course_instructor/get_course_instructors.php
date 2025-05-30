<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$course_id = $_GET['course_id'];

$sql = "SELECT
            course_instructor.instructor_id,
            instructor.full_name AS instructor_name,
            course_instructor.academic_year
        FROM course_instructor
        JOIN instructor ON course_instructor.instructor_id = instructor.id
        WHERE course_instructor.course_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode(["status" => "success", "data" => $data]);

$stmt->close();
$conn->close();
