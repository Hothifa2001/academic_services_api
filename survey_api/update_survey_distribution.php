<?php
header('Content-Type: application/json');
include_once '../config.php';

// نقرأ الجسم كاملاً كسلسلة ثم نحوّله إلى مصفوفة
$input = json_decode(file_get_contents('php://input'), true);

$id            = $input['id']            ?? null;
$survey_id     = $input['survey_id']     ?? null;
$course_id     = $input['course_id']     ?? null;
$academic_year = $input['academic_year'] ?? null;

// تحقق من وجود القيم
if (!$id || !$survey_id || !$course_id || !$academic_year) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'جميع الحقول (id, survey_id, course_id, academic_year) مطلوبة'
    ]);
    exit;
}

// إعداد رمي الاستثناءات من MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // جهّز و نفّذ بيان التحديث
    $stmt = $conn->prepare("
      UPDATE survey_distribution
      SET survey_id = ?, course_id = ?, academic_year = ?
      WHERE id = ?
    ");
    $stmt->bind_param('iisi', $survey_id, $course_id, $academic_year, $id);
    $stmt->execute();

    // تم التحديث بنجاح
    echo json_encode([
      'status'  => 'success',
      'message' => 'تم تعديل التوزيع بنجاح'
    ]);
} catch (mysqli_sql_exception $e) {
    // إذا كان خطأ تكرار مفتاح فريد
    if ($e->getCode() === 1062) {
        echo json_encode([
          'status'  => 'error',
          'message' => 'توزيع مماثل موجود مسبقًا'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
          'status'  => 'error',
          'message' => 'خطأ في الخادم: ' . $e->getMessage()
        ]);
    }
}
