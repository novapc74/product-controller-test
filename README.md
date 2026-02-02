### Start

в корне /project создайте .env.local
```
APP_NAME=symfony-test-job
DATABASE_HOST=maria-db
DATABASE_PORT=3306
MYSQL_PASSWORD=dev
MYSQL_USER=dev
MYSQL_DATABASE=dev
MARIADB_ROOT_PASSWORD=root
```
### в консоли выподлните команды:
```
make create_network
make init
make php-cli
composer install
bin/console doctrine:fixtures:load --no-interaction
```
#### Наполнение базы данных (фейковые).
```
bin/console doctrine:fixtures:load --no-interaction
```
#### Тесты
```
php bin/phpunit
```