<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../config.php');

// التحقق من البيانات المرسلة
$input = json_decode(file_get_contents('php://input'), true);
if (
    empty($input['course_id']) ||
    empty($input['academic_year'])
) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'course_id and academic_year are required']);
    exit;
}

$course_id = intval($input['course_id']);
$academic_year = sanitize($input['academic_year']);

// 1. جلب تفاصيل المادة والمسارات المرتبطة بها
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
        WHERE c.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Course not found']);
    exit;
}

$row = $result->fetch_assoc();

// 2. بناء المسار الكامل للمجلد
$segments = [
    sanitize($row['department_name']),
    sanitize($row['major_name']),
    sanitize($row['level_name']),
    sanitize($row['term_name']),
    sanitize($row['course_name']),
    $academic_year
];
$relativePath = implode('/', $segments);

// 3. المسار الكامل في السيرفر
$root = realpath(dirname(__DIR__, 2) . '/library');
if (!$root) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Library root not found']);
    exit;
}

$target = $root . '/' . $relativePath;
if (!is_dir($target)) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Target folder does not exist']);
    exit;
}

// 4. قراءة الملفات داخل المجلد
$items = [];
foreach (scandir($target) as $entry) {
    if ($entry === '.' || $entry === '..') continue;
    $full = $target . DIRECTORY_SEPARATOR . $entry;
    $isDir = is_dir($full);
    $items[] = [
        'name' => $entry,
        'type' => $isDir ? 'dir' : 'file',
        'size' => $isDir ? null : filesize($full),
    ];
}

// 5. النتيجة
echo json_encode([
    'status' => 'success',
    'folder_path' => $relativePath,
    'data' => $items
], JSON_UNESCAPED_UNICODE);

// تنقية النصوص
function sanitize($text) {
    return preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $text);
}
?>
