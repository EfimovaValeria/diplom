<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница сайта</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        .email {
            color: #007BFF; /* Цвет ссылки */
            text-decoration: none; /* Убираем подчеркивание */
        }
        .email:hover {
            text-decoration: underline; /* Подчеркивание при наведении */
        }
    </style>
</head>
<body>
    <header>
        <a href="home.php"><img src="logo.png" alt="Logo" class="logo"></a>
        <div class="btn-group">
            <a href="registration.php" class="register-btn">Регистрация</a>
            <a href="entry.php" class="auth-btn">Вход</a>
        </div>
    </header>
    <div class="container">
        <div id="main" class="main-content">
            <h2>Семейное образование</h2>
            <p>Семейное образование — это уникальная возможность создать индивидуальный подход к обучению вашего ребенка. На нашем сайте мы предлагаем программы, ресурсы и поддержку для родителей, которые хотят углубить знания своих детей и развивать их потенциал в комфортной и вдохновляющей обстановке.</p>
            <p>Семейное образование — это не просто альтернатива традиционному обучению, это уникальная возможность создать индивидуальный путь к знаниям для вашего ребенка. Мы верим в то, что каждый ребенок уникален, и его образовательный опыт должен отражать его интересы, способности и темп развития.</p>
            <h3>Наши услуги</h3>
            <ul>
                <li>1. Консультации по выбору образовательных программ</li>
                <li>2. Помощь в организации учебного процесса</li>
                <li>3. Поддержка в поиске ресурсов и материалов для обучения</li>
            </ul>
            <h3>Контакты</h3>
            <p>Если у вас есть вопросы или вы хотите получить дополнительную информацию, свяжитесь с нами:</p>
            <p>Email: <a href="mailto:epfimova.valeria030516@gmail.com" class="email">epfimova.valeria030516@gmail.com</a>, <a href="mailto:2004polyakova0430@gmail.com" class="email">2004polyakova0430@gmail.com</a></p>
            <p>Телефон: +7 (123) 456-78-90</p>
            <h4>График работы</h4>
            <p>Работаем с понедельника по пятницу с 9:00 до 17:00, обед с 13:00 до 14:00.</p>
        </div>
    </div>
    <footer class="footer">
        <button class="back-to-top" onclick="scrollToTop()">Наверх</button>
    </footer>

    <script src="script.js"></script>
</body>
</html>