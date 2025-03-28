<?php
session_start();
ob_start();

$host = "localhost"; // или ваш хост
$dbname = "postgres";
$user = "postgres";
$password = "postgres";

if (!isset($_SESSION['student_id'])) {
    header("Location: entry.php");
    exit();
}

$student_id = $_SESSION['student_id'];

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запрос на получение класса
    $stmt = $conn->prepare("SELECT class FROM student WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $classRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$classRow) {
        echo("Ошибка: не найден класс для указанного студента.");
    }

    $class_id = (int)$classRow['class'];

    // Запрос на получение предметов
    $subjectQuery = 'SELECT s.subject_name 
                     FROM subject s 
                     JOIN subjectclass sc ON s.subject_id = sc.subject_id 
                     WHERE sc.class_id = :class_id';
    $stmt = $conn->prepare($subjectQuery);
    $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    
    // Проверяем наличие значений выбранных дат
    if (isset($_POST['selected_date1'], $_POST['selected_date2'])) {
        $selected_date1 = $_POST['selected_date1'];
        $selected_date2 = $_POST['selected_date2'];

       // Запрос на получение расписания с названиями предметов
        $checkScheduleQuery = "SELECT s.date, s.starttime, sub.subject_name 
            FROM schedule AS s
            JOIN subjectclass AS sc ON s.subjectclass_id = sc.subjectclass_id
            JOIN subject AS sub ON sc.subject_id = sub.subject_id
            WHERE s.date BETWEEN :start_date AND :end_date AND s.student_id = :student_id
            ";
        $stmt = $conn->prepare($checkScheduleQuery);
        $stmt->bindParam(":start_date", $selected_date1);
        $stmt->bindParam(":end_date", $selected_date2);
        $stmt->bindParam(":student_id", $student_id, PDO::PARAM_INT);
        $stmt->execute();
        $scheduleResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$scheduleResult || empty($scheduleResult)) {
            echo("Ошибка: не найден класс для указанного студента.");
            }

        // Формирование расписания
        $scheduleTable = [];
        foreach ($scheduleResult as $schedule) {
        $dayOfWeek = date('N', strtotime($schedule['date']));
        $startTime = $schedule['starttime'];
        if (!isset($scheduleTable[$startTime])) {
            $scheduleTable[$startTime] = ['', '', '', '', '', '', ''];
        }
        // Используем subject_name вместо subjectclass_id
        $scheduleTable[$startTime][$dayOfWeek - 1] = htmlspecialchars($schedule['subject_name']);
        }
    } else {
        
    }

    // Закрытие соединения
    $conn = null;
    ob_end_flush();
} catch (PDOException $e) {
    echo "Ошибка подключения: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Конструктор Расписания</title>
    <link rel="stylesheet" href="schedule.css">
</head>
<body>
    <h1>Конструктор Расписания</h1>
    <div id="schedule-container">
        <form action="schedule.php" method="POST">
        
        <div class="date-inputs-container">
        <input type="date" class="date-input" name="selected_date1" value="<?php echo isset($_POST['selected_date1']) ? htmlspecialchars($_POST['selected_date1']) : ''; ?>" required>
        <input type="date" class="date-input" name="selected_date2" value="<?php echo isset($_POST['selected_date2']) ? htmlspecialchars($_POST['selected_date2']) : ''; ?>" required>
        <button type="submit" class="show-schedule-btn">Показать расписание </button>
        </div>
        <table id="schedule-table">
            <thead>
                <tr>
                    <th class="time-column">Время</th>
                    <th>Пн</th>
                    <th>Вт</th>
                    <th>Ср</th>
                    <th>Чт</th>
                    <th>Пт</th>
                    <th>Сб</th>
                    <th>Вс</th>
                </tr>
            </thead>
            <tbody>
            <?php 
// Дополнительные времена для расписания
$additionalTimes = ['09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00'];

// Объединяем дополнительные времена с уже существующим расписанием
$allTimes = array_keys($scheduleTable);
$mergedTimes = array_unique(array_merge($allTimes, $additionalTimes));
sort($mergedTimes); // Сортируем время

// Генерация строк таблицы для каждого времени
foreach ($mergedTimes as $time): ?>
    <tr>
        <td class="time-column"><?php echo htmlspecialchars($time); ?></td>
        <?php
        // Проверяем, есть ли у нас расписание на это время
        if (isset($scheduleTable[$time])) {
            $days = $scheduleTable[$time];
            foreach ($days as $subject): ?>
                <td><?php echo ($subject ? htmlspecialchars($subject) : ''); ?></td>
            <?php endforeach;
        } else {
            // Если расписания нет, добавляем пустые ячейки для каждого дня
            for ($i = 0; $i < 7; $i++): // Предполагаем 7 дней в неделе
                echo '<td></td>';
            endfor;
        }
        ?>
    </tr>
<?php endforeach; ?>




            </tbody>
        </table>
    </div>
    <div id="subject-menu" class="hidden">
        <select id="subject-select">
            <option value="">Выберите предмет</option>
            <?php foreach ($subjects as $subject): ?>
            <option value="<?= htmlspecialchars($subject['subject_name']) ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
            <?php endforeach; ?>
        </select>

    </div>
    <button id="save-schedule" class="show-schedule-btn">Сохранить расписание</button>
    <button id="clear-schedule" class="show-schedule-btn">Очистить расписание</button>
    <a href="studentlist.php" class="show-schedule-btn">
    Назад к студентам
</a>

    <script src="schedule.js"></script>
</body>
</html>

   