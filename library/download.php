<?php
// C:\xampp\htdocs\academic_services_api\library\download.php

// 1) استعلام المعاملات من GET
if (empty($_GET['departmentName']) || empty($_GET['itemName'])) {
    http_response_code(400);
    exit('Missing parameters');
}

$deptName = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $_GET['departmentName']);
$itemName = preg_replace('/[^A-Za-z0-9_\-ا-ي \.\,]/u', '', $_GET['itemName']);
$path     = '';
if (!empty($_GET['path'])) {
    $raw   = str_replace(['..\\','../'], '', $_GET['path']);
    $clean = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u', '', $raw);
    $path  = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($clean, "/\\"));
}

// 2) تحديد المسار الكامل للمكتبة
$root = realpath(dirname(__DIR__,2) . '/library');
if ($root === false) {
    http_response_code(500);
    exit('Library root not found');
}

// 3) مسار مجلد القسم
$baseDept = $root . DIRECTORY_SEPARATOR . $deptName;
if (!is_dir($baseDept)) {
    http_response_code(404);
    exit('Department not found');
}

// 4) مسار العنصر (ملف أو مجلد)
$target = $baseDept . DIRECTORY_SEPARATOR . ($path === '' ? '' : $path . DIRECTORY_SEPARATOR) . $itemName;
if (!file_exists($target)) {
    http_response_code(404);
    exit('Item not found');
}

// 5) إذا كان ملفًا، أرسله مباشرة
if (is_file($target)) {
    $mime = mime_content_type($target) ?: 'application/octet-stream';
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . basename($target) . '"');
    header('Content-Length: ' . filesize($target));
    readfile($target);
    exit;
}

// 6) إذا كان مجلدًا، ضمّنه في ZIP وأرسله
$zipName = $itemName . '.zip';
$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('libzip_') . '.zip';

$zip = new ZipArchive();
if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
    http_response_code(500);
    exit('Failed to create zip');
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($files as $file) {
    $filePath     = $file->getPathname();
    // مصار نسبي داخل الأرشيف
    $relativePath = substr($filePath, strlen($target) + 1);
    if ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
    } else {
        $zip->addFile($filePath, $relativePath);
    }
}
$zip->close();

// 7) إرسال الـ ZIP
header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($tmpZip));
readfile($tmpZip);

// 8) حذف ZIP المؤقت
unlink($tmpZip);
exit;
