<?php  
    ob_start();   
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn_string = "host=localhost port=5432 dbname=postgres user=postgres password=postgres";
    $conn = pg_connect($conn_string);                        
    
    if (!$conn) {
        $error = pg_last_error();
        echo "Ошибка подключения: $error";    
    } else {
        // Получение данных из формы
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $surname = $_POST['surname'];
        $firstname = $_POST['firstname'];
        $patronymic = $_POST['patronymic'];
        $password = $_POST['password']; 
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $role = $_POST['roleName'];        
        $childName = isset($_POST['childName']) ? $_POST['childName'] : null;
        $classForParent = isset($_POST['classForParent']) ? $_POST['classForParent'] : null;
        $classForStudent = isset($_POST['classForStudent']) ? $_POST['classForStudent'] : null;
        function getClassId($conn, $classForStudent) {
            // Подготовим SQL-запрос для получения идентификатора роли
            $query = 'SELECT class_id FROM class WHERE class.class = $1';
            
            // Выполним запрос с передачей имени роли
            $result = pg_query_params($conn, $query, array($classForStudent));
            
            // Проверим, есть ли результат
            if ($result && pg_num_rows($result) > 0) {
                // Получим ассоциативный массив из результата
                $row = pg_fetch_assoc($result);
                return (int)$row['class_id']; // Вернем идентификатор роли как целое число
            } else {
                // Если роль не найдена, можно вернуть null или обработать ошибку
                return null; // Или выбросить исключение, если это необходимо
            }
        }

        function getRoleId($conn, $role) {
            // Подготовим SQL-запрос для получения идентификатора роли
            $query = 'SELECT role_id FROM role WHERE role.role = $1';
            
            // Выполним запрос с передачей имени роли
            $result = pg_query_params($conn, $query, array($role));
            
            // Проверим, есть ли результат
            if ($result && pg_num_rows($result) > 0) {
                // Получим ассоциативный массив из результата
                $row = pg_fetch_assoc($result);
                return (int)$row['role_id']; // Вернем идентификатор роли как целое число
            } else {
                // Если роль не найдена, можно вернуть null или обработать ошибку
                return null; // Или выбросить исключение, если это необходимо
            }
        }
        
        // Проверка существования пользователя с таким email
        $checkEmailQuery = 'SELECT email FROM student WHERE email = $1 UNION SELECT email FROM parent WHERE email = $1 UNION SELECT email FROM tutor WHERE email = $1';
        $result = pg_query_params($conn, $checkEmailQuery, array($email));
        
        if ($result === false) {
            echo "Ошибка выполнения запроса: " . pg_last_error($conn);
        } elseif (pg_num_rows($result) > 0) {
            echo "Ошибка: пользователь с таким email уже существует.";
        } else {
            // Добавляем пользователя в соответствующую таблицу
            if ($role == 'student') {
                // Получите идентификатор роли
                $classId = getClassId($conn, $classForStudent);
                $roleId = getRoleId($conn, $role); // Реализуйте эту функцию для получения идентификатора роли
                $insertQuery = 'INSERT INTO student (surname, firstname, patronymic, email, password, role, class) VALUES ($1, $2, $3, $4, $5, $6, $7)';
                $result = pg_query_params($conn, $insertQuery, array($surname, $firstname, $patronymic, $email, $hashedPassword, $roleId, $classId));
            } elseif ($role == 'parent') {
                // Получите идентификатор роли
                $roleId = getRoleId($conn, $role);
                $insertQuery = 'INSERT INTO parent (surname, firstname, patronymic, email, password, role) VALUES ($1, $2, $3, $4, $5, $6)';
                $result = pg_query_params($conn, $insertQuery, array($surname, $firstname, $patronymic, $email, $hashedPassword, $roleId));
            } elseif ($role == 'tutor') {
                // Получите идентификатор роли
                $roleId = getRoleId($conn, $role);
                $insertQuery = 'INSERT INTO tutor (surname, firstname, patronymic, email, password, role) VALUES ($1, $2, $3, $4, $5, $6)';
                $result = pg_query_params($conn, $insertQuery, array($surname, $firstname, $patronymic, $email, $hashedPassword, $roleId));
            }
            
            if ($result) {
                header("Location: confirmation.php");
                exit();
                //echo "<p>Данные успешно сохранены!</p>";
            } else {
                echo "<p>Ошибка при сохранении данных: " . pg_last_error($conn) . "</p>";
            }
        }
        pg_close($conn);
    }
}
ob_end_flush(); 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 30px;
        }
        .registration-form {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .registration-form label {
            display: block;
            margin-bottom: 10px;
        }
        .registration-form h2 {
            text-align: center;
        }
        .registration-form input[type="text"], 
        .registration-form input[type="email"], 
        .registration-form input[type="password"], 
        .registration-form select {
            width: 98%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .registration-form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
        }
        .registration-form input[type="submit"]:hover {
            background-color: #178044;
        }
        .additional-fields {
            display: none; /* Скрываем дополнительные поля по умолчанию */
        }
    </style>
    <script>
        function toggleAdditionalFields() {
            var role = document.getElementById("role").value;
            var parentFields = document.getElementById("parentFields");
            var tutorFields = document.getElementById("tutorFields");
            var studentFields = document.getElementById("studentFields");

            // Скрыть все дополнительные поля
            parentFields.style.display = "none";
            tutorFields.style.display = "none";
            studentFields.style.display = "none";

            // Показать поля в зависимости от выбранной роли
            if (role === "parent") {
                parentFields.style.display = "block";
            } else if (role === "tutor") {
                tutorFields.style.display = "block"; 
            } else if (role === "student") {
                studentFields.style.display = "block";            
            }
        }
        
    </script>
</head>
<body>
    <div class="registration-form">
        <h2>Регистрация</h2>
        <form id="registrationForm" action="" method="post">
            <label for="role">Кто вы?</label>
            <select id="role" name="roleName" onchange="toggleAdditionalFields()" required>
                <option value="">--Выберите--</option>
                <option value="parent">Родитель</option>
                <option value="tutor">Тьютор</option>
                <option value="student">Ученик</option>
            </select>

            <label for="surname">Фамилия:</label>
            <input type="text" id="surname" name="surname" required>

            <label for="firstname">Имя:</label>
            <input type="text" id="firstname" name="firstname" required>

            <label for="patronymic">Отчество:</label>
            <input type="text" id="patronymic" name="patronymic" required>

            <label for="email">Электронная почта:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>

           <div id="parentFields" class="additional-fields">
                <!-- <label for="childName">Имя ребенка:</label>
                <input type="text" id="childName" name="childName" required>
                <label for="class">Класс:</label>
                <select id="class" name="classForParent" required>
                    <option value="5 класс">5 класс</option>
                    <option value="6 класс">6 класс</option>
                    <option value="7 класс">7 класс</option>
                    <option value="8 класс">8 класс</option>
                    <option value="9 класс">9 класс</option>
                </select>-->
            </div>

            <div id="tutorFields" class="additional-fields">
                <!-- Нет дополнительных полей для тьютора -->
            </div>

            <div id="studentFields" class="additional-fields">
                <label for="classForStudent">Класс:</label>
                <select id="classForStudent" name="classForStudent" required>
                    <option value="5 класс">5 класс</option>
                    <option value="6 класс">6 класс</option>
                    <option value="7 класс">7 класс</option>
                    <option value="8 класс">8 класс</option>
                    <option value="9 класс">9 класс</option>
                </select>
            </div>

            <input type="submit" value="Зарегистрироваться">
        </form>
    </div>
</body>
</html>