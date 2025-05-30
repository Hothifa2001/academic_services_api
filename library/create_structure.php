<?php
// C:\xampp\htdocs\academic_services_api\library\create_structure.php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'../config.php'; // تأكد من وجود ملف الاتصال بقاعدة البيانات

// 1) التحقق من وجود departmentName
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['departmentName'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'departmentName is required']);
    exit;
}

// 2) تنظيف اسم القسم
$deptName = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $input['departmentName']);

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3) الحصول على department_id من اسم القسم
    $stmt = $pdo->prepare("SELECT id FROM department WHERE name = ?");
    $stmt->execute([$deptName]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$department) {
        throw new Exception("القسم غير موجود في قاعدة البيانات");
    }
    $department_id = $department['id'];

    // 4) جلب جميع التخصصات التابعة للقسم
    $majors = $pdo->prepare("
        SELECT id, name 
        FROM major 
        WHERE department_id = ?
    ");
    $majors->execute([$department_id]);

    // 5) الدليل الجذري للمكتبة
    $root = realpath(dirname(__DIR__,2).'/library');
    if (!is_dir($root)) {
        mkdir($root, 0755, true);
    }

    foreach ($majors as $major) {
        // 6) إنشاء مجلد التخصص
        $majorPath = $root . '/' . sanitize($major['name']);
        if (!is_dir($majorPath)) {
            mkdir($majorPath, 0755);
        }

        // 7) جلب المستويات والفصول والمواد
        $courses = $pdo->prepare("
            SELECT c.id, c.name, l.name as level, t.name as term 
            FROM course c
            JOIN level l ON c.level_id = l.id
            JOIN term t ON c.term_id = t.id
            WHERE c.major_id = ?
        ");
        $courses->execute([$major['id']]);

        foreach ($courses as $course) {
            // 8) إنشاء مسار المستوى/الفصل/المادة
            $coursePath = sprintf(
                "%s/%s/%s/%s/%s",
                $majorPath,
                sanitize($course['level']),
                sanitize($course['term']),
                sanitize($course['name'])
            );

            if (!is_dir($coursePath)) {
                mkdir($coursePath, 0755, true);
            }

            // 9) جلب السنوات الدراسية للمادة
            $years = $pdo->prepare("
                SELECT DISTINCT academic_year 
                FROM course_instructor 
                WHERE course_id = ?
            ");
            $years->execute([$course['id']]);

            foreach ($years as $year) {
                // 10) إنشاء مجلد السنة الدراسية
                $yearPath = $coursePath . '/' . sanitize($year['academic_year']);
                if (!is_dir($yearPath)) {
                    mkdir($yearPath, 0755);
                }
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'تم إنشاء الهيكل بنجاح'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// دالة لتنظيف أسماء المجلدات
function sanitize($name) {
    return preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $name);
}