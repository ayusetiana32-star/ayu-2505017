# Deadlineku

Aplikasi Laravel untuk mengelola tugas kuliah dan deadline, termasuk pengingat email, notifikasi dalam aplikasi, dan kalender deadline.

## Persyaratan

- PHP 8.2 atau lebih baru dengan ekstensi PDO SQLite
- Composer
- Node.js dan npm

## Instalasi

1. Pasang dependency PHP:

   ```bash
   composer install
   ```

2. Salin konfigurasi environment jika file `.env` belum ada:

   ```powershell
   Copy-Item .env.example .env
   ```

   Di macOS/Linux, gunakan `cp .env.example .env`. Konfigurasi contoh memakai SQLite dan `MAIL_MAILER=log`.

3. Buat app key dan siapkan database:

   ```bash
   php artisan key:generate
   php artisan migrate
   ```

4. Pasang dan build asset frontend:

   ```bash
   npm install
   npm run build
   ```

## Menjalankan aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Buka URL yang ditampilkan Artisan, lalu buat akun melalui halaman registrasi. Untuk pengembangan frontend dengan hot reload, jalankan `npm run dev` di terminal terpisah.

Kalender tersedia di `/calendar` dan memuat FullCalendar dari jsDelivr CDN, sehingga browser perlu akses internet. Event diambil dari `GET /tasks/events` dan hanya berisi tugas milik pengguna yang sedang login.

Untuk mengirim pengingat deadline secara berkala, jalankan scheduler di terminal lain:

```bash
php artisan schedule:work
```

Command pengingat juga bisa dijalankan manual:

```bash
php artisan tasks:send-reminders
```

Pengingat dikirim pada H-3, H-1, dan hari H untuk tugas yang belum selesai. Dengan `MAIL_MAILER=log`, email tidak dikirim sungguhan dan dapat dilihat di `storage/logs/laravel.log`. Notifikasi juga tersimpan di database dan muncul di ikon lonceng.

## Pengujian

Jalankan seluruh feature dan unit test dengan:

```bash
php artisan test
```

Test menggunakan SQLite in-memory dan tidak memerlukan database development.
