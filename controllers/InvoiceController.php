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
    // Admin sees all invoices, regular users see only their own
    $invoices = $this->request->isAdmin()
    ? $this->invoiceModel->getAllInvoices()
    : $this->invoiceModel->findByUserId($this->request->getUserId());
  
    Response::view('invoice/dashboard', [
      'invoices' => $invoices,
      'isAdmin' => $this->request->isAdmin()
    ]);
  }

  public function create() {
    if ($this->request->getMethod() === 'POST') {
      $body = $this->request->getBody();
      $file = $this->request->getFile('invoice_image');

      // Basic validation
      $amount = filter_var($body['amount'], FILTER_VALIDATE_FLOAT);
      $transferDate = $body['transfer_date'] ?? null;

      if (!$amount || $amount <= 0) {
        Session::setFlash('error', 'المبلغ يجب أن يكون رقمًا صحيحًا أكبر من الصفر');
        Response::redirect('/invoice/create');
      }

      if (!$transferDate) {
        Session::setFlash('error', 'تاريخ التحويل مطلوب');
        Response::redirect('/invoice/create');
      }

      // Process file upload if present
      if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg',
          'image/png',
          'image/gif'];
        $fileType = mime_content_type($file['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
          if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->invoiceModel->create(
              $this->request->getUserId(),
              $amount,
              $transferDate,
              $fileName
            );
            Session::setFlash('success', 'تم رفع الفاتورة بنجاح');
            Response::redirect('/dashboard');
          }
        }
      }
      Session::setFlash('error', 'حدث خطأ أثناء رفع الفاتورة');
    }

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