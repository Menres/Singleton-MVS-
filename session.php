<?php
// Стартуем сессию
session_start();

// Авторизация пользователя
function login($user_id) {
    $_SESSION['user_id'] = $user_id;
}

// Выход из сессии
function logout() {
    session_destroy();
}

// Получение текущего пользователя
function currentUser() {
    return $_SESSION['user_id'] ?? null;
}
