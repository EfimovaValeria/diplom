<?php
session_start(); // Запускаем сессию

// Проверяем наличие tutor_id в сессии
if (!isset($_SESSION['student_id'])) {
    header("Location: entry.php"); // Перенаправляем на страницу входа
    exit();
}

// Теперь можно использовать $_SESSION['tutor_id'] для получения информации о преподавателе
$student_id = $_SESSION['student_id'];

// Подключение к базе данных
$host = "localhost"; // или ваш хост
$dbname = "postgres";
$user = "postgres";
$password = "postgres";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запрос для получения информации о студенте
    /*$stmt = $conn->prepare("SELECT * FROM student WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    // Получаем данные тьютора
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем, что тьютор найден
    if (!$student) {
        echo "Тьютор не найден.";
        exit();
    }*/

    $stmt = $conn->prepare("
        SELECT s.student_id, s.surname, s.firstname, s.patronymic, s.email, c.class, s.student_id 
        FROM student s 
        JOIN class c ON s.class = c.class_id 
        WHERE s.student_id = :student_id
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$student) {
        echo "Тьютор не найден.";
        exit();
    }

    // Здесь можно использовать данные тьютора, например:
    /*echo "Фамилия студента: " . htmlspecialchars($student['surname']);
    echo "Имя студента: " . htmlspecialchars($student['firstname']);
    echo "Отчество студента: " . htmlspecialchars($student['patronymic']);
    echo "Email студента: " . htmlspecialchars($student['email']);*/

    /*$stmt = $conn->prepare('SELECT class FROM class WHERE class.class_id = :class_id');
    $stmt->bindParam(':class_id', $class_id);
    $stmt->execute();
    
    // Получаем данные тьютора
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем, что тьютор найден
    if (!$class_id) {
        echo "Класс не найден.";
        exit();
    }*/
    
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
                <a href="studentprofile.php">Профиль</a>
                <a href="studentschedule.php">Расписание</a>
                <a href="studentassignment.php">Задания</a>
                <!--<a href="studentreportcard.php">Дневник</a>-->
                <!--ДИПЛОМ-->
            </div>
            <div id="main" class="main-content">
                <button class="openbtn" onclick="openNav()">&#9776;</button>
                <span style = "font-size:9mm">Профиль ученика</span>
                <!--ПРОФИЛЬ-->
                <div class="profile-info">
                    <h2>Идентификатор студента: <?php echo htmlspecialchars($student['student_id']); ?></h2>    
                    <h2>Имя студента: <?php echo htmlspecialchars($student['surname'] . ' ' . $student['firstname'] . ' ' . $student['patronymic']); ?></h2>
                    <h2>Электронная почта: <?php echo htmlspecialchars($student['email']); ?></h2>   
                    <h2>Класс: <?php echo htmlspecialchars($student['class']); ?></h2> 
                </div>
            </div>
        </div>
	<footer class="footer">
        <button class="back-to-top" onclick="scrollToTop()">Наверх</button>
    </footer>

    <script src="script.js"></script>
</body>
</html>