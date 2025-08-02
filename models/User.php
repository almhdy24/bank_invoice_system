<?php
class User {
  private $db;

  public function __construct() {
    $this->db = Database::getInstance()->getConnection();
  }

  public function create($username, $password, $email, $role = Config::ROLE_USER) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $this->db->prepare('
            INSERT INTO users (username, password, email, role)
            VALUES (:username, :password, :email, :role)
        ');

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);

    return $stmt->execute();
  }

  public function findByUsername($username) {
    $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function findById($id) {
    $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function verifyCredentials($username, $password) {
    $user = $this->findByUsername($username);

    if ($user && password_verify($password, $user['password'])) {
      return $user;
    }

    return false;
  }

  public function getAllUsers() {
    $stmt = $this->db->query('SELECT id, username, email, role, created_at FROM users');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function register($username, $password, $email) {
    // التحقق من عدم وجود مستخدم بنفس الاسم أو البريد
    $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        throw new Exception('اسم المستخدم أو البريد الإلكتروني موجود بالفعل');
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $role = Config::ROLE_USER;
    
    $stmt = $this->db->prepare('
        INSERT INTO users (username, password, email, role) 
        VALUES (?, ?, ?, ?)
    ');
    
    return $stmt->execute([$username, $hashedPassword, $email, $role]);
}
}