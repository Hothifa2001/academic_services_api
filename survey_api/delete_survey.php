<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$id    = $input['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'حقل id مطلوب']);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $stmt = $conn->prepare("DELETE FROM survey WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status'=>'success','message'=>'تم حذف الاستبيان بنجاح']);
    } else {
        echo json_encode(['status'=>'error','message'=>'لم يتم العثور على الاستبيان المطلوب']);
    }
} catch (mysqli_sql_exception $e) {
    // كود 1451 يعني فشل بسبب أقفال المفاتيح الأجنبية
    if ($e->getCode() === 1451) {
        http_response_code(400);
        echo json_encode([
          'status'  => 'error',
          'message' => 'لا يمكن حذف الاستبيان لأنّ هناك توزيعات مرتبطة به'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
          'status'  => 'error',
          'message' => 'خطأ في الخادم'
        ]);
    }
}
