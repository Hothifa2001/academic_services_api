<?php
header('Content-Type: application/json');
include_once '../config.php';

// 1. قَرْاءة المدخلات من JSON
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

// 2. التحقق من وجود المعرّف
if (!$id) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'حقل id مطلوب للحذف'
    ]);
    exit;
}

// 3. تفعيل رمي الاستثناءات من MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 4. إعداد وتنفيذ بيان الحذف
    $stmt = $conn->prepare("DELETE FROM survey_distribution WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // 5. التحقق من عدد الصفوف المتأثرة
    if ($stmt->affected_rows > 0) {
        echo json_encode([
          'status'  => 'success',
          'message' => 'تم حذف التوزيع بنجاح'
        ]);
    } else {
        // إذا لم يكن هناك صفوف للحذف
        echo json_encode([
          'status'  => 'error',
          'message' => 'لم يتم العثور على التوزيع المطلوب'
        ]);
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode([
      'status'  => 'error',
      'message' => 'خطأ في الخادم: ' . $e->getMessage()
    ]);
}
