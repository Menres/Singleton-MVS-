<?php 

require_once 'user_logic.php';
$userLogic = new UserLogic();
$users = $userLogic->getUsers();

// Обработка отправки формы с проверкой
$errors = [];
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
$team_number = isset($_POST['team_number']) ? trim($_POST['team_number']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Доступные номера бригад
$team_numbers = ["101", "102", "103", "104"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {

    // Проверка полей на заполнение
    if (!$name) {
        $errors[] = 'Поле «Имя» не заполнено';
    }
    if (!$birthdate) {
        $errors[] = 'Поле «Дата рождения» не заполнено';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
        $errors[] = 'Поле «Дата рождения» указано неверно. Используйте формат YYYY-MM-DD.';
    }
    if (!$team_number || !in_array($team_number, $team_numbers)) {
        $errors[] = 'Поле «Номер бригады» не выбрано или неверно';
    }
    if (!$email) {
        $errors[] = 'Поле «Электронная почта» не заполнено';
    }

    // Загрузка фотографии
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp_path = $_FILES['photo']['tmp_name'];
        $photo_name = basename($_FILES['photo']['name']);
        $photo_path = 'uploads/' . uniqid() . '_' . $photo_name;

        if (!move_uploaded_file($photo_tmp_path, $photo_path)) {
            $errors[] = 'Ошибка загрузки фотографии';
        }
    } else {
        $errors[] = 'Фотография не загружена';
    }

    // Если нет ошибок - создаём пользователя
    if (empty($errors)) {
        try {
            $userLogic->createUser([
                'name' => $name,
                'birthdate' => $birthdate,
                'team_number' => $team_number,
                'email' => $email,
                'photo' => $photo_path
            ]);
            header('Location: index.php');
            exit;

        } catch (Exception $e) {
            if (strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
                $errors[] = 'Введенный email уже есть в базе данных!';
            } else {
                $errors[] = $e->getMessage();  // <-- Исправлено: добавлен отступ
            }
        } // <-- Закрываем блок catch
    }  // <-- Закрываем if (empty($errors))
}  // <-- Закрываем if ($_SERVER['REQUEST_METHOD'] === 'POST')


//
$edit_user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $edit_id = $_POST['edit_id'];
    $edit_user = $userLogic->getUserById($edit_id);
}
//


//
// Обновление пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    $user_id = $_POST['user_id'] ?? null; // ✅ Теперь, если user_id отсутствует, будет null

    if ($user_id === null) {
        die("Ошибка: ID пользователя не передан!");
    }

    $name = trim($_POST['name']);
    $birthdate = trim($_POST['birthdate']);
    $team_number = trim($_POST['team_number']);
    $email = trim($_POST['email']);

    // Используем старое фото, если новое не загружено
    $photo_path = $edit_user['photo'] ?? '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp_path = $_FILES['photo']['tmp_name'];
        $photo_name = basename($_FILES['photo']['name']);
        $photo_path = 'uploads/' . uniqid() . '_' . $photo_name;
        move_uploaded_file($photo_tmp_path, $photo_path);
    }

    $userLogic->updateUser($user_id, [
        'name' => $name,
        'birthdate' => $birthdate,
        'team_number' => $team_number,
        'email' => $email,
        'photo' => $photo_path
    ]);

    header('Location: index.php');
    exit;
}



?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список рабочих</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

</head>
<body>

<div class="wrapper">
    <div class="container">
        <h1 class="text-center mb-4">Список рабочих</h1>

        <table class="table table-striped table-hover shadow-sm table-bordered">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Фотография</th>
                    <th scope="col">Имя</th>
                    <th scope="col">Дата рождения</th>
                    <th scope="col">Номер бригады</th>
                    <th scope="col">Электронная почта</th>
                    <th scope="col">Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <th scope="row"><?= htmlspecialchars($user['id']) ?></th>
                    <td>
                        <?php if (!empty($user['photo']) && file_exists($user['photo'])): ?>
                            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Фото" width="50">
                        <?php else: ?>
                            <img src="/uploads/no-image.jpg" alt="Нет фото" width="50"> <!-- Запасное фото -->
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= date('d-m-Y', strtotime($user['birthdate'])) ?></td>
                    <td><?= htmlspecialchars($user['team_number']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>     
                        <div class="d-flex gap-2">
                            <form action="read.php" method="get">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-info btn-sm">Читать</button>
                            </form>

                            <form action="index.php" method="post">
                                <input type="hidden" name="edit_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="action" value="edit" class="btn btn-warning btn-sm">Редактировать</button>
                            </form>

                            <form action="user_actions.php" method="post" onsubmit="return confirm('Точно удалить пользователя?');">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Удалить</button>
                            </form>
                        </div>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Форма добавления нового пользователя -->
          <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title"><?= $edit_user ? 'Редактировать пользователя' : 'Добавить нового пользователя' ?></h5>
                <form action="index.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?= $edit_user['id'] ?? '' ?>">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Имя" value="<?= htmlspecialchars($edit_user['name'] ?? $name) ?>">
                    </div>
                    <div class="mb-3">
                        <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($edit_user['birthdate'] ?? $birthdate) ?>">
                    </div>
                    <div class="mb-3">
                        <select name="team_number" class="form-control">
                            <option value="">Выберите номер бригады</option>
                            <?php foreach ($team_numbers as $number): ?>
                                <option value="<?= $number ?>" <?= isset($edit_user) && $edit_user['team_number'] == $number ? 'selected' : '' ?>><?= $number ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Электронная почта" value="<?= htmlspecialchars($edit_user['email'] ?? $email) ?>">
                    </div>
                    <div class="mb-3">
                        <input type="file" name="photo" class="form-control">
                    </div>
                    <button type="submit" name="action" value="<?= $edit_user ? 'update' : 'create' ?>" class="btn btn-primary">
                        <?= $edit_user ? 'Обновить' : 'Создать' ?>
                    </button>
                </form>
            </div>
        </div>


    </div>

    <footer class="mt-5 bg-dark text-white">
        <p>© <?= date('Y') ?> My project MVS</p>
    </footer>
</div>

</body>
</html>