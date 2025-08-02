<?php
$success = Session::getFlash('success');
$error = Session::getFlash('error');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم - الأدمن</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <header>
    <h1>لوحة التحكم - الأدمن</h1>
    <nav>
      <a href="/admin/dashboard">الفواتير</a>
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

    <h2>جميع فواتير التحويل البنكي</h2>

    <form method="GET" class="filter-form">
      <div class="form-group">
        <label for="status">فلترة حسب الحالة:</label>
        <select id="status" name="status" onchange="this.form.submit()">
          <option value="">الكل</option>
          <?php foreach ($statuses as $status): ?>
          <option value="<?= $status ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>>
            <?= $status ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </form>

    <?php if (empty($invoices)): ?>
    <p>
      لا توجد فواتير مسجلة
    </p>
    <?php else : ?>
    <table>
      <thead>
        <tr>
          <th>رقم الفاتورة</th>
          <th>المستخدم</th>
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
          <td><?= $invoice['username'] ?></td>
          <td><?= $invoice['amount'] ?></td>
          <td><?= $invoice['transfer_date'] ?></td>
          <td><?= $invoice['status'] ?></td>
          <td><?= $invoice['created_at'] ?></td>
          <td>
            <a href="/admin/invoice/<?= $invoice['id'] ?>" class="btn btn-small">عرض</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </main>
</body>
</html>