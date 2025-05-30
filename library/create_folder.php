<?php
// C:\xampp\htdocs\academic_services_api\library\create_folder.php

header('Content-Type: application/json; charset=utf-8');

// 1) قراءة JSON من الطلب
$input = json_decode(file_get_contents('php://input'), true);
if (
    !isset($input['departmentName'], $input['folderName'])
) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'departmentName and folderName are required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) تعقيم اسم القسم واسم المجلد الجديد
$deptName   = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $input['departmentName']);
$folderName = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $input['folderName']);

// 3) قراءة المسار الفرعي إذا وُجد وتحويل الفواصل
$path = '';
if (!empty($input['path'])) {
    // منع ../ و ..\
    $raw   = str_replace(['..\\', '../'], '', $input['path']);
    // السماح بالحروف العربية والإنجليزية والأرقام والفواصل المائلة فقط
    $clean = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u', '', $raw);
    // تحويل '/' و '\' إلى فاصل النظام
    $path  = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($clean, "/\\"));
}

// 4) تحديد مسار المجلد الجذري للمكتبة
$rootLibrary = realpath(dirname(__DIR__, 2) . '/library');
if ($rootLibrary === false) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Library root folder not found'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 5) مسار مجلد القسم الأساسي
$baseDept = $rootLibrary . DIRECTORY_SEPARATOR . $deptName;
if (!is_dir($baseDept) && !mkdir($baseDept, 0777, true)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create department folder'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 6) بناء مسار المجلد الهدف (يشمل المسار الفرعي إن وجد)
$targetDir = $path === ''
    ? $baseDept
    : $baseDept . DIRECTORY_SEPARATOR . $path;

// التحقق من وجود المجلد الهدف
if (!is_dir($targetDir)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Target folder does not exist'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 7) مسار المجلد الجديد داخل المجلد الهدف
$newFolderPath = $targetDir . DIRECTORY_SEPARATOR . $folderName;

// التحقق من عدم وجود المجلد مسبقاً
if (is_dir($newFolderPath)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Folder already exists'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 8) إنشاء المجلد الجديد
if (mkdir($newFolderPath, 0777, true)) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Folder created successfully'
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create folder'
    ], JSON_UNESCAPED_UNICODE);
}
