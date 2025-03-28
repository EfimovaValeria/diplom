<?php  
session_start(); // Начинаем сессию
ob_start();   

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn_string = "host=localhost port=5432 dbname=postgres user=postgres password=postgres";
    $conn = pg_connect($conn_string);                                
    if (!$conn) {
        $_SESSION['error'] = "Ошибка подключения: " . htmlspecialchars(pg_last_error());
        header("Location: entry.php"); // Перенаправляем на страницу входа
        exit();
    } else {
        // Получение данных из формы
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $enteredPassword = $_POST['password'];
        
        // Проверка существования пользователя с таким email
        $checkEmailQuery = 'SELECT role, password, student_id AS id FROM student WHERE email = $1 
                            UNION SELECT role, password, parent_id AS id FROM parent WHERE email = $1 
                            UNION SELECT role, password, tutor_id AS id FROM tutor WHERE email = $1';        
        $result = pg_query_params($conn, $checkEmailQuery, array($email));        
        
        if (pg_num_rows($result) > 0) {
            // Пользователь найден
            $userData = pg_fetch_assoc($result);
            $storedHashedPassword = $userData['password'];
            $role = $userData['role'];
            
            // Проверяем пароль
            if (password_verify($enteredPassword, $storedHashedPassword)) {
                // Пароль правильный, перенаправляем на соответствующую страницу профиля
                session_regenerate_id(); // Обновляем ID сессии
                switch ($role) {
                    case '3':
                        $_SESSION['student_id'] = $userData['id']; 
                        header("Location: studentprofile.php");
                        break;
                    case '1':
                        $_SESSION['parent_id'] = $userData['id']; 
                        header("Location: parentprofile.php");
                        break;
                    case '2':
                        $_SESSION['tutor_id'] = $userData['id']; 
                        header("Location: tutorprofile.php");
                        break;
                    default:
                        $_SESSION['error'] = "Неизвестная роль пользователя.";
                        header("Location: entry.php");
                        break;
                }
                exit(); // Завершаем выполнение скрипта после перенаправления
            } else {
                $_SESSION['error'] = "Пароль неверный!";
            }
        } else {
            $_SESSION['error'] = "Пользователь с таким email не найден!";
        }
        
        // Закрываем соединение с базой данных
        pg_close($conn);    
    }
}

ob_end_flush(); 
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 97%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #178044;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Вход</h2>
    <?php    
    if (isset($_SESSION['error'])) {
        echo "<div style='color:red;'>" . htmlspecialchars($_SESSION['error']) . "</div>";         
        unset($_SESSION['error']); // Удаляем сообщение об ошибке после отображения
    }    
    ?>     
    <form id="loginForm" method="POST" action="">        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>        
        <label for="password">Пароль:</label>        
        <input type="password" id="password" name="password" required>
        <button type="submit">Войти</button>
    </form>
</div>

<script>
  // Получаем введённый пароль и хешированный пароль из базы данных



</script>

</body>
</html>