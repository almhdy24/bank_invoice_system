<?php
$success = Session::getFlash('success');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم - نظام تتبع الفواتير</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <header>
    <h1>لوحة التحكم</h1>
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

    <h2>فواتير التحويل البنكي</h2>

    <a href="/invoice/create" class="btn">رفع فاتورة جديدة</a>

    <?php if (empty($invoices)): ?>
    <p>
      لا توجد فواتير مسجلة
    </p>
    <?php else : ?>
    <table>
      <thead>
        <tr>
          <th>رقم الفاتورة</th>
          <th>المبلغ</th>
          <th>تاريخ التحويل</th>
          <th>الحالة</th>
          <th>التاريخ</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoices as $invoice): ?>
        <tr>
          <td><?= $invoice['id'] ?></td>
          <td><?= $invoice['amount'] ?></td>
          <td><?= $invoice['transfer_date'] ?></td>
          <td><?= $invoice['status'] ?></td>
          <td><?= $invoice['created_at'] ?></td>
          <td>
            <a href="/invoice/<?= $invoice['id'] ?>" class="btn btn-small">عرض</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </main>
</body>
</html>