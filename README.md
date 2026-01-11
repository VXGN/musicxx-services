## Musicxx-services
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![GitHub pull requests](https://img.shields.io/github/issues-pr-closed-raw/VXGN/musicxx-services)](https://github.com/VXGN/musicxx-services/pulls?q=is%3Apr+is%3Aclosed)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)


Nama Proyek: Musicxx-services

Deskripsi Singkat:
Musicxx-services adalah API RESTful berbasis Laravel untuk manajemen musik yang mendukung autentikasi JWT, manajemen artis, lagu, album, serta playlist. Dirancang untuk memudahkan publisher mengunggah konten dan listener mendengarkan musik serta membuat playlist.

## TOC
[cara-menjalankan-sistem](#cara-menjalankan-sistem)  
[informasi-akun-uji-coba-contoh](#informasi-akun-uji-coba-contoh)  
[dokumentasi-api](#dokumentasi-api)  
[endpoint-api](#endpoint-api)  
[struktur-fitur-singkat](#struktur-fitur-singkat)  
[lisensi](#lisensi)

## Cara Menjalankan Sistem

1. Clone repository
```bash
	git clone https://github.com/VXGN/musicxx-services.git
	cd musicxx-services
```
2. Install dependency
```bash
	composer install
	npm install
```
3. Salin file konfigurasi environment
```bash
	cp .env.example .env
```
4. Konfigurasi `.env`

5. Jalankan migration dan seeder (opsional)
```bash
	php artisan migrate:fresh
	php artisan db:seed
```
6. Jalankan server (Laravel)
```bash
	php artisan serve
```

---

## Informasi Akun Uji Coba (Contoh)

Gunakan akun berikut untuk pengujian cepat (jika belum dibuat, gunakan seeder atau registrasi):

- Publisher (contoh):
  + Email: publisher@example.com
  + Password: password123

## Informasi Akun Uji Coba (Contoh)

Gunakan akun berikut untuk pengujian cepat (jika belum dibuat, gunakan seeder atau registrasi):

- Publisher (contoh):
  + Email: publisher@example.com
  + Password: password123

---

## Dokumentasi API

**Swagger UI (visual): http://localhost:8000/swagger**


### Endpoint API
## Dokumentasi API

**Swagger UI (visual): http://localhost:8000/swagger**


### Endpoint API

### Auth
- `POST /api/register` — Registrasi
- `POST /api/login` — Login
- `POST /api/logout` — Logout (perlu autentikasi)
- `POST /api/refresh` — Refresh token JWT
- `GET /api/me` — Info pengguna saat ini

### Artist
- `GET /api/artists` — Daftar semua artis
- `GET /api/artists/{id}` — Detail artis
- `POST /api/artists` — Buat profil artis (terhubung ke user)
- `PUT /api/artists/{id}` — Update profil artis sendiri
- `DELETE /api/artists/{id}` — Hapus profil artis sendiri

### Album
- `GET /api/albums` — Daftar semua album
- `GET /api/albums/{id}` — Detail album
- `POST /api/albums` — Buat album (terhubung ke artis sendiri)
- `PUT /api/albums/{id}` — Update album sendiri
- `DELETE /api/albums/{id}` — Hapus album sendiri

### Song
- `GET /api/songs` — Daftar semua lagu
- `GET /api/songs/{id}` — Detail lagu
- `POST /api/songs` — Buat lagu (terhubung ke artis sendiri, opsional album)
- `PUT /api/songs/{id}` — Update lagu sendiri
- `DELETE /api/songs/{id}` — Hapus lagu sendiri

### Playlist
- `GET /api/playlists` — Daftar playlist user
- `GET /api/playlists/{id}` — Detail playlist
- `POST /api/playlists` — Buat playlist
- `PUT /api/playlists/{id}` — Update playlist
- `DELETE /api/playlists/{id}` — Hapus playlist
- `POST /api/playlists/{id}/songs` — Tambah lagu ke playlist
- `DELETE /api/playlists/{id}/songs/{songId}` — Hapus lagu dari playlist

---

## Struktur Fitur Singkat

- Autentikasi pengguna (JWT)
- Peran pengguna: `listener`, `publisher`
- Publisher dapat membuat profil artis, album, dan lagu (upload file)
- User dapat membuat playlist dan menambahkan lagu
- Logging permintaan API

---
[![Contributor Profile](https://contrib.rocks/image?repo=VXGN/musicxx-services)](https://github.com/VXGN/musicxx-services)

## Lisensi
Framework Laravel adalah open-source di bawah [LICENSE](LICENSE) MIT. 

## Struktur Fitur Singkat

- Autentikasi pengguna (JWT)
- Peran pengguna: `listener`, `publisher`
- Publisher dapat membuat profil artis, album, dan lagu (upload file)
- User dapat membuat playlist dan menambahkan lagu
- Logging permintaan API

---
[![Contributor Profile](https://contrib.rocks/image?repo=VXGN/musicxx-services)](https://github.com/VXGN/musicxx-services)

## Lisensi
Framework Laravel adalah open-source di bawah [LICENSE](LICENSE) MIT. 
