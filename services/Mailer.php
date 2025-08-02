<?php

class Mailer {
    public function send($to, $subject, $message) {
        // في بيئة حقيقية، استخدم PHPMailer أو مكتبة مشابهة
        // هذا تنفيذ بسيط لأغراض التوضيح
        
        $headers = "From: " . Config::MAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . Config::MAIL_FROM . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        return mail($to, $subject, $message, $headers);
    }
}