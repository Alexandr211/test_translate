# Тестовый модуль CRM: переводчики

## Формулировка задания на доработку тест CRM

Необходимо реализовать модуль управления занятостью переводчиков, чтобы CRM могла непрерывно назначать задачи от заказчиков:

- часть переводчиков работает как штатные исполнители в **будние дни** (`weekday`);
- часть переводчиков доступна как подработка в **выходные дни** (`weekend`);
- интерфейс CRM должен позволять выбрать режим работы (будни/выходные) и показать только доступных переводчиков из БД;
- API-модуль должен возвращать одну из фраз:
  - `Список переводчиков готов`
  - `Нет свободных переводчиков`

## Что реализовано

- Yii 2 Advanced проект с docker-окружением.
- Миграции для таблицы `translator` и тестовые данные о занятости.
- Веб-интерфейс с Vue 3 для выбора режима и отображения переводчиков.
- API-эндпоинт для проверки доступности переводчиков.
- API для создания и обновления записей в таблице `translator`.

## Структура проекта

- `common/models/Translator.php` - ActiveRecord модель переводчика.
- `console/migrations/` - миграции создания и наполнения таблицы.
- `frontend/controllers/TranslatorController.php` - страница модуля и JSON-эндпоинт списка.
- `frontend/views/translator/index.php` - UI на Vue.js.
- `frontend/modules/api/` - API-модуль с endpoint доступности.
- `docker-compose.yml` - контейнеры `php`, `nginx`, `mysql`.
- `docker/nginx/default.conf` и `docker/php/Dockerfile` - инфраструктурная конфигурация.

## Локальный запуск

1. Поднять контейнеры:

> В Ubuntu 22.04 может быть доступен `docker-compose` (v1), а не `docker compose` (v2).  
> Если `docker compose` не найден, используйте команды с дефисом (`docker-compose`), как в примерах ниже.

```bash
docker-compose up -d --build
```

2. Установить зависимости внутри контейнера (только если нужно):

Если в проекте уже есть папка `vendor/` (как в текущем репозитории), зависимости будут смонтированы в контейнер, и `composer install` обычно не требуется. Он нужен после `git clone`, если `vendor/` отсутствует, или если вы удалили `vendor/`.

```bash
docker-compose exec -T php composer install
```

3. Инициализировать Yii Advanced (только если нужно):

В репозитории уже есть сгенерированные файлы `*-local.php` (создаются командой `yii init`), поэтому для текущего проекта повторный `php init` не требуется. Шаг нужен после свежего `git clone`, если этих файлов нет.

```bash
docker-compose exec -T php php init --env=Development --overwrite=All
```

4. Применить миграции:

```bash
docker-compose exec -T php php yii migrate --interactive=0
```
Если вам нужно перезапустить вручную:
```bash
cd /home/alexander/project1/test_translate
docker-compose down
docker-compose up -d --build
docker-compose exec -T php php yii migrate --interactive=0
```

Чтобы остановить, но не удалить контейнеры:
```bash
docker-compose stop
```

Потом снова запустить те же контейнеры:
```bash
docker-compose start
```
Войти в translator_php:
```bash
docker exec -it translator_php bash
```

5. Открыть приложение:

- Главная: `http://localhost:8080`
- Модуль переводчиков: `http://localhost:8080/translators`
- API: `http://localhost:8080/api/availability?type=weekday`
- Создать переводчика: `POST http://localhost:8080/api/translator` (body JSON, см. ниже)
- Обновить переводчика: `PUT` / `PATCH` / `POST http://localhost:8080/api/translator/{id}`
- MySQL с хоста: `127.0.0.1:33061` (порт `33060` часто занят локальным `mysql.service`)

## Пример API-ответа

```json
{
  "message": "Список переводчиков готов",
  "type": "weekday"
}
```

или

```json
{
  "message": "Нет свободных переводчиков",
  "type": "weekend"
}
```

### Создание и обновление переводчика (API)

Поля модели: `name`, `employment_type` (`weekday` | `weekend`), `language_pair`, опционально `is_available` (по умолчанию при создании — `true`).

Создать (`POST`):

```bash
curl -s -X POST http://localhost:8080/api/translator \
  -H 'Content-Type: application/json' \
  -d '{"name":"Dima Ivanov","employment_type":"weekday","language_pair":"ru-en","is_available":true}'
```

Обновить (`PUT` или `PATCH`, также допускается `POST` для совместимости):

```bash
curl -s -X PATCH http://localhost:8080/api/translator/1 \
  -H 'Content-Type: application/json' \
  -d '{"is_available":false,"language_pair":"ru-de"}'
```

При ошибке валидации ответ с HTTP `422` и полем `errors`.

## Деплой на боевой сервер (базовый вариант)

1. Подготовить Linux-сервер (Ubuntu) и установить Docker/Compose.
2. Клонировать проект:

```bash
git clone git@github.com:Alexandr211/test_translate.git
cd test_translate
```

3. Запустить контейнеры:

```bash
docker compose up -d --build
```

4. Выполнить миграции:

```bash
docker compose exec php php yii migrate --interactive=0
```

5. Настроить reverse proxy (Nginx/Caddy) и SSL (Let's Encrypt) на домен.
6. Для обновлений:

```bash
git pull
docker compose up -d --build
docker compose exec php php yii migrate --interactive=0
```
