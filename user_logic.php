<?php
require_once 'user_table.php';

class UserLogic {
    private $table;

    public function __construct() {
        $this->table = new UserTable();
    }

    // Правильный способ вызова метода getAllUsers()
    public function getUsers() {
        return $this->table->getAllUsers();
    }

    public function createUser($data) {
        try {
            return $this->table->addUser(
                $data['name'], 
                $data['birthdate'], 
                $data['team_number'], 
                $data['email'],
                $data['photo']
            );
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function getUserById($id) {
        return $this->table->getUserById($id);
    }


    public function updateUser($id, $data) {
        return $this->table->updateUser($id, $data);
    }


    public function deleteUser($id) {
        return $this->table->deleteUser($id);
    }
}
