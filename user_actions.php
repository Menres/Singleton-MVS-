<?php
require_once 'user_logic.php';

$userLogic = new UserLogic();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $userLogic->createUser($_POST);
            break;

        case 'update':
            $userLogic->editUser($_POST['id'], $_POST);
            break;

        case 'delete':
            $userLogic->deleteUser($_POST['id']); // вот корректный вызов метода
            break;
    }

    header('Location: index.php');
    exit;
}
