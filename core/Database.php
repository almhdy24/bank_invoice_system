<?php
class Database {
  private static $instance = null;
  private $pdo;

  private function __construct() {
    try {
      $this->pdo = new PDO('sqlite:' . Config::DB_PATH);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->pdo->exec('PRAGMA foreign_keys = ON');

    } catch (PDOException $e) {
      die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
    }
  }

  public static function getInstance() {
    if (!self::$instance) {
      self::$instance = new Database();
    }
    return self::$instance;
  }

  public function getConnection() {
    return $this->pdo;
  }


}