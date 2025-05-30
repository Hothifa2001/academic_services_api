<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/octet-stream");

// 1. استلام المعاملات
$itemName = $_GET['itemName'] ?? '';
$rawPath = $_GET['path'] ?? '';

// 2. تنظيف المدخلات
$cleanItem = preg_replace('/[^A-Za-z0-9_\-ا-ي \.\,]/u', '', $itemName);
$cleanPath = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u', '', $rawPath);

// 3. تحديد المسار الأساسي
$baseDir = realpath("../../library");
if (!$baseDir) {
    http_response_code(500);
    exit('المسار الأساسي غير موجود');
}

// 4. بناء المسار الكامل
$fullPath = $baseDir . DIRECTORY_SEPARATOR . $cleanPath . DIRECTORY_SEPARATOR . $cleanItem;
if (!file_exists($fullPath)) {
    http_response_code(404);
    exit('العنصر غير موجود');
}

// 5. إذا كان ملفاً
if (is_file($fullPath)) {
    $fileName = basename($fullPath);
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . filesize($fullPath));
    readfile($fullPath);
    exit;
}

// 6. إذا كان مجلداً (ننشئ ملف ZIP)
if (is_dir($fullPath)) {
    $zipName = $cleanItem . '.zip';
    $tempZip = tempnam(sys_get_temp_dir(), 'zip_');
    
    // إنشاء أرشيف ZIP
    $zip = new ZipArchive();
    if ($zip->open($tempZip, ZipArchive::CREATE) !== TRUE) {
        http_response_code(500);
        exit('لا يمكن إنشاء ملف ZIP');
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($fullPath) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    $zip->close();

    // إرسال الأرشيف
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($tempZip));
    readfile($tempZip);
    
    // حذف الملف المؤقت
    unlink($tempZip);
    exit;
}

// 7. إذا لم يكن ملفاً ولا مجلداً
http_response_code(400);
exit('نوع غير معروف');