# BookingCore — модуль бронирования охотничьих туров

Мини-модуль для ядра **BookingCore**, реализующий API бронирования охотничьих туров с выбором гида.  
Разработан на **Laravel 11**, с акцентом на чистую архитектуру, тестируемость и читаемость кода.

---

## Возможности

- `GET /api/v1/guides` — получить список активных гидов
    - поддерживает фильтр `?min_experience=3`
- `POST /api/v1/bookings` — создать новое бронирование тура

### Бизнес-правила

- Гид должен существовать и быть активным
- Гид не может быть забронирован на ту же дату дважды
- Количество участников ≤ 10
- Дата не может быть в прошлом

---

## Структура проекта

```text
app/
 ├── Contracts/Booking/
 │    └── BookingException.php              # контракт для всех доменных ошибок
 ├── DTO/
 │    ├── GuideFiltersData.php              # DTO для фильтра гидов
 │    └── HuntingBookingData.php            # DTO для создания бронирования
 ├── Exceptions/Booking/
 │    ├── GuideBusyException.php
 │    ├── GuideNotActiveException.php
 │    ├── InvalidParticipantsCountException.php
 │    └── PastDateException.php
 ├── Http/
 │    ├── Controllers/Api/
 │    │    ├── GuideController.php
 │    │    └── HuntingBookingController.php
 │    ├── Requests/
 │    │    ├── Guide/GuideIndexRequest.php
 │    │    └── Booking/StoreHuntingBookingRequest.php
 │    └── Resources/
 │         ├── GuideResource.php
 │         └── HuntingBookingResource.php
 ├── Models/
 │    ├── Guide.php
 │    └── HuntingBooking.php
 └── Services/
      ├── GuideService.php
      └── HuntingBookingService.php
```

## Архитектурные принципы

- **Controller** — минимальный слой, обрабатывает только HTTP-вход и формирует ответ  
- **FormRequest** — отвечает за валидацию данных и сообщения об ошибках  
- **Service** — содержит бизнес-логику и инварианты домена  
- **DTO** — строго описывает структуру входных данных, передаваемых в сервисы  
- **Exception** — определяет доменные ошибки, все реализуют общий интерфейс `BookingException`  
- **Resource** — форматирует API-ответы в единый вид  
- **Test** — разделён на feature (поведение API) и unit (бизнес-логика)

---

## Единый формат ошибок

Все ошибки — валидационные, доменные, 404 и 500 — возвращаются в едином формате JSON:

```json
{
  "status": "fail",
  "message": "Гид уже занят на указанную дату.",
  "errors": {
    "guide_id": ["Гид уже занят на указанную дату."]
  },
  "code": 422
}
```

## Тестирование

**Feature-тесты**

- `tests/Feature/Api/Booking/CreateBookingTest.php` — проверяет создание бронирования, корректность бизнес-правил и сообщений об ошибках
- `tests/Feature/Api/Guide/GuideIndexTest.php` — проверяет получение списка активных гидов и фильтр `min_experience`

**Unit-тесты**

- `tests/Unit/Services/HuntingBookingServiceTest.php` — тестирует бизнес-логику создания брони (валидные и невалидные сценарии)
- `tests/Unit/Services/GuideServiceTest.php` — тестирует логику фильтрации активных гидов

**Запуск тестов**

```bash
./vendor/bin/sail artisan test
```

**Пример ответа**
```bash
  PASS Tests\Unit\ExampleTest
  ✓ that true is true                                                                                                                                                                             0.01s  

   PASS  Tests\Unit\Services\GuideServiceTest
  ✓ it returns only active guides                                                                                                                                                                 0.65s  
  ✓ it applies min experience filter                                                                                                                                                              0.01s  

   PASS  Tests\Unit\Services\HuntingBookingServiceTest
  ✓ it creates booking when guide is active and free                                                                                                                                              0.02s  
  ✓ it throws guide busy exception when same date is already booked                                                                                                                               0.02s  

   PASS  Tests\Feature\Api\Guide\GuideIndexTest
  ✓ it returns only active guides                                                                                                                                                                 0.07s  
  ✓ it filters guides by min experience                                                                                                                                                           0.02s  
  ✓ it returns empty when no guides match filter                                                                                                                                                  0.01s  
  ✓ it ignores inactive guides even with experience                                                                                                                                               0.02s  

   PASS  Tests\Feature\Api\HuntingBooking\CreateBookingTest
  ✓ it creates booking successfully                                                                                                                                                               0.02s  
  ✓ it returns error when guide is not active                                                                                                                                                     0.06s  
  ✓ it returns error when guide is busy on date                                                                                                                                                   0.02s  
  ✓ it returns error for invalid participants count                                                                                                                                               0.02s  
  ✓ it returns error for past date                                                                                                                                                                0.01s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                                                                                                                                 0.03s  

  Tests:    15 passed (59 assertions)
  Duration: 1.08s
```

## Запуск проекта

### Установка зависимостей и окружения (через Laravel Sail)

```bash
#Клонируем проект
git clone https://github.com/kca66y/test-task.git

# Устанавливаем зависимости
composer install

# Копируем конфиг окружения
cp .env.example .env
```
> Важно: Laravel Sail использует Docker.
Для работы требуется установленный Docker Desktop / Docker Engine / php < 8.1

### Запуск окружения через Sail

```bash
# Поднять контейнеры (в фоне)
./vendor/bin/sail up -d

# Сделать ключ приложения
./vendor/bin/sail artisan key:generate

#Если проект разворачивается впервые — выполни миграции и сидеры
./vendor/bin/sail artisan migrate --seed

# Проверить состояние
./vendor/bin/sail ps
```

### Доступ к приложению

После запуска контейнеров приложение будет доступно по адресу:
```text
http://localhost:8000
```
Или можно тыкнуть [сюда](http://localhost:8000).

---

## Примеры работы API

### Успешное бронирование
```bash
curl -X POST http://localhost:8000/api/v1/bookings   -H "Content-Type: application/json"   -d '{
    "tour_name": "Большая охота",
    "hunter_name": "Иван Петров",
    "guide_id": 1,
    "date": "2025-12-01",
    "participants_count": 4
  }'
```

**Response**
```json
{
  "status": "success",
  "data": {
    "id": 42,
    "tour_name": "Большая охота",
    "hunter_name": "Иван Петров",
    "guide_id": 1,
    "date": "2025-12-01",
    "participants_count": 4
  }
}
```

### Ошибка — гид уже занят
```bash
curl -X POST http://localhost:8000/api/v1/bookings   -H "Content-Type: application/json"   -d '{
    "tour_name": "Большая охота",
    "hunter_name": "Иван Петров",
    "guide_id": 1,
    "date": "2025-11-12",
    "participants_count": 4
  }'
```

**Response**
```json
{
  "status": "fail",
  "message": "Гид уже занят на указанную дату.",
  "errors": {
    "guide_id": ["Гид уже занят на указанную дату."]
  },
  "code": 422
}
```

---

## Интеграция в BookingCore

Модуль полностью соответствует архитектуре ядра BookingCore и может быть интегрирован как отдельный подпакет.

**1. Подключение**
- Разместить модуль в `modules/HuntingBooking/`
- Зарегистрировать `BookingModuleServiceProvider` в `config/app.php` или через ядро BookingCore

**2. Роутинг**
```php
Route::apiResource('guides', GuideController::class)->only('index');
Route::apiResource('bookings', HuntingBookingController::class)->only('store');
```

**3. Расширяемость**
- Добавление типов туров — через отдельные DTO и сервисы
- Интеграция с оплатами — через события `BookingCreated`
- Поддержка админки — Nova / Filament
- Поддержка многоязычности — Laravel Lang файлы

---

## Общая информация

- Бизнес-логика: `App\Services\HuntingBookingService`
- DTO: `App\DTO\HuntingBookingData`
- Доменные ошибки: `App\Exceptions\Booking\*`
- Feature-тесты: покрывают все сценарии API
- Unit-тесты: изолируют поведение сервисов
- Единый JSON-формат ошибок демонстрирует системность подхода
- OpenAPI-спецификация и README отражают инженерную культуру

---
Автор: *Александр Бобров*


