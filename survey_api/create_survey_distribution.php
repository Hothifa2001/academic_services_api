<?php
header('Content-Type: application/json');
include_once '../config.php';

// نقرأ الجسم كاملاً كسلسلة ثم نحوّله إلى مصفوفة
$input = json_decode(file_get_contents('php://input'), true);

$survey_id     = $input['survey_id']    ?? null;
$course_id     = $input['course_id']    ?? null;
$academic_year = $input['academic_year'] ?? null;

if (!$survey_id || !$course_id || !$academic_year) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'جميع الحقول (survey_id, course_id, academic_year) مطلوبة'
    ]);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $stmt = $conn->prepare("
      INSERT INTO survey_distribution (survey_id, course_id, academic_year)
      VALUES (?, ?, ?)
    ");
    $stmt->bind_param('iis', $survey_id, $course_id, $academic_year);
    $stmt->execute();

    echo json_encode([
      'status'  => 'success',
      'message' => 'تم إضافة التوزيع بنجاح'
    ]);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        echo json_encode([
          'status'  => 'error',
          'message' => 'هذا التوزيع موجود مسبقًا'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
          'status'  => 'error',
          'message' => 'خطأ في الخادم: ' . $e->getMessage()
        ]);
    }
}
