<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT
            course.id,
            course.name,
            course.major_id,
            major.name AS major_name,
            course.level_id,
            level.name AS level_name,
            course.term_id,
            term.name AS term_name
        FROM course
        JOIN major ON course.major_id = major.id
        JOIN level ON course.level_id = level.id
        JOIN term ON course.term_id = term.id";

$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode(["status" => "success", "data" => $data]);

$conn->close();
