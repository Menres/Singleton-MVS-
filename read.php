<?php
// Подключаем логику работы с пользователями
require_once 'user_logic.php';
$userLogic = new UserLogic();

// Проверка наличия параметра id в GET-запросе
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Ошибка: ID пользователя не передан.");
}

$id = $_GET['id'];

// Получаем данные пользователя по id
$user = $userLogic->getUserById($id);

// Если пользователь не найден, выводим сообщение
if (!$user) {
    die("Пользователь с ID {$id} не найден.");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Детали пользователя</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Детали пользователя</h1>
    <div class="card">
        <div class="card-body">
            <th scope="row"><?= htmlspecialchars($user['id']) ?></th>
                    <td>
                        <?php if (!empty($user['photo']) && file_exists($user['photo'])): ?>
                            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Фото" width="50">
                        <?php else: ?>
                            <img src="/uploads/no-image.jpg" alt="Нет фото" width="50"> <!-- Запасное фото -->
                        <?php endif; ?>
                    </td>

            <!-- Вывод остальных данных пользователя -->
            <h3 class="mt-3"><?= htmlspecialchars($user['name']) ?></h3>
            <p><strong>Дата рождения:</strong> <?= date('d-m-Y', strtotime($user['birthdate'])) ?></p>
            <p><strong>Номер бригады:</strong> <?= htmlspecialchars($user['team_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>

    <!-- Кнопка возврата на главную страницу -->
    <a href="index.php" class="btn btn-primary mt-3">Назад</a>

</div>

<footer class="mt-5 bg-dark text-white">
    <p>© <?= date('Y') ?> My project MVS</p>
</footer>
</body>
</html>
