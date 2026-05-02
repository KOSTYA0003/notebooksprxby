# 💻 Интернет магазин ноутбуков

**🔗 Демо версия:** [https://notebooks.prx.by/](https://notebooks.prx.by/)

## 📖 Описание проекта

Интернет магазин ноутбуков — это учебный интернет-магазин ноутбуков, данные для которого были получены с помощью парсера с сайта 21vek.by. Проект демонстрирует создание полноценного каталога с системой фильтрации, пагинацией, корзиной и формой заказа.

## 🚀 Основной функционал

### 🔄 Парсинг данных

- **Пагинация** — обход всех страниц каталога ноутбуков
- **Кэширование** — сохранение HTML-страниц в БД для повторного использования
- **Извлечение информации** — бренд, название, артикул, цена, рейтинг, количество отзывов
- **Галерея изображений** — сбор ссылок на фото с заменой превью на full-size
- **Характеристики** — группировка по разделам (процессор, память, экран и т.д.)

### 📝 Отзывы

- **Сбор отзывов** — из HTML и AJAX-запросов (JSON API 21vek.by)
- **Пагинация отзывов** — автоматическая подгрузка через offset/limit
- **Структура отзыва** — имя, рейтинг, достоинства, недостатки, резюме, ответ магазина

### 🛒 Интернет-магазин

- **Каталог** — вывод товаров с пагинацией
- **Фильтрация** — по брендам и характеристикам (динамическое построение запросов)
- **Сортировка** — по цене (дешевле/дороже), популярности, рейтингу
- **Корзина** — Livewire-компоненты (добавление, изменение количества, удаление)
- **Оформление заказа** — форма с валидацией

### 🔄 Фоновые задачи

- **Обновление счетчиков отзывов** — команда для синхронизации `reviews_count` у товаров

## 🛠️ Технологический стек

| Технология | Версия | Назначение |
|------------|--------|------------|
| **PHP** | ^8.2 | Язык программирования |
| **Laravel** | ^12.0 | Основной PHP-фреймворк |
| **Livewire** | ^4.1 | Реактивные компоненты (корзина, фильтры) |
| **Symfony DomCrawler** | ^7.4 | Парсинг HTML / извлечение данных |
| **GuzzleHTTP** | (встроен) | HTTP-запросы с User-Agent и таймаутами |
| **Tailwind CSS** | ^4.0 | Стилизация интерфейса |
| **Vite** | ^7.0 | Сборка фронтенд-ресурсов |
| **MySQL / MariaDB** | 10.11+ | Реляционная база данных |
| **Redis** | 7.2+ | Кэш и сессии (рекомендуется) |
| **Docker** | - | Контейнеризация среды разработки |

## 📦 Установка и запуск (Docker)

Это рекомендуемый способ запуска. Локальная установка PHP, Node.js или MySQL не требуется.

### 1. Клонирование и настройка окружения

```bash
git clone https://github.com/KOSTYA0003/notebooksprxby.git
cd notebooksprxby
cp .env.example .env
```

*Убедитесь, что в `.env` указаны настройки для Docker: `DB_HOST=laptop-db`, `DB_PASSWORD=root`, `DB_DATABASE=laptop_parser`.*

### 2. Сборка и запуск контейнеров
```bash
docker-compose up -d --build
```

### 3. Настройка приложения внутри контейнера
```bash
docker exec -it laptop-app composer install
docker exec -it laptop-app php artisan key:generate
docker exec -it laptop-app php artisan storage:link
```

### 4. Сборка фронтенда (Vite + Tailwind 4)
```bash
docker exec -it laptop-app npm install --force
docker exec -it laptop-app npm run build
```

### 5. Подготовка базы данных
```bash
docker exec -it laptop-app php artisan migrate:fresh --seed
```

### 🌐 Доступ к проекту:
- **Сайт:** [http://localhost:8084](http://localhost:8084)
- **База данных (phpMyAdmin):** [http://localhost:8085](http://localhost:8085) 
  *(Сервер: `laptop-db`, Логин: `root`, Пароль: `root`)*

## 🚀 Запуск парсинга

1. Сбор ссылок на товары (пагинация)

```bash
docker exec -it laptop-app php artisan parse:21vek
```

2. Извлечение данных и сохранение в БД

```bash
docker exec -it laptop-app php artisan app:extract-notebooks
```

3. Сбор отзывов

```bash
docker exec -it laptop-app php artisan app:parse-reviews
```

4. Обновление счетчиков отзывов

```bash
docker exec -it laptop-app php artisan app:update-reviews
```

## 📁 Структура ключевых команд

| Команда | Назначение |
|---------|------------|
| `parse:21vek` | Обход пагинации, сбор URL товаров, кэширование страниц |
| `app:extract-notebooks` | Извлечение данных из кэша, сохранение товаров, характеристик, фото |
| `app:parse-reviews` | Сбор отзывов из HTML и JSON-API (с пагинацией) |
| `app:update-reviews` | Синхронизация количества отзывов в таблице `products` |

### 🖼️ Интерфейс / Screenshots

**Витрина товаров**
![Main Catalog](screenshots/main_catalog.png)

**Динамические фильтры**
![Filters](screenshots/filtering_logic.png)

**Характеристики и отзывы**
![Product Details](screenshots/product_details.png)

**Корзина (Livewire)**
![Cart](screenshots/shopping_cart.png)

## 📄 Лицензия
MIT
