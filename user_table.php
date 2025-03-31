<?php
require_once 'db.php';

class UserTable {
    private $db;

    // Подключение к базе данных при создании объекта
    public function __construct() {
        $this->db = DB::getInstance();
    }

    // Получение всех пользователей
    public function getAllUsers() {
        return $this->db->query('SELECT id, name, birthdate, team_number, email, photo FROM users')->fetchAll(PDO::FETCH_ASSOC);
    }


    // Получение одного пользователя по ID
    public function getUser($id) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Создание нового пользователя
  public function addUser($name, $birthdate, $team_number, $email, $photo) {
    $checkStmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $checkStmt->execute([$email]);

    if ($checkStmt->fetchColumn() > 0) {
        return "error_email_exists"; // Вместо исключения возвращаем ошибку
    }

    $stmt = $this->db->prepare("INSERT INTO users (name, birthdate, team_number, email, photo) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $birthdate, $team_number, $email, $photo]);
}



    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Обновление данных пользователя
    public function updateUser($id, $data) {
        $query = "UPDATE users SET name = ?, birthdate = ?, team_number = ?, email = ?" .
                 ($data['photo'] ? ", photo = ?" : "") . " WHERE id = ?";
        
        $params = [$data['name'], $data['birthdate'], $data['team_number'], $data['email']];
        
        if ($data['photo']) {
            $params[] = $data['photo'];
        }
        
        $params[] = $id;
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }


    // Удаление пользователя
    public function deleteUser($id) {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
