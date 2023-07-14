# Bookly 
### [Video review](https://youtu.be/4flQbZZ-VVo)

## Technologies

* PHP 8.1
* [Laravel 9](https://laravel.com)
* [Livewire](https://github.com/livewire/livewire)


## Install

Set your .env vars:
```bash
cp .env.example .env
```

Emails processing .env settings (you can use [mailtrap](https://mailtrap.io/) or your smtp credentials like user@gmail.com):
```dotenv
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_USERNAME=<mailtrap_key>
MAIL_PASSWORD=<mailtrap_password>
MAIL_PORT=587
MAIL_FROM_ADDRESS=admin@thread.com
MAIL_FROM_NAME="BSA Thread Admin"
```

Set book search engine .env settings (add API keys .env):
```dotenv
GOOGLE_BOOKS_KEY=
ISBN_BOOKS_KEY=

SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=

CUSTOMERIO_SITE_KEY=
CUSTOMERIO_API_KEY=
```

Install composer dependencies and generate app key:
```bash
composer install
php artisan key:generate
```

Install and generate styles/scripts:
```bash
npm i
npm run build
```

Database migrations install (set proper .env vars)
```bash
php artisan migrate
```

Seeding the database
```bash
php artisan db:seed
```

Run async queue (to handle background processes)
```bash
php artisan queue:work
```
____
Set "reserved" usernames you can in `config/reserved-usernames.php`    
Set the book search engine you can in `config/books.php`
```dotenv
'search_service' => 'db'
```


For testing locally (application should be ready on `http://127.0.0.1:8000`)
```bash
php artisan serve
```


