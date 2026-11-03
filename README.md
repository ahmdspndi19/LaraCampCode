# CampCode

Aplikasi web **checkout bootcamp** berbasis Laravel. Pengguna dapat melihat paket bootcamp, mendaftar, melakukan checkout, dan melihat invoice. Admin mengelola status pembayaran dari dashboard khusus.

> Pembayaran pada proyek ini **disimulasikan**: tidak ada payment gateway. Admin mengonfirmasi pembayaran secara manual.

## Fitur

**Pengguna**
- Halaman utama berisi daftar paket bootcamp beserta benefit dan harga.
- Registrasi, login, dan logout (dengan *rate limiting* pada login, registrasi, dan checkout).
- Checkout paket bootcamp dengan validasi data kartu.
- Dashboard berisi riwayat checkout dan status pembayaran.
- Invoice per transaksi (nomor `INV-00001`, dst.). Hanya pemilik transaksi dan admin yang dapat membukanya.
- Email notifikasi setelah checkout (menunggu pembayaran) dan setelah pembayaran diterima.

**Admin**
- Dashboard ringkasan: jumlah transaksi per status dan total pendapatan.
- Filter berdasarkan status dan pencarian berdasarkan nama/email pengguna, dengan paginasi.
- Mengubah status tagihan `waiting` menjadi `paid` atau `canceled`.

**Keamanan dan integritas data**
- Nomor kartu penuh dan CVC **tidak disimpan**, hanya 4 digit terakhir.
- Data kartu tidak ikut di-flash ke session saat validasi gagal.
- `amount` pada checkout adalah *snapshot* harga saat transaksi, jadi invoice lama tidak berubah walau harga paket diubah.
- Satu pengguna hanya bisa punya satu tagihan aktif (`waiting`/`paid`) per paket.
- Perubahan status oleh admin memakai *row locking* agar tidak terjadi perubahan bersamaan.
- `is_admin` tidak *mass-assignable*.
- Soft delete pada `users`, `camps`, dan `checkouts`.

## Teknologi

| Komponen | Keterangan |
|---|---|
| PHP | ^8.2 |
| Framework | Laravel ^12.0 |
| Database | SQLite (default), dapat diganti MySQL lewat `.env` |
| Frontend | Blade, Bootstrap 5.1 (CDN), font Poppins |
| Session, cache, queue | Driver `database` |
| Email | Driver `log` (default) |
| Testing | PHPUnit ^11.5 |
| Code style | Laravel Pint |

## Prasyarat

- PHP 8.2 atau lebih baru (ekstensi `pdo_sqlite`/`sqlite3` aktif)
- Composer
- Node.js dan npm (opsional, hanya untuk aset Vite/Tailwind di `resources/`)

## Instalasi

```bash
# 1. Pasang dependensi
composer install

# 2. Siapkan environment
cp .env.example .env
php artisan key:generate

# 3. Siapkan database SQLite
touch database/database.sqlite

# 4. Jalankan migrasi dan seeder
php artisan migrate --seed

# 5. Jalankan server
php artisan serve
```

Buka `http://localhost:8000`.

Alternatif cepat: `composer setup` menjalankan instalasi, penyalinan `.env`, `key:generate`, migrasi, serta `npm install` dan `npm run build`. Setelah itu jalankan `php artisan db:seed` untuk mengisi data awal.

Untuk mode pengembangan (server, queue, log, dan Vite sekaligus): `composer dev`.

> Pengguna XAMPP: letakkan proyek di `htdocs` atau jalankan `php artisan serve`, dan pastikan versi PHP XAMPP minimal 8.2.

## Konfigurasi

Konfigurasi utama ada di `.env` (salinan dari `.env.example`):

| Variabel | Default | Keterangan |
|---|---|---|
| `APP_NAME` | `Laravel` | Ubah menjadi `CampCode` |
| `APP_URL` | `http://localhost` | Sesuaikan dengan URL aplikasi |
| `DB_CONNECTION` | `sqlite` | Untuk MySQL isi juga `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| `SESSION_DRIVER` | `database` | Memerlukan tabel `sessions` (dibuat oleh migrasi) |
| `QUEUE_CONNECTION` | `database` | |
| `MAIL_MAILER` | `log` | Email ditulis ke `storage/logs/laravel.log`. Ganti ke `smtp` untuk mengirim email sungguhan |

Contoh konfigurasi MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campcode
DB_USERNAME=root
DB_PASSWORD=
```

## Akun Demo

Dibuat oleh `DatabaseSeeder`:

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@campcode.test` | `password` |
| Pengguna | `user@campcode.test` | `password` |

> **Penting:** akun ini hanya untuk pengembangan. Ganti password sebelum deploy.

Seeder juga membuat dua paket bootcamp:

| Paket | Slug | Harga |
|---|---|---|
| Gila Belajar | `gila-belajar` | Rp 280.000 |
| Baru Mulai | `baru-mulai` | Rp 140.000 |

`CampSeeder` bersifat idempoten, jadi aman dijalankan berulang kali tanpa menggandakan data.

## Alur Penggunaan

1. Pengguna membuka halaman utama dan memilih paket.
2. Pengguna login atau mendaftar, lalu mengisi form checkout (nama, pekerjaan, data kartu).
3. Sistem membuat transaksi berstatus `waiting` dan mengirim email "menunggu pembayaran".
4. Admin membuka dashboard admin dan mengubah status menjadi `paid` (email "pembayaran diterima" dikirim) atau `canceled`.
5. Pengguna melihat status dan invoice di dashboard.

## Struktur Database

```
users (id, name, email, password, avatar, occupation, is_admin, ...)
  └── checkouts (id, user_id, camp_id, card_last_four, amount, expired,
  │              payment_status, paid_at, ...)
camps (id, title, slug, description, price, ...)
  ├── camp_benefits (id, camp_id, name)
  └── checkouts
```

Relasi:
- `User` memiliki banyak `Checkout`.
- `Camp` memiliki banyak `CampBenefit` dan banyak `Checkout`.
- `Checkout` milik satu `User` dan satu `Camp`.

Nilai `payment_status`: `waiting`, `paid`, `canceled` (enum `App\Enums\PaymentStatus`).

Migrasi `harden_checkouts_table` memindahkan data lama dengan aman: 4 digit terakhir diambil dari `card_number`, lalu kolom `card_number` dan `cvc` dihapus.

## Daftar Route

| Method | URI | Nama | Akses |
|---|---|---|---|
| GET | `/` | `welcome` | Publik |
| GET/POST | `/login` | `login`, `login.store` | Guest (throttle 5/menit) |
| GET/POST | `/register` | `register`, `register.store` | Guest (throttle 10/menit) |
| POST | `/logout` | `logout` | Login |
| GET | `/checkout/{camp:slug}` | `checkout` | Login |
| POST | `/checkout/{camp:slug}` | `checkout.store` | Login (throttle 10/menit) |
| GET | `/checkout/success` | `success_checkout` | Login |
| GET | `/dashboard` | `dashboard` | Login |
| GET | `/invoices/{checkout}` | `invoice` | Pemilik transaksi atau admin |
| GET | `/admin/dashboard` | `admin.dashboard` | Admin |
| PATCH | `/admin/checkouts/{checkout}` | `admin.checkout.update` | Admin |

## Struktur Folder

```
app/
├── Enums/PaymentStatus.php
├── Http/
│   ├── Controllers/        # Auth, Home, Checkout, Dashboard, Invoice
│   │   └── Admin/          # CheckoutController (dashboard admin)
│   ├── Middleware/AdminMiddleware.php
│   └── Requests/           # Login, Register, StoreCheckout
├── Mail/Checkout/          # AfterCheckout, Paid
└── Models/                 # User, Camp, CampBenefit, Checkout
database/
├── factories/
├── migrations/
└── seeders/                # DatabaseSeeder, CampSeeder
resources/views/
├── admin/                  # Dashboard admin
├── components/             # navbar, flash, form-field
├── emails/checkout/        # Template email
├── layout/                 # app, auth
└── ...                     # welcome, login, register, checkout, dashboard, invoice
routes/web.php
```

## Pengujian

```bash
composer test
```

## Catatan

- Halaman memakai stylesheet dan gambar dari folder `public/` (`public/css/main.css`, `public/css/campcode.css`, `public/images/`). Pastikan folder ini ikut disertakan di repositori.
- Berkas `resources/css/app.css` dan `resources/js/` merupakan bawaan Laravel (Vite + Tailwind). Layout saat ini tidak memuatnya lewat `@vite`.
- Email dikirim secara sinkron. Kegagalan pengiriman dicatat ke log dan tidak menggagalkan checkout.

## Lisensi

Proyek ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT), mengikuti kerangka Laravel.
