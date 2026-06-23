## Зависимости
- php 8.5.7
- nginx 1.31.2
- mysql 9.7.1

## Установка для Linux
(В зависимости от дистрибутива операционной системы, пути в конфигурациях и командах могут отличаться)
1. Клонировать проект в директорию веб-сервера:
- `$cd /usr/share/nginx/html`
- `$sudo git clone https://github.com/Apstol/rest-api-test`
2. При необходимости, создать директории в nginx:
- `$sudo mkdir /etc/nginx/sites-available /etc/nginx/sites-enabled`
3. Скопировать конфигурацию веб сервера:
- `$sudo cp nginx/nginx.conf /etc/nginx`
- `$sudo cp nginx/sites-available/tasks-api.conf /etc/nginx/sites-available`
4. Включить сайт, создав символическую ссылку:
- `$sudo ln -s /etc/nginx/sites-available/tasks-api.conf /etc/nginx/sites-enabled/`
5. Переименовать и отредактировать конфигурационный файл базы данных:
- `$mv config/database.example.php config/database.php`
6. При необходимости, назначить пользователя веб-сервера владельцем файлов и применить права к файлам (пользователь может отличаться в зависимости от дистрибутива):
- `$sudo chown -R www-data:www-data /usr/share/nginx/html`
- `$sudo find /usr/share/nginx/html -type d -exec chmod 755 {} \;`
- `$sudo find /usr/share/nginx/html -type f -exec chmod 644 {} \;`
7. Применить миграцию `rest-api-test.sql`
8. Воспользоваться postman коллекцией "Tasks Rest API.postman_collection.json" для обращения к апи
