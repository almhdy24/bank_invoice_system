<?php
$success = Session::getFlash('success');
$error = Session::getFlash('error');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>فاتورة #<?= $invoice['id'] ?> - نظام تتبع الفواتير</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <header>
    <h1>فاتورة #<?= $invoice['id'] ?></h1>
    <nav>
      <a href="/dashboard">الفواتير</a>
      <a href="/invoice/create">رفع فاتورة جديدة</a>
      <a href="/logout">تسجيل الخروج</a>
    </nav>
  </header>

  <main>
    <?php if ($success): ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error">
      <?= $error ?>
    </div>
    <?php endif; ?>

    <div class="invoice-details">
      <div class="detail-row">
        <span class="label">رقم الفاتورة:</span>
        <span class="value"><?= $invoice['id'] ?></span>
      </div>

      <div class="detail-row">
        <span class="label">المستخدم:</span>
        <span class="value"><?= $invoice['username'] ?></span>
      </div>

      <div class="detail-row">
        <span class="label">المبلغ:</span>
        <span class="value"><?= $invoice['amount'] ?></span>
      </div>

      <div class="detail-row">
        <span class="label">تاريخ التحويل:</span>
        <span class="value"><?= $invoice['transfer_date'] ?></span>
      </div>

      <div class="detail-row">
        <span class="label">الحالة:</span>
        <span class="value"><?= $invoice['status'] ?></span>
      </div>

      <?php if (!empty($invoice['notes'])): ?>
      <div class="detail-row">
        <span class="label">ملاحظات:</span>
        <span class="value"><?= htmlspecialchars($invoice['notes']) ?></span>
      </div>
      <?php endif; ?>

      <div class="detail-row">
        <span class="label">تاريخ الإدخال:</span>
        <span class="value"><?= $invoice['created_at'] ?></span>
      </div>

      <div class="detail-row">
        <span class="label">صورة الفاتورة:</span>
        <div class="value">
          <?php if (!empty($invoice['image_path'])): ?>
            <img src="/uploads/<?= htmlspecialchars($invoice['image_path']) ?>" alt="صورة الفاتورة" class="invoice-image">
          <?php else: ?>
            <span>لا توجد صورة</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if (isset($isAdminView) && $isAdminView): ?>
    <form action="/admin/invoice/<?= $invoice['id'] ?>/update-status" method="POST">
      <div class="form-group">
        <label for="status">تغيير الحالة:</label>
        <select id="status" name="status" required>
          <option value="<?= Config::INVOICE_STATUS_PENDING ?>" <?= $invoice['status'] === Config::INVOICE_STATUS_PENDING ? 'selected' : '' ?>>
            <?= Config::INVOICE_STATUS_PENDING ?>
          </option>
          <option value="<?= Config::INVOICE_STATUS_APPROVED ?>" <?= $invoice['status'] === Config::INVOICE_STATUS_APPROVED ? 'selected' : '' ?>>
            <?= Config::INVOICE_STATUS_APPROVED ?>
          </option>
          <option value="<?= Config::INVOICE_STATUS_REJECTED ?>" <?= $invoice['status'] === Config::INVOICE_STATUS_REJECTED ? 'selected' : '' ?>>
            <?= Config::INVOICE_STATUS_REJECTED ?>
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="notes">ملاحظات:</label>
        <textarea id="notes" name="notes"><?= isset($invoice['notes']) ? htmlspecialchars($invoice['notes']) : '' ?></textarea>
      </div>

      <button type="submit" class="btn">حفظ التغييرات</button>
    </form>
    <?php endif; ?>

    <a href="/dashboard" class="btn btn-secondary">العودة للقائمة</a>
  </main>
</body>
</html>