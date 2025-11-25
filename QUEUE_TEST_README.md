# Queue Test API

## Описание

Этот проект содержит простой API endpoint для тестирования очередей Laravel.

## Компоненты

### 1. Job: `LogRequestJob`
- **Расположение**: `app/Jobs/LogRequestJob.php`
- **Функция**: Логирует данные запроса в лог файл
- **Данные**: Сохраняет метод, URL, IP, user agent и payload запроса

### 2. Controller: `QueueTestController`
- **Расположение**: `app/Http/Controllers/Api/QueueTestController.php`
- **Метод**: `dispatch(Request $request)`
- **Функция**: Отправляет job в очередь с задержкой в 1 секунду

### 3. Route
- **Endpoint**: `POST /api/queue-test`
- **Имя маршрута**: `api.queue-test`

## Использование

### 1. Запустить queue worker

```bash
sail artisan queue:work
```

### 2. Отправить тестовый запрос

```bash
curl -X POST http://localhost/api/queue-test \
  -H "Content-Type: application/json" \
  -d '{"test_key": "test_value", "number": 123}'
```

### 3. Проверить логи

```bash
sail artisan tail
# или
tail -f storage/logs/laravel.log
```

## Ответ API

```json
{
  "message": "Job dispatched successfully",
  "data": {
    "method": "POST",
    "url": "http://localhost/api/queue-test",
    "ip": "172.18.0.1",
    "user_agent": "curl/7.68.0",
    "payload": {
      "test_key": "test_value",
      "number": 123
    }
  },
  "queued_at": "2025-11-25 17:21:49"
}
```

## Тестирование

Запустить тест:

```bash
sail test --filter=QueueTestControllerTest
```

## Примечания

- Job выполняется с задержкой в 1 секунду после отправки в очередь
- Используется драйвер очереди `database` (настроено в `.env`)
- Логи записываются с уровнем `info` и содержат все данные запроса
