<?php
// C:\xampp\htdocs\academic_services_api\library\create_structure.php

header('Content-Type: application/json; charset=utf-8');
require_once('../config.php'); // الاتصال بقاعدة البيانات

// التحقق من وجود معرف المشرف في الطلب
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['supervisor_id'])) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'supervisor_id is required']);
    exit;
}
$supervisor_id = intval($input['supervisor_id']);

// المسار الجذري للمجلدات
$root = realpath(dirname(__DIR__) . '../../library');
if ($root === false) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Library root not found']);
    exit;
}

// جلب اسم القسم التابع له المشرف
$stmt = $conn->prepare("SELECT d.name AS department_name, d.id AS department_id 
                        FROM supervisor s 
                        JOIN department d ON s.department_id = d.id 
                        WHERE s.id = ?");
$stmt->bind_param('i', $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Supervisor not found']);
    exit;
}
$row = $result->fetch_assoc();
$department_name = sanitize($row['department_name']);
$department_id = $row['department_id'];
$dept_path = $root . '/' . $department_name;

// إنشاء مجلد القسم
createDir($dept_path);

// جلب التخصصات التابعة لهذا القسم
$majors = $conn->query("SELECT id, name FROM major WHERE department_id = $department_id");
while ($major = $majors->fetch_assoc()) {
    $major_name = sanitize($major['name']);
    $major_id = $major['id'];
    $major_path = "$dept_path/$major_name";
    createDir($major_path);

    // لكل تخصص، جلب المستويات
    $levels = $conn->query("SELECT id, name FROM level");
    while ($level = $levels->fetch_assoc()) {
        $level_name = sanitize($level['name']);
        $level_id = $level['id'];
        $level_path = "$major_path/$level_name";
        createDir($level_path);

        // لكل مستوى، جلب الاترام
        $terms = $conn->query("SELECT id, name FROM term");
        while ($term = $terms->fetch_assoc()) {
            $term_name = sanitize($term['name']);
            $term_id = $term['id'];
            $term_path = "$level_path/$term_name";
            createDir($term_path);

            // جلب المواد المرتبطة بهذا التخصص والمستوى والترم
            $courses = $conn->query("SELECT id, name FROM course 
                WHERE major_id = $major_id AND level_id = $level_id AND term_id = $term_id");
            while ($course = $courses->fetch_assoc()) {
                $course_name = sanitize($course['name']);
                $course_id = $course['id'];
                $course_path = "$term_path/$course_name";
                createDir($course_path);

                // جلب السنوات الدراسية المرتبطة بهذه المادة من جدول الدرجات
                $years = $conn->query("SELECT DISTINCT academic_year FROM grade WHERE course_id = $course_id");
                while ($year = $years->fetch_assoc()) {
                    $year_path = "$course_path/" . sanitize($year['academic_year']);
                    createDir($year_path);
                }
            }
        }
    }
}

echo json_encode(['status'=>'success','message'=>'Folders created successfully'], JSON_UNESCAPED_UNICODE);

// دالة إنشاء المجلد إن لم يكن موجوداً
function createDir($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

// دالة تنقية الأسماء
function sanitize($str) {
    return preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $str);
}
?>
