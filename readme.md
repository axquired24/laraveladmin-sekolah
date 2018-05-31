### Sample use of Laravel Admin

##### Instructions

### Pertama, jalankan perintah ini (HARUS URUT)
- copy `.env.example` to `.env`, configure with your DB
- `composer install` | Install Dependencies
- Run `php artisan key:generate` 
- Run `php artisan storage:link` | if failed, remove `public/storage` first.
- Run `php artisan admin:install` | to seed laravel-admin db
- Run `php artisan db:seed` | to seed dummy data

### !HANYA JIKA DATA BROKEN (HARUS URUT)
untuk me-reset dan re-seed database dengan perintah
- Run `php artisan migrate:reset`
- Run `php artisan admin:install` | to seed laravel-admin db
- Run `php artisan db:seed` | to seed dummy data
