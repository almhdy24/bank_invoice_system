<?php
class Invoice {
  private $db;

  public function __construct() {
    $this->db = Database::getInstance()->getConnection();
  }

  public function create($userId, $amount, $transferDate, $imagePath) {
    $stmt = $this->db->prepare('
            INSERT INTO invoices (user_id, amount, transfer_date, image_path)
            VALUES (:user_id, :amount, :transfer_date, :image_path)
        ');

    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':transfer_date', $transferDate);
    $stmt->bindParam(':image_path', $imagePath);

    return $stmt->execute();
  }

  public function findById($id) {
    $stmt = $this->db->prepare('
            SELECT i.*, u.username
            FROM invoices i
            JOIN users u ON i.user_id = u.id
            WHERE i.id = :id
        ');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function findByUserId($userId) {
    $stmt = $this->db->prepare('SELECT * FROM invoices WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllInvoices($status = null) {
    $sql = 'SELECT i.*, u.username FROM invoices i JOIN users u ON i.user_id = u.id';

    if ($status) {
      $sql .= ' WHERE i.status = :status';
    }

    $sql .= ' ORDER BY i.created_at DESC';

    $stmt = $this->db->prepare($sql);

    if ($status) {
      $stmt->bindParam(':status', $status);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function updateStatus($id, $status, $notes = null) {
    $stmt = $this->db->prepare('
            UPDATE invoices
            SET status = :status, notes = :notes
            WHERE id = :id
        ');

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':notes', $notes);

    return $stmt->execute();
  }

  public function delete($id) {
    $stmt = $this->db->prepare('DELETE FROM invoices WHERE id = :id');
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }
}