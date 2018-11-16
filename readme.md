Помогите :(

## Установка

```
composer update
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build-prod
```

## Обновление

```
composer install
npm run build-prod
```
