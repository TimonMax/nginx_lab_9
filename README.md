# Лабораторная работа №8: Тестирование PHP-приложения с использованием PHPUnit и Guzzle


## 👩‍💻 Автор
**ФИО:** Фаткин Артем Александрович  
**Группа:** 2ПМ-1  

---

## 📌 Описание задания
Научиться
1. настраивать CI/CD pipeline
2. использовать GitHub Actions
3. запускать Docker-контейнеры в CI
4. автоматически запускать тесты PHPUnit
5. выявлять ошибки через CI

http://localhost:8080

---

## ⚙️ Как запустить проект

### 1. Клонировать репозиторий
```bash
git clone https://github.com/TimonMax/nginx_lab_9.git
cd nginx_lab_9
```
### 2. Запустить контейнеры Docker
```bash
docker-compose up -d --build
```
### 3. Открыть в браузере
```bash
http://localhost:8080
```
### 4. Открыть панель RabbitMQ (Логин и пароль одинаковы: guest)
```bash
http://localhost:15672
```
### 5. Запустить worker для обработки сообщений
```bash
docker exec -it lab8_php php www/worker.php
```
### 6. Запустить тесты
```bash
vendor/bin/phpunit --bootstrap tests/bootstrap.php tests
```
### Содержимое .env.test
```bash
DB_HOST=db
DB_NAME=test_db
DB_USER=test_user
DB_PASSWORD=test_pass
APP_URL=http://localhost:8080
```
### Результат тестирования
```bash
PHPUnit 11.5.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.28

......                                                              6 / 6 (100%)

Time: 00:00.211, Memory: 10.00 MB

OK (6 tests, 16 assertions)
```
### Проверка работы
Чтобы убедиться, что CI действительно отслеживает ошибки, можно специально сломать тест, например заменить:
```bash
$this->assertEquals(2, 1 + 1);
```
най
```bash
$this->assertEquals(2, 1 + 2);
```
после чего сделать коммит (в целом необязательно, так как в репозитории уже есть различные примеры того, как отлавливаются ошибки от неправильно написанного CI до прохождения тестов с ошибками)
## Содержимое проекта
```docker-compose.yml``` — описание сервисов

```Dockerfile``` — параметры для запуска

```www/composer.json``` — зависимости проекта и автозагрузка классов

```www/composer.lock``` — зафиксированные версии зависимостей

```www/db.php``` — константы для ДБ

```www/index.php``` — главная страница

```nginx/default.conf``` — файл для обработки PHP

```www/send.php` — producer: отправка сообщения в очередь RabbitMQ

```www/worker.php``` — consumer: получение и обработка сообщений из очереди

```www/QueueManager.php``` — класс для подключения к RabbitMQ и работы с очередью

```tests/bootstrap``` — загрузка автолодера и .env.test

```tests/ExampleTest.php``` — tests

```tests/VisitRecordTest.php``` — mock-тест с PDO

```tests/HttpTest.php``` — HTTP-тест страницы через Guzzle

```tests/HttpMockTest.php``` — EnvTest.php

```tests/EnvTest.php``` — проверка переменных окружения

```github/workflows/ci.yml``` — CI pipeline для GitHub Actions