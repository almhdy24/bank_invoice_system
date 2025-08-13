<?php
$error = Session::getFlash('error');
$success = Session::getFlash('success');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول - نظام تتبع الفواتير</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <div class="login-container">
    <h1>تسجيل الدخول</h1>

    <?php if ($error): ?>
    <div class="alert alert-error">
      <?= $error ?>
    </div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-succes">
      <?= $success ?>
    </div>
    <?php endif; ?>

    <form action="/login" method="POST">
      <div class="form-group">
        <label for="username">اسم المستخدم:</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="form-group">
        <label for="password">كلمة المرور:</label>
        <input type="password" id="password" name="password" required>
      </div>

      <button type="submit" class="btn">تسجيل الدخول</button>
      <p class="text-center">لا تملك حساباً؟ <a href="/register">أنشئ حساب جديد</a></p>
    </form>
  </div>
</body>
</html>