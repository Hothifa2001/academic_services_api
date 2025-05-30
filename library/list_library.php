<?php
// C:\xampp\htdocs\academic_services_api\library\list_library.php

header('Content-Type: application/json; charset=utf-8');

// 1) قراءة JSON
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['departmentName'])) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'departmentName is required']);
    exit;
}
$deptName = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $input['departmentName']);

// 2) قراءة المسار الفرعي وتحويل الفواصل
$path = '';
if (!empty($input['path'])) {
    // منع ../
    $raw = str_replace(['..\\','../'], '', $input['path']);
    // السماح بالحروف والأرقام والفاصلة المائلة فقط
    $clean = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u', '', $raw);
    // تحويل '/' و '\' إلى فاصل النظام
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($clean, "/\\"));
}

// 3) الدليل الجذري للمكتبة
$root = realpath(dirname(__DIR__,2).'/library');
if ($root === false) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Library root not found']);
    exit;
}

// 4) دليل القسم (الأساسي)
$baseDept = $root . '/'. $deptName;       // استخدم '/' دائماً
$target   = $path === ''
    ? $baseDept
    : $baseDept . '/'. $path;            // ولا تستخدم DIRECTORY_SEPARATOR هنا

// تحقق أنّ $target موجود:
if (!is_dir($target)) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Target folder does not exist']);
    exit;
}

// 6) جلب المحتويات
$items = [];
foreach (scandir($target) as $entry) {
    if ($entry === '.' || $entry === '..') continue;
    $full = $target . DIRECTORY_SEPARATOR . $entry;
    $isDir = is_dir($full);
    $items[] = [
        'name' => $entry,
        'type' => $isDir ? 'dir' : 'file',
        'size' => $isDir ? null : filesize($full),
    ];
}

// 7) إعادة النتيجة
echo json_encode(['status'=>'success','data'=>$items], JSON_UNESCAPED_UNICODE);
