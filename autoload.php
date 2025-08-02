<?php
// autoload.php

spl_autoload_register(function ($class) {
    // قائمة بمجلدات البحث عن الفئات
    $directories = [
        'models/',
        'controllers/',
        'core/',
        'services/'
    ];
    
    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // لأغراض التنقيح
    error_log("Class not found: {$class}");
});