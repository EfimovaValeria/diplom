<?php
session_start(); // Запускаем сессию

// Проверяем наличие tutor_id в сессии
if (!isset($_SESSION['parent_id'])) {
    header("Location: entry.php"); // Перенаправляем на страницу входа
    exit();
}

// Теперь можно использовать $_SESSION['tutor_id'] для получения информации о преподавателе
$parent_id = $_SESSION['parent_id'];

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

    //$stmt = $conn->prepare("SELECT * FROM parent WHERE parent_id = :parent_id");
    $stmt = $conn->prepare("
        SELECT surname, firstname, patronymic, email, student_id 
        FROM parent
        WHERE parent_id = :parent_id
    ");
    $stmt->bindParam(':parent_id', $parent_id);
    $stmt->execute();
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);
    /*if (!$parent) {
        echo "Тьютор не найден.";
        exit();
    }*/



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
                <a href="parentprofile.php">Профиль</a>
                
                <a href="parentschedule.php" value="<?php echo htmlspecialchars($_SESSION['student_id'] = $parent['student_id']); ?>">Расписание</a>
                
                <a href="parentassignment.php">Задания</a>
                <a href="parentreportcard.php">Дневник</a>
            </div>
            <div id="main" class="main-content">
                <button class="openbtn" onclick="openNav()">&#9776;</button>
                <span style = "font-size:9mm">Ваш профиль</span>

                <!--ПРОФИЛЬ-->
                <div class="profile-info">    
                    <h2>Имя: <?php echo htmlspecialchars($parent['surname'] . ' ' . $parent['firstname'] . ' ' . $parent['patronymic']); ?></h2>
                    <h2>Электронная почта: <?php echo htmlspecialchars($parent['email']); ?></h2>  
                    <h2>Идентификатор студента: <?php echo htmlspecialchars($parent['student_id']); ?></h2>
                </div>
                
            </div>
        </div>
        
	<footer class="footer">
        <button class="back-to-top" onclick="scrollToTop()">Наверх</button>
    </footer>

    <script src="script.js"></script>
</body>
</html>