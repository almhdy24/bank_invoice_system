<?php
$error = Session::getFlash('error');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>رفع فاتورة جديدة - نظام تتبع الفواتير</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <header>
    <h1>رفع فاتورة جديدة</h1>
    <nav>
      <a href="/dashboard">الفواتير</a>
      <a href="/invoice/create">رفع فاتورة جديدة</a>
      <a href="/logout">تسجيل الخروج</a>
    </nav>
  </header>

  <main>
    <?php if ($error): ?>
    <div class="alert alert-error">
      <?= $error ?>
    </div>
    <?php endif; ?>

    <form action="/invoice/create" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="amount">المبلغ:</label>
        <input type="number" step="0.01" id="amount" name="amount" required>
      </div>

      <div class="form-group">
        <label for="transfer_date">تاريخ التحويل:</label>
        <input type="date" id="transfer_date" name="transfer_date" required>
      </div>

      <div class="form-group">
        <label for="invoice_image">صورة الفاتورة:</label>
        <input type="file" id="invoice_image" name="invoice_image" accept="image/*" required>
        <small>يجب أن تكون الصورة بصيغة JPG, PNG أو GIF</small>
      </div>

      <button type="submit" class="btn">رفع الفاتورة</button>
      <a href="/dashboard" class="btn btn-secondary">إلغاء</a>
    </form>
  </main>
</body>
</html>