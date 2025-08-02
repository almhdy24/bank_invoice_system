<?php
class InvoiceController {
  private $request;
  private $invoiceModel;

  public function __construct(Request $request) {
    $this->request = $request;
    $this->invoiceModel = new Invoice();

    if (!$this->request->isAuthenticated()) {
      Response::redirect('/login');
    }
  }

  public function dashboard() {
  $invoices = $this->request->isAdmin()
    ? $this->invoiceModel->getAllInvoices()
    : $this->invoiceModel->findByUserId($this->request->getUserId());
  Response::view('invoice/dashboard', [
    'invoices' => $invoices,
    'isAdmin' => $this->request->isAdmin()
  ]);
}


  public function create() {
    // إذا كان الطلب من نوع POST (عند إرسال النموذج)
    if ($this->request->getMethod() === 'POST') {
      $body = $this->request->getBody();
      $file = $this->request->getFile('invoice_image');

      // التحقق من صحة المبلغ
      $amount = filter_var($body['amount'], FILTER_VALIDATE_FLOAT);
      $transferDate = $body['transfer_date'] ?? null;

      // تحقق من أن المبلغ صحيح
      if (!$amount || $amount <= 0) {
        // تعليق: المبلغ غير صحيح
        Session::setFlash('error', 'خطأ: المبلغ المدخل غير صالح. يجب إدخال رقم أكبر من الصفر.');
        Response::redirect('/invoice/create');
        return;
      }

      // تحقق من أن تاريخ التحويل موجود
      if (!$transferDate) {
        // تعليق: تاريخ التحويل مفقود
        Session::setFlash('error', 'خطأ: يرجى إدخال تاريخ التحويل.');
        Response::redirect('/invoice/create');
        return;
      }

      // تحقق من رفع الصورة
      if (!$file) {
        // تعليق: لم يتم اختيار صورة
        Session::setFlash('error', 'خطأ: يرجى اختيار صورة للفاتورة.');
        Response::redirect('/invoice/create');
        return;
      }

      // تحقق من وجود خطأ أثناء رفع الملف
      if ($file['error'] !== UPLOAD_ERR_OK) {
        // تعليق: خطأ في رفع الصورة
        Session::setFlash('error', 'خطأ أثناء رفع الصورة: رمز الخطأ ' . $file['error']);
        Response::redirect('/invoice/create');
        return;
      }

      // تحقق من نوع الصورة
      $allowedTypes = ['image/jpeg',
        'image/png',
        'image/gif'];
      $fileType = mime_content_type($file['tmp_name']);
      if (!in_array($fileType, $allowedTypes)) {
        // تعليق: نوع الملف غير مدعوم
        Session::setFlash('error', 'خطأ: نوع الصورة غير مدعوم. يرجى رفع صورة بصيغة JPG أو PNG أو GIF.');
        Response::redirect('/invoice/create');
        return;
      }

      // تحقق من صلاحية مجلد الرفع
      $uploadDir = __DIR__ . '/../uploads/';
      if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        // تعليق: مشكلة صلاحية مجلد الرفع
        Session::setFlash('error', 'خطأ: لا يمكن الكتابة في مجلد الصور. يرجى مراجعة صلاحيات المجلد.');
        Response::redirect('/invoice/create');
        return;
      }

      // تجهيز اسم الملف ومسار الحفظ
      $fileName = uniqid() . '_' . basename($file['name']);
      $targetPath = $uploadDir . $fileName;

      // رفع الصورة
      if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        // تعليق: فشل رفع الصورة
        Session::setFlash('error', 'خطأ: حدثت مشكلة أثناء رفع الصورة إلى الخادم.');
        Response::redirect('/invoice/create');
        return;
      }

      // محاولة حفظ بيانات الفاتورة في قاعدة البيانات
      $created = $this->invoiceModel->create(
        $this->request->getUserId(),
        $amount,
        $transferDate,
        $fileName
      );
      if ($created) {
        // نجاح الإنشاء
        Session::setFlash('success', 'تم رفع الفاتورة بنجاح.');
        Response::redirect('/dashboard');
        return;
      } else {
        // تعليق: فشل الإدخال في قاعدة البيانات
        Session::setFlash('error', 'خطأ: لم يتم حفظ الفاتورة. تحقق من قاعدة البيانات.');
        Response::redirect('/invoice/create');
        return;
      }
    }

    // عرض نموذج الإنشاء
    Response::view('invoice/create');
  }


  public function show($id) {
    $invoice = $this->invoiceModel->findById($id);

    // Admin can view any invoice, users only their own
    if (!$invoice || (!$this->request->isAdmin() && $invoice['user_id'] != $this->request->getUserId())) {
      Response::sendNotFound();
    }

    Response::view('invoice/show', [
      'invoice' => $invoice,
      'isAdminView' => $this->request->isAdmin()
    ]);
  }

  public function updateStatus($id) {
    if (!$this->request->isAdmin()) {
      Response::sendForbidden();
    }

    if ($this->request->getMethod() === 'POST') {
      $body = $this->request->getBody();
      $status = $body['status'] ?? null;
      $notes = $body['notes'] ?? null;

      $this->invoiceModel->updateStatus($id, $status, $notes);
      Session::setFlash('success', 'تم تحديث حالة الفاتورة');
    }

    Response::redirect("/admin/invoice/$id");
  }
}