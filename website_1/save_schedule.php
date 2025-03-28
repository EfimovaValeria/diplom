<?php
session_start();
// Установите соединение с базой данных
$host = "localhost"; // или ваш хост
$dbname = "postgres";
$user = "postgres";
$password = "postgres";
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Получаем данные из POST-запроса
    $data = json_decode(file_get_contents('php://input'), true);

    // Подготовка SQL-запроса для вставки данных в таблицу
    $stmt = $pdo->prepare("INSERT INTO schedule (student_id, date, weekday, starttime, subjectclass_id, curr_id) VALUES (:student_id, :date, :weekday, :starttime, :subjectclass_id, :curr_id)");
    // Предположим, что у вас есть даты начала и конца недели
        $startOfWeek = $_POST['selected_date1'];// Понедельник
        $endOfWeek = $_POST['selected_date2']; // Воскресенье
        
    // Предполагаем, что student_id и curr_id известны заранее
    $student_id = $_SESSION['student_id']; // Замените на актуальное значение
    $curr_id = 1; // Замените на актуальное значение

    foreach ($data as $entry) {
     $dayOfWeek = $entry['day']; // 1-7 (Пн-Вс)
        $time = $entry['time']; // Время в формате HH:MM
        $subject = $entry['subject'];

    // Определяем дату на основе дня недели
        $date = date('Y-m-d', strtotime($startOfWeek . " + " . ($dayOfWeek - 1) . " days"));

        // Проверяем, находится ли дата в пределах начала и конца недели
    if ($date >= $startOfWeek && $date <= $endOfWeek) {
        // Дата корректна и находится в пределах недели
        echo "Дата: " . $date;
    } else {
    // Дата выходит за пределы заданной недели
        echo "Дата выходит за пределы заданной недели.";
    }  
        // Получаем subjectclass_id по названию предмета
       // Получаем subject_id по имени предмета
    $subject_stmt = $pdo->prepare("SELECT subject_id FROM subject WHERE subject_name = :subject");
    $subject_stmt->execute(['subject' => $subject]);
    $subject_row = $subject_stmt->fetch(PDO::FETCH_ASSOC);

    if ($subject_row) {
        $subject_id = $subject_row['subject_id'];
        // Теперь получаем subjectclass_id по subject_id
        $subjectclass_stmt = $pdo->prepare("SELECT subjectclass_id FROM subjectclass WHERE subject_id = :subject_id");
        $subjectclass_stmt->execute(['subject_id' => $subject_id]);
        $subjectclass_row = $subjectclass_stmt->fetch(PDO::FETCH_ASSOC);

        if ($subjectclass_row) {
        $subjectclass_id = $subjectclass_row['subjectclass_id'];

        // Выполняем вставку данных в таблицу расписания
        $stmt->execute([
            ':student_id' => $student_id,
            ':date' => $date,
            ':weekday' => $dayOfWeek,
            ':starttime' => $time,
            ':subjectclass_id' => $subjectclass_id,
            ':curr_id' => $curr_id
        ]);
        } else {
        // Обработка случая, когда subjectclass не найден
        echo "Класс для данного предмета не найден.";
        }
    } else {
    // Обработка случая, когда предмет не найден
    echo "Предмет не найден.";
}}

    // Возвращаем успешный ответ
    http_response_code(200);
    echo json_encode(['message' => 'Расписание сохранено!']);
} catch (PDOException $e) {
    // Обработка ошибок базы данных
    http_response_code(500);
    echo json_encode(['message' => 'Ошибка при сохранении расписания: ' . $e->getMessage()]);
}
?>
