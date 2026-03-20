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

```bash
docker compose up -d --build
```

2. Установить зависимости внутри контейнера (если нужно):

```bash
docker compose exec php composer install
```

3. Инициализировать Yii Advanced:

```bash
docker compose exec php php init --env=Development --overwrite=All
```

4. Применить миграции:

```bash
docker compose exec php php yii migrate --interactive=0
```

5. Открыть приложение:

- Главная: `http://localhost:8080`
- Модуль переводчиков: `http://localhost:8080/translators`
- API: `http://localhost:8080/api/availability?type=weekday`

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
