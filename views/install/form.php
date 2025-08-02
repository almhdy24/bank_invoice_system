<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت النظام</title>
    <link rel="stylesheet" href="/css/install.css">
</head>
<body>
    <div class="install-container">
        <h1>تثبيت نظام إدارة الفواتير</h1>
        
<?php 
$flash = Session::get('flash');
if ($flash): ?>
    <div class="alert <?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php Session::delete('flash'); ?>
<?php endif; ?>
        
        <div class="requirements">
            <h2>متطلبات النظام</h2>
            
            <div class="requirement-list">
                <?php foreach ($requirements as $key => $req): ?>
                    <?php if ($key !== 'all_passed'): ?>
                        <div class="requirement <?= $req['passed'] ? 'passed' : 'failed' ?>">
                            <span class="status-icon"><?= $req['passed'] ? '✓' : '✗' ?></span>
                            <span class="requirement-name"><?= $req['name'] ?></span>
                            <span class="requirement-status"><?= $req['passed'] ? 'جاهز' : 'غير متوفر' ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if ($requirements['all_passed']): ?>
            <div class="install-form">
                <h2>إعدادات المدير</h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="form-group">
                        <label for="admin_username">اسم المستخدم:</label>
                        <input type="text" id="admin_username" name="admin_username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_email">البريد الإلكتروني:</label>
                        <input type="email" id="admin_email" name="admin_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_password">كلمة المرور:</label>
                        <input type="password" id="admin_password" name="admin_password" required minlength="8">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">تأكيد كلمة المرور:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn-install">تثبيت النظام</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert warning">
                لا يمكن متابعة التثبيت - يرجى حل المشكلات المذكورة أعلاه أولاً
            </div>
        <?php endif; ?>
    </div>
</body>
</html>