# Laravel Api Auth

## Вступление
> Laravel пакет для быстрого развертывания авторизацию.

## Todo
- [ ] нужно для всех контроллеров повесить событий (до, после и во время)
- [ ] lang files
- [ ] Обработать exception 
- [ ] Дописать тесты
- [ ] Провести рефакторинг

## Установка
Для установки пакета добавьте эти строчки в файл composer.json:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "name": "future/lara-api-auth",
            "url": "https://github.com/zairbek/lara-api-auth.git"
        }
    ],
}
```
После добавление выполните команду:
```bash
composer require future/lara-auth-api
```

Опционально: для публикации конфигурации
```bash
php artisan vendor:publish --provider="Future\LaraApiAuth\LaraApiAuthServiceProvider"
```

Выполняем миграцию
```bash
php artisan migrate
```
Эта команда создаст ключи шифрования, необходимые для создания токенов безопасного доступа. 
Кроме того, команда создаст клиентов «персональный доступ» и «предоставление пароля», 
которые будут использоваться для генерации токенов доступа.
```bash
php artisan passport:install
```

После выполнения команды passport:install добавьте трейт Laravel\Passport\HasApiTokens 
в вашу модель App\Models\User. Эта черта предоставит вашей модели несколько
вспомогательных методов, которые позволят вам проверить токен
и области аутентифицированного пользователя:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

Наконец, в файле конфигурации вашего приложения config/auth.php 
вы должны установить для параметра драйвера (driver) защиты аутентификации api значение passport. 
Это укажет вашему приложению использовать TokenGuard Passport при аутентификации входящих запросов API:
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
]
```