<?php
session_start();// Подключение к базе данных
$host = "localhost"; // или ваш хост
$dbname = "postgres";
$user = "postgres";
$password = "postgres";

// Предположим, что tutor_id хранится в сессии после входа в систему

if (!isset($_SESSION['tutor_id'])) {
    header("Location: entry.php"); // Перенаправление на страницу входа
    exit();
}
$tutor_id = $_SESSION['tutor_id']; // Убедитесь, что tutor_id хранится в сессии

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запрос на получение списка учеников с их классами
    $stmt = $conn->prepare("
        SELECT s.surname, s.firstname, s.patronymic, c.class AS class_name, s.student_id 
        FROM student s 
        JOIN class c ON s.class = c.class_id 
        WHERE s.tutor_id = :tutor_id
    ");
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();

    // Получаем все результаты
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ученики</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4caf50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .schedule-button {
            background-color: #008CBA;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .schedule-button:hover {
            background-color: #005f7f;
        }
    </style>
</head>
<body>
<header>
<a href="home.php"><img src="logo.png" alt="Logo" class="logo"></a>
        <div class="btn-group">
            <a href="home.php" class="register-btn">Выйти</a>
        </div>
</header>
<div class="container">
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="tutorprofile.php">Профиль</a>
        <a href="studentlist.php">Мои ученики</a>
        <a href="assignment.php">Задания</a>

    </div>
    <div id="main" class="main-content">
        <button class="openbtn" onclick="openNav()">&#9776;</button>
        <span style="font-size:9mm">Список учеников</span>
        <!-- СПИСОК УЧЕНИКОВ -->
        <table>
            <thead>
                <tr>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Класс</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['surname']); ?></td>
                        <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($student['patronymic']); ?></td>
                        <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                        <td>
                            <form action="schedule.php" method="POST">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($_SESSION['student_id'] = $student['student_id']); ?>">
                            <input type="submit" class="schedule-button" value="Составить расписание">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
<footer class="footer">
    <button class="back-to-top" onclick="scrollToTop()">Наверх</button>
</footer>
<script src="script.js"></script>
</body>
</html>


