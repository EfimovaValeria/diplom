document.addEventListener('DOMContentLoaded', function () {
    const subjectMenu = document.getElementById('subject-menu');
    const subjectSelect = document.getElementById('subject-select');
    let selectedCell = null;

    // Закрытие меню при клике вне
    document.addEventListener('click', function (event) {
        if (!subjectMenu.contains(event.target)) {
            subjectMenu.classList.add('hidden');
        }
    });

    // Выбор ячейки для занятия
    const cells = document.querySelectorAll('#schedule-table td:not(.time-column)');
    cells.forEach(cell => {
        cell.addEventListener('mouseenter', function () {
            selectedCell = cell;
            subjectMenu.style.display = 'block';
            subjectMenu.style.top = `${cell.getBoundingClientRect().bottom + window.scrollY}px`;
            subjectMenu.style.left = `${cell.getBoundingClientRect().left + window.scrollX}px`;
            subjectMenu.classList.remove('hidden');
        });
    });

    // Добавление занятия при выборе из выпадающего списка
    subjectSelect.addEventListener('change', function () {
        const selectedSubject = subjectSelect.value;

        if (!selectedSubject || !selectedCell) {
            return; // Если ничего не выбрано или ячейка не выбрана, ничего не делаем
        }

        selectedCell.textContent = selectedSubject; // Заполняем выбранной темой
        subjectMenu.classList.add('hidden'); // Скрываем меню после добавления
        subjectSelect.value = ''; // Сбрасываем выбор
    });

    // Очистка расписания
    document.getElementById('clear-schedule').addEventListener('click', function () {
        const cells = document.querySelectorAll('#schedule-table td:not(.time-column)');
        const confirmation = confirm('Вы уверены, что хотите очистить расписание?');
        if (confirmation) {
            cells.forEach(cell => {
                cell.textContent = ''; // Очищаем содержимое ячеек
            });
            alert('Расписание очищено!');
        }
    });

    // Сохранение расписания
    document.getElementById('save-schedule').addEventListener('click', async function () {
        // Сбор данных с таблицы
        const rows = document.querySelectorAll('#schedule-table tbody tr');
        const scheduleData = [];

        rows.forEach(row => {
            const time = row.querySelector('.time-column').textContent;
            const subjects = row.querySelectorAll('td');

            subjects.forEach((subject, index) => {
                if (subject.textContent) { // Если есть предмет
                    scheduleData.push({
                        time: time,
                        day: index, 
                        subject: subject.textContent
                    });
                }
            });
        });

        // Отправка данных на сервер
        const response = await fetch('save_schedule.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(scheduleData)
        });
     
        if (response.ok) {
            alert('Расписание сохранено!');
        } else {
            const errorText = await response.text(); // Получаем текст ошибки
            console.error('Ошибка сервера:', errorText);
            alert('Ошибка при сохранении расписания! ' + errorText);
        }
    });
});
