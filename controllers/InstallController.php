<?php
class InstallController
{
  private $request;

  public function __construct(Request $request) {
    $this->request = $request;

    if (Config::isInstalled() && strpos($this->request->uri(), '/install') === false) {
      Response::redirect('/');
    }
  }

  public function showInstallForm() {
    if (Config::isInstalled()) {
      Response::redirect('/');
    }

    $requirements = $this->checkRequirements();

    Response::view('install/form', [
      'requirements' => $requirements
    ]);
  }

  public function processInstall() {
    try {
        $postData = $this->request->getBody();

        $requirements = $this->checkRequirements();
        if (!$requirements['all_passed']) {
            throw new Exception('بعض متطلبات النظام غير متوفرة. يرجى تصحيحها قبل متابعة التثبيت.');
        }

        $this->validateInput($postData);

        $this->runInstallation($postData);

        Config::markAsInstalled();

        Session::set('flash', [
            'type' => 'success',
            'message' => 'تم تثبيت النظام بنجاح! يمكنك الآن تسجيل الدخول.'
        ]);

        Response::redirect('/login');
    } catch (Exception $e) {
        Session::set('flash', [
            'type' => 'error',
            'message' => 'خطأ: ' . $e->getMessage()
        ]);

        Response::redirect('/install');
    }
  }

  private function checkRequirements() {
    $requirements = [
      'php_version' => [
        'name' => 'نسخة PHP 7.1 أو أعلى',
        'passed' => version_compare(PHP_VERSION, '7.1.0', '>=')
      ],
      'pdo_sqlite' => [
        'name' => 'إضافة PDO SQLite',
        'passed' => extension_loaded('pdo_sqlite')
      ],
      'uploads_dir' => [
        'name' => 'صلاحيات مجلد الرفع',
        'passed' => $this->checkDirectoryPermissions('/uploads')
      ],
      'db_dir' => [
        'name' => 'صلاحيات مجلد قاعدة البيانات',
        'passed' => $this->checkDirectoryPermissions('db')
      ]
    ];

    $requirements['all_passed'] = !in_array(false, array_column($requirements, 'passed'));

    return $requirements;
  }

  private function checkDirectoryPermissions($path) {
    $fullPath = __DIR__ . '/../../' . $path;

    if (!file_exists($fullPath)) {
      @mkdir($fullPath, 0755, true);
    }

    return is_writable($fullPath);
  }

  private function validateInput($data) {
    $required = ['admin_username', 'admin_email', 'admin_password', 'confirm_password'];
    foreach ($required as $field) {
      if (empty($data[$field])) {
        throw new Exception('جميع الحقول مطلوبة ولا يمكن تركها فارغة.');
      }
    }

    if (!filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
      throw new Exception('البريد الإلكتروني غير صالح. يرجى إدخال بريد إلكتروني صحيح.');
    }

    if (strlen($data['admin_password']) < 8) {
      throw new Exception('كلمة المرور يجب أن تتكون من 8 أحرف على الأقل.');
    }

    if ($data['admin_password'] !== $data['confirm_password']) {
      throw new Exception('تأكيد كلمة المرور غير متطابق مع كلمة المرور.');
    }
  }

  private function runInstallation($data) {
    $this->setupDatabase();

    $this->createAdminUser(
      $data['admin_username'],
      $data['admin_email'],
      $data['admin_password']
    );
  }

  private function setupDatabase() {
    $db = Database::getInstance()->getConnection();

    $db->exec('CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT "user",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )');

    $db->exec('CREATE TABLE IF NOT EXISTS invoices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            amount REAL NOT NULL,
            transfer_date TEXT NOT NULL,
            image_path TEXT,
            notes TEXT,
            status TEXT NOT NULL DEFAULT "pending",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )');
  }

  private function createAdminUser($username, $email, $password) {
    $user = new User();
    $user->create($username, $password, $email, 'admin');
  }
}