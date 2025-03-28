<?php
session_start(); // Запускаем сессию

// Проверяем наличие tutor_id в сессии
if (!isset($_SESSION['tutor_id'])) {
    header("Location: entry.php"); // Перенаправляем на страницу входа
    exit();
}

// Теперь можно использовать $_SESSION['tutor_id'] для получения информации о преподавателе
$tutor_id = $_SESSION['tutor_id'];

// Подключение к базе данных
$host = "localhost"; // или ваш хост
$dbname = "postgres";
$user = "postgres";
$password = "postgres";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запрос для получения информации о тьюторе
    $stmt = $conn->prepare("SELECT * FROM tutor WHERE tutor_id = :tutor_id");
    $stmt->bindParam(':tutor_id', $tutor_id);
    $stmt->execute();
    
    // Получаем данные тьютора
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем, что тьютор найден
    if (!$tutor) {
        echo "Тьютор не найден.";
        exit();
    }

    // Здесь можно использовать данные тьютора, например:
    /*echo "Фамилия тьютора: " . htmlspecialchars($tutor['surname']);
    echo "Имя тьютора: " . htmlspecialchars($tutor['firstname']);
    echo "Отчество тьютора: " . htmlspecialchars($tutor['patronymic']);
    echo "Email тьютора: " . htmlspecialchars($tutor['email']);*/
    
} catch (PDOException $e) {
    echo "Ошибка подключения: " . htmlspecialchars($e->getMessage());
    exit();
}
?> 
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
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
                    <! --конструктор от каждого ученика-->
                <a href="assignment.php">Задания</a>
                <!--<a href="tutorreportcard.php">Журнал</a>-->
            </div>
            <div id="main" class="main-content">
                <button class="openbtn" onclick="openNav()">&#9776;</button>
                <span style = "font-size:9mm">Профиль тьютора</span>
                <div class="profile-info">
                    <h2>Имя тьютора: <?php echo htmlspecialchars($tutor['surname'] . ' ' . $tutor['firstname'] . ' ' . $tutor['patronymic']); ?></h2>
                    <h2>Электронная почта: <?php echo htmlspecialchars($tutor['email']); ?></h2>   
                </div>
            </div>
        </div>
	<footer class="footer">
        <button class="back-to-top" onclick="scrollToTop()">Наверх</button>
    </footer>

    <script src="script.js"></script>
</body>
</html>