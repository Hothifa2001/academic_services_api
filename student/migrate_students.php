<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

// قراءة البيانات من الطلب
$data = json_decode(file_get_contents("php://input"), true);

$from_level = $data['from_level'];
$from_term = $data['from_term'];
$to_level = $data['to_level'];
$to_term = $data['to_term'];
$major_id = $data['major_id']; // ✅ أضفنا التخصص

// تحقق من القيم
if (!$from_level || !$from_term || !$to_level || !$to_term || !$major_id) {
    echo json_encode(["status" => "error", "message" => "جميع الحقول مطلوبة"]);
    exit;
}

// تحديث الطلاب النشطين حسب المستوى والترم والتخصص
$stmt = $conn->prepare("UPDATE student 
    SET level_id = ?, term_id = ? 
    WHERE level_id = ? AND term_id = ? AND major_id = ? AND status = 1");

$stmt->bind_param("iiiii", $to_level, $to_term, $from_level, $from_term, $major_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success", 
        "message" => "تم ترحيل الطلاب بنجاح",
        "affected_rows" => $stmt->affected_rows
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في الترحيل", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
