<?php
class Response {
  public static function redirect($url) {
    header("Location: $url");
    exit();
  }

  public static function sendNotFound() {
    http_response_code(404);
    echo "الصفحة غير موجودة";
    exit();
  }

  public static function sendUnauthorized() {
    http_response_code(401);
    echo "غير مصرح بالوصول";
    exit();
  }

  public static function sendJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
  }

  public static function view($viewPath, $data = []) {
    extract($data);
    require __DIR__ . '/../views/' . $viewPath . '.php';
  }
}