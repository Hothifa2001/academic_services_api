<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$surveyId = $_GET['survey_id'] ?? null;
if (!$surveyId) {
  echo json_encode(['status' => 'error', 'message' => 'survey_id مفقود']);
  exit;
}

// جلب الأسئلة المرتبطة بالاستبيان
$questionsResult = $conn->query("SELECT id, question FROM survey_question WHERE survey_id = $surveyId");
$stats = [];

while ($q = $questionsResult->fetch_assoc()) {
  $questionId = $q['id'];
  $questionText = $q['question'];

  // جلب عدد الإجابات لكل اختيار لهذا السؤال
  $choicesResult = $conn->query("
    SELECT sc.id AS choice_id, sc.choice_text, COUNT(sa.id) AS count
    FROM survey_choice sc
    LEFT JOIN survey_answer sa ON sa.choice_id = sc.id AND sa.question_id = $questionId
    WHERE sc.survey_id = $surveyId
    GROUP BY sc.id
  ");

  $total = 0;
  $choices = [];

  while ($c = $choicesResult->fetch_assoc()) {
    $total += $c['count'];
    $choices[] = [
      'choice_id' => (int)$c['choice_id'],
      'choice_text' => $c['choice_text'],
      'count' => (int)$c['count']
    ];
  }

  // احسب النسبة المئوية
  foreach ($choices as &$ch) {
    $ch['percent'] = $total > 0 ? round(($ch['count'] / $total) * 100, 1) : 0;
  }

  $stats[] = [
    'question_id' => (int)$questionId,
    'question' => $questionText,
    'total_answers' => $total,
    'choices' => $choices
  ];
}

echo json_encode(['status' => 'success', 'data' => $stats], JSON_UNESCAPED_UNICODE);
?>
