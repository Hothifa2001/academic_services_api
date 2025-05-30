<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$input    = json_decode(file_get_contents('php://input'), true);
$surveyId = $input['survey_id'] ?? null;
if (!$surveyId) {
    echo json_encode(['status'=>'error','message'=>'survey_id مطلوب']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM survey_choice WHERE survey_id = ?");
$stmt->bind_param('i', $surveyId);
if ($stmt->execute()) {
    echo json_encode(['status'=>'success']);
} else {
    echo json_encode(['status'=>'error','message'=>'فشل في حذف الخيارات']);
}
