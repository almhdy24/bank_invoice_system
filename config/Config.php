<?php
class Config {
  // حالة التثبيت مع تخزين مؤقت
  private static $installed = null;

  // مسار قاعدة البيانات مع التحقق من الصلاحيات
  const DB_PATH = __DIR__ . '/../db/database.sqlite';

  // إعدادات البريد الإلكتروني
  const MAIL_FROM = 'no-reply@bank-invoice-system.com';
  const MAIL_FROM_NAME = 'نظام تتبع الفواتير';

  // إعدادات التطبيق
  const APP_NAME = 'نظام تتبع فواتير التحويل البنكي';
  const SESSION_TIMEOUT = 3600; // ثانية (ساعة واحدة)

  // حالات الفاتورة
  const INVOICE_STATUS_PENDING = 'معلقة';
  const INVOICE_STATUS_APPROVED = 'مقبولة';
  const INVOICE_STATUS_REJECTED = 'مرفوضة';

  // أدوار المستخدمين
  const ROLE_USER = 'user';
  const ROLE_ADMIN = 'admin';

  // التحقق من حالة التثبيت مع تحسينات الأمان
  public static function isInstalled() {
    if (self::$installed === null) {
      $installFile = __DIR__ . '/../.installed';
      self::$installed = file_exists($installFile) && is_readable($installFile);

      // التحقق من محتوى الملف إذا لزم الأمر
      if (self::$installed && filesize($installFile) === 0) {
        self::$installed = false;
      }
    }
    return self::$installed;
  }

  // إنشاء ملف التثبيت مع التحقق من الصلاحيات
  public static function markAsInstalled() {
    $installFile = __DIR__ . '/../.installed';

    // التحقق من إمكانية الكتابة في المجلد
    if (!is_writable(dirname($installFile))) {
      throw new RuntimeException('لا يوجد صلاحيات كتابة في مجلد التطبيق');
    }

    // كتابة تاريخ التثبيت
    $contents = "System Installed: " . date('Y-m-d H:i:s') . "\n";
    $contents .= "Version: 1.0.0\n";

    if (file_put_contents($installFile, $contents, LOCK_EX) === false) {
      throw new RuntimeException('فشل في إنشاء ملف التثبيت');
    }

    self::$installed = true;
  }

  // حذف ملف التثبيت (لأغراض التطوير فقط)
  public static function markAsUninstalled() {
    $installFile = __DIR__ . '/../.installed';
    if (file_exists($installFile)) {
      unlink($installFile);
    }
    self::$installed = false;
  }

  // التحقق من إعدادات الخادم
  public static function checkServerRequirements() {
    $requirements = [
      'php_version' => version_compare(PHP_VERSION, '7.1.0', '>='),
      'pdo_sqlite' => extension_loaded('pdo_sqlite'),
      'gd' => extension_loaded('gd'),
      'mbstring' => extension_loaded('mbstring'),
      'uploads_dir' => is_writable(__DIR__ . '/../uploads'),
      'db_dir' => is_writable(dirname(self::DB_PATH))
    ];

    return $requirements;
  }

  // منع إنشاء كائن من الفئة
  private function __construct() {}

  // منع الاستنساخ
  private function __clone() {}

  // منع إعادة التهيئة
  public function __wakeup() {
    throw new Exception("Cannot unserialize singleton");
  }
}