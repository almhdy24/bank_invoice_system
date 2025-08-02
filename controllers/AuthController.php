<?php
class AuthController {
  private $request;
  private $userModel;

  public function __construct(Request $request) {
    $this->request = $request;
    $this->userModel = new User();
  }

  public function login() {
    if ($this->request->isAuthenticated()) {
      Response::redirect('/dashboard');
    }

    if ($this->request->getMethod() === 'POST') {
      $body = $this->request->getBody();
      $username = trim($body['username'] ?? '');
      $password = $body['password'] ?? '';

      $user = $this->userModel->verifyCredentials($username, $password);

      if ($user) {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('username', $user['username']);

        $redirect = $user['role'] === Config::ROLE_ADMIN ? '/admin/dashboard' : '/dashboard';
        Response::redirect($redirect);
      } else {
        Session::setFlash('error', 'اسم المستخدم أو كلمة المرور غير صحيحة');
        Response::redirect('/login');
      }
    }

    Response::view('auth/login', [
      'error' => Session::getFlash('error'),
      'success' => Session::getFlash('success')
    ]);
  }

  public function showRegister() {
    Response::view('auth/register', [
      'error' => Session::getFlash('error'),
      'success' => Session::getFlash('success')
    ]);
  }

  public function register() {
    if ($this->request->getMethod() === 'POST') {
      $body = $this->request->getBody();

      try {
        // التحقق من صحة الإدخال
        if (empty($body['username']) || empty($body['email']) || empty($body['password']) || empty($body['confirm_password'])) {
          throw new Exception('جميع الحقول مطلوبة');
        }
        if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
          throw new Exception('البريد الإلكتروني غير صالح');
        }
        if ($body['password'] !== $body['confirm_password']) {
          throw new Exception('كلمتا المرور غير متطابقتين');
        }

        $this->userModel->register(
          $body['username'],
          $body['password'],
          $body['email']
        );

        Session::setFlash('success', 'تم تسجيل الحساب بنجاح! يمكنك تسجيل الدخول الآن');
        Response::redirect('/login');

      } catch (Exception $e) {
        Session::setFlash('error', $e->getMessage());
        Response::redirect('/register');
      }
    }
  }

  public function logout() {
    Session::destroy();
    Response::redirect('/login');
  }
}