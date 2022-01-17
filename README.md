# console_import_csv
Импорт данных через консольную команду
из csv файла в бд
<h1>Установка</h1>

1. git clone https://github.com/maximsustavov/console_import_csv.git

2. cd console_import_csv

3. composer install

4. измените файл настроек .env

5. php artisan migrate:fresh

<h1>Запуск</h1>

файл импорта находится в "storage/import/random.csv"

консольная команда для запуска

php artisan import:csv
