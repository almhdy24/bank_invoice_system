<?php
class AdminController {
  private $request;
  private $invoiceModel;
  private $userModel;

  public function __construct(Request $request) {
    $this->request = $request;
    $this->invoiceModel = new Invoice();
    $this->userModel = new User();

    if (!$this->request->isAuthenticated() || !$this->request->isAdmin()) {
      Response::sendUnauthorized();
    }
  }

  public function dashboard() {
    $status = $this->request->getBody()['status'] ?? null;
    $invoices = $this->invoiceModel->getAllInvoices($status);

    Response::view('admin/dashboard', [
      'invoices' => $invoices,
      'statuses' => [
        Config::INVOICE_STATUS_PENDING,
        Config::INVOICE_STATUS_APPROVED,
        Config::INVOICE_STATUS_REJECTED
      ],
      'selectedStatus' => $status
    ]);
  }

  public function updateInvoiceStatus($id) {
    if ($this->request->getMethod() === 'POST') {
      $body = $this->request->getBody();
      $status = $body['status'] ?? null;
      $notes = $body['notes'] ?? null;

      $invoice = $this->invoiceModel->findById($id);

      if (!$invoice) {
        Response::sendNotFound();
      }

      if (!in_array($status, [
        Config::INVOICE_STATUS_PENDING,
        Config::INVOICE_STATUS_APPROVED,
        Config::INVOICE_STATUS_REJECTED
      ])) {
        Session::setFlash('error', 'حالة الفاتورة غير صالحة');
        Response::redirect("/admin/invoice/$id");
      }

      $success = $this->invoiceModel->updateStatus($id, $status, $notes);

      if ($success) {
        // إرسال إشعار للمستخدم إذا كان هناك تغيير في الحالة
        if ($invoice['status'] !== $status) {
          $this->sendStatusChangeNotification($invoice, $status, $notes);
        }

        Session::setFlash('success', 'تم تحديث حالة الفاتورة بنجاح');
      } else {
        Session::setFlash('error', 'حدث خطأ أثناء تحديث حالة الفاتورة');
      }

      Response::redirect("/admin/invoice/$id");
    }
  }

  private function sendStatusChangeNotification($invoice, $newStatus, $notes) {
    $user = $this->userModel->findById($invoice['user_id']);

    if ($user && $user['email']) {
      $mailer = new Mailer();
      $subject = "تغيير حالة الفاتورة #{$invoice['id']}";

      $message = "مرحباً {$user['username']},\n\n";
      $message .= "تم تغيير حالة الفاتورة رقم #{$invoice['id']} إلى: $newStatus\n";
      $message .= "المبلغ: {$invoice['amount']}\n";
      $message .= "تاريخ التحويل: {$invoice['transfer_date']}\n\n";

      if ($notes) {
        $message .= "ملاحظات:\n$notes\n\n";
      }

      $message .= "شكراً لاستخدامك نظام تتبع الفواتير.";

      $mailer->send(
        $user['email'],
        $subject,
        $message
      );
    }
  }

  public function showInvoice($id) {
    $invoice = $this->invoiceModel->findById($id);

    if (!$invoice) {
      Response::sendNotFound();
    }

    Response::view('invoice/show', [
      'invoice' => $invoice,
      'isAdminView' => true
    ]);
  }
}