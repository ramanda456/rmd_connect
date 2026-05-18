# RMD Connect
## Deskripsi

RMD Connect adalah aplikasi chat realtime berbasis web menggunakan Laravel dan WebSocket. 

# Fitur Utama
* Login & Register
* Dashboard daftar pengguna
* Private chat realtime
* Status online/offline realtime
* Riwayat pesan tersimpan di database

# Teknologi yang Digunakan

## Backend
* PHP 8+
* Laravel 12
* Laravel Reverb
* MySQL
## Frontend
* Bootstrap 5
* JavaScript
* Laravel Echo
* Pusher JS

# Cara Install

## 1. Clone Repository

```bash
git clone https://github.com/ramanda456/rmd_connect.git
cd rmd_connect
```

## 2. Install Dependency

```bash
composer install
npm install
```

## 3. Copy File .env

```bash
copy .env.example .env
```

## 4. Generate Key

```bash
php artisan key:generate
```

## 5. Buat Database

Buat database bernama:

```text
rmd_connect
```

Atur koneksi database di file `.env`.

## 6. Jalankan Migration

```bash
php artisan migrate
```

## 7. Install Broadcasting

```bash
php artisan install:broadcasting
```

Pilih:

```text
reverb
```

## 8. Install Echo & Pusher

```bash
npm install --save-dev laravel-echo pusher-js
```

---

# Menjalankan Aplikasi

Jalankan 3 terminal:

## Terminal 1

```bash
php artisan serve
```

## Terminal 2

```bash
php artisan reverb:start
```

## Terminal 3

```bash
npm run dev
```

