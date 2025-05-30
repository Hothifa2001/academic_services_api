<?php
header('Content-Type: application/json');
include_once '../config.php';

// 1. التحقق من وجود المعرف
if (!isset($_GET['distribution_id'])) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'حقل distribution_id مطلوب'
    ]);
    exit;
}

$distribution_id = intval($_GET['distribution_id']);

try {
    // 2. عدد المشاركين
    $stmt = $conn->prepare("
      SELECT COUNT(*) AS cnt
      FROM survey_participant
      WHERE distribution_id = ?
    ");
    $stmt->bind_param('i', $distribution_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $total_participants = intval($row['cnt']);

    // 3. جلب survey_id للمضي قدماً لجلب الأسئلة
    $stmt = $conn->prepare("
      SELECT survey_id
      FROM survey_distribution
      WHERE id = ?
    ");
    $stmt->bind_param('i', $distribution_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $survey_id = intval($row['survey_id']);

    // 4. جلب الأسئلة وإحصاء كل خيار لكل سؤال (محدّث)
$stmt = $conn->prepare("
  SELECT id, question
  FROM survey_question
  WHERE survey_id = ?
  ORDER BY id
");
$stmt->bind_param('i', $survey_id);
$stmt->execute();
$questions_result = $stmt->get_result();

$questions = [];
while ($q = $questions_result->fetch_assoc()) {
    $question_id   = intval($q['id']);
    $question_text = $q['question'];

    // هنا نأخذ كل خيارات الاستبيان (حتى التي لم يختارها أحد)
    $stmt2 = $conn->prepare("
      SELECT
        sc.choice_value,
        sc.choice_text,
        COALESCE(cnt.cnt, 0) AS cnt
      FROM survey_choice sc
      LEFT JOIN (
        SELECT choice_id, COUNT(*) AS cnt
        FROM survey_answer
        WHERE distribution_id = ?
          AND question_id = ?
        GROUP BY choice_id
      ) AS cnt ON sc.id = cnt.choice_id
      WHERE sc.survey_id = ?
      ORDER BY sc.choice_value DESC
    ");
    $stmt2->bind_param('iii', $distribution_id, $question_id, $survey_id);
    $stmt2->execute();
    $choices_result = $stmt2->get_result();

    $choices = [];
    while ($c = $choices_result->fetch_assoc()) {
        $choices[] = [
            'choice_value' => intval($c['choice_value']),
            'choice_text'  => $c['choice_text'],
            'count'        => intval($c['cnt']),
        ];
    }
    $stmt2->close();

    $questions[] = [
        'question_id'   => $question_id,
        'question_text' => $question_text,
        'choices'       => $choices,
    ];
}
$stmt->close();


    // 5. إحصاء ملخص عبر كل الأسئلة (محدّث)
$stmt = $conn->prepare("
  SELECT
    sc.choice_value,
    sc.choice_text,
    COALESCE(ans.total_count, 0) AS total_count
  FROM survey_choice sc
  LEFT JOIN (
    SELECT choice_id, COUNT(*) AS total_count
    FROM survey_answer
    WHERE distribution_id = ?
    GROUP BY choice_id
  ) AS ans ON sc.id = ans.choice_id
  WHERE sc.survey_id = ?
  ORDER BY sc.choice_value DESC
");
$stmt->bind_param('ii', $distribution_id, $survey_id);
$stmt->execute();
$summary_result = $stmt->get_result();

$summary = [];
while ($s = $summary_result->fetch_assoc()) {
    $summary[] = [
        'choice_value' => intval($s['choice_value']),
        'choice_text'  => $s['choice_text'],
        'total_count'  => intval($s['total_count']),
    ];
}
$stmt->close();


    // 6. الإخراج النهائي
    echo json_encode([
        'status'             => 'success',
        'total_participants' => $total_participants,
        'questions'          => $questions,
        'summary'            => $summary,
    ]);

} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'خطأ في الخادم: ' . $e->getMessage()
    ]);
}
