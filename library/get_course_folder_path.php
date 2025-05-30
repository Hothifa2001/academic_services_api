<?php
// C:\xampp\htdocs\academic_services_api\library\get_course_folder_path.php

header('Content-Type: application/json; charset=utf-8');
require_once('../config.php');

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['course_id']) || empty($input['academic_year'])) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'course_id and academic_year are required']);
    exit;
}

$course_id = intval($input['course_id']);
$academic_year = preg_replace('/[^0-9\-]/', '', $input['academic_year']);

// جلب تفاصيل المادة
$sql = "SELECT 
            d.name AS department_name,
            m.name AS major_name,
            l.name AS level_name,
            t.name AS term_name,
            c.name AS course_name
        FROM course c
        JOIN major m ON c.major_id = m.id
        JOIN department d ON m.department_id = d.id
        JOIN level l ON c.level_id = l.id
        JOIN term t ON c.term_id = t.id
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Course not found']);
    exit;
}

$row = $result->fetch_assoc();

// بناء المسار
$segments = [
    sanitize($row['department_name']),
    sanitize($row['major_name']),
    sanitize($row['level_name']),
    sanitize($row['term_name']),
    sanitize($row['course_name']),
    sanitize($academic_year)
];

$relative_path = implode('/', $segments);

echo json_encode([
    'status' => 'success',
    'folder_path' => $relative_path
], JSON_UNESCAPED_UNICODE);

// تنقية النص
function sanitize($str) {
    return preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $str);
}
?>
