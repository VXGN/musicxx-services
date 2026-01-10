## Musicxx-services

---


---

## Table of Contents
- [Fitur](#fitur)
- [Resource Pool](#resource-pool)
- [Endpoint API](#endpoint-api)
- [Instalasi](#instalasi)
- [Penggunaan](#penggunaan)
- [Lisensi](#lisensi)

---

## Fitur
- Autentikasi pengguna (JWT)
- Menggunakan Supabase sebagai penyimpanan file dan database
---

## Resource Pool
- **User**
- **Artist**
- **Album**
- **Song**
- **Playlist**
- **Log**

---

## Endpoint API

### Auth
- `POST /api/register` — Registrasi
- `POST /api/login` — Login
- `POST /api/logout` — Logout (perlu autentikasi)
- `POST /api/refresh` — Refresh token JWT
- `GET /api/me` — Info pengguna saat ini

### Artist (Hanya Publisher)
- `GET /api/artists` — Daftar semua artis
- `GET /api/artists/{id}` — Detail artis
- `POST /api/artists` — Buat profil artis (terhubung ke user)
- `PUT /api/artists/{id}` — Update profil artis sendiri
- `DELETE /api/artists/{id}` — Hapus profil artis sendiri

### Album (Hanya Publisher)
- `GET /api/albums` — Daftar semua album
- `GET /api/albums/{id}` — Detail album
- `POST /api/albums` — Buat album (terhubung ke artis sendiri)
- `PUT /api/albums/{id}` — Update album sendiri
- `DELETE /api/albums/{id}` — Hapus album sendiri

### Song (Hanya Publisher)
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

## Instalasi
- [Fitur](#fitur)
- [Resource Pool](#resource-pool)
- [Endpoint API](#endpoint-api)
- [Instalasi](#instalasi)
- [Penggunaan](#penggunaan)
- [Lisensi](#lisensi)

---

## Fitur
- Autentikasi pengguna (JWT)
- Menggunakan Supabase sebagai penyimpanan file dan database
---

## Resource Pool
- **User**
- **Artist**
- **Album**
- **Song**
- **Playlist**
- **Log**

---

## Endpoint API

### Auth
- `POST /api/register` — Registrasi
- `POST /api/login` — Login
- `POST /api/logout` — Logout (perlu autentikasi)
- `POST /api/refresh` — Refresh token JWT
- `GET /api/me` — Info pengguna saat ini

### Artist (Hanya Publisher)
- `GET /api/artists` — Daftar semua artis
- `GET /api/artists/{id}` — Detail artis
- `POST /api/artists` — Buat profil artis (terhubung ke user)
- `PUT /api/artists/{id}` — Update profil artis sendiri
- `DELETE /api/artists/{id}` — Hapus profil artis sendiri

### Album (Hanya Publisher)
- `GET /api/albums` — Daftar semua album
- `GET /api/albums/{id}` — Detail album
- `POST /api/albums` — Buat album (terhubung ke artis sendiri)
- `PUT /api/albums/{id}` — Update album sendiri
- `DELETE /api/albums/{id}` — Hapus album sendiri

### Song (Hanya Publisher)
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

## Instalasi
```bash
# Clone repository
# Clone repository
git clone https://github.com/VXGN/musicxx-services.git

# Masuk ke direktori proyek
# Masuk ke direktori proyek
cd musicxx-services

# Install dependensi
# Install dependensi
composer install
npm install
```

---

## Penggunaan
- Jalankan migrasi: `php artisan migrate:fresh`
- Mulai server: `php artisan serve`
- Gunakan Postman atau alat serupa untuk akses endpoint
- Lihat rute API di `routes/api.php`