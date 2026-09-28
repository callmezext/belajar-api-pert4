# ⚡ Enchant AI Gateway & Multi-Tier API Platform
### Tugas Praktikum Interoperabilitas — Pertemuan 4

Proyek ini merupakan implementasi arsitektur **Interoperabilitas Sistem Terdistribusi** yang menghubungkan dua ekosistem teknologi berbeda: **Node.js (Express.js)** sebagai penyedia layanan API Gateway dan **PHP (Laravel 11)** sebagai antarmuka klien Web (*Frontend Client*).

Sistem ini memodelkan platform penyedia API kecerdasan buatan (*AI API Provider*) dengan sistem kuota token bertingkat (*Multi-Tier*), otentikasi berbasis JWT & API Key, serta dashboard manajemen pengguna dan analitik.

---

## 📌 Daftar Isi
1. [Arsitektur & Konsep Interoperabilitas](#-arsitektur--konsep-interoperabilitas)
2. [Pemisahan Backend vs Frontend](#-pemisahan-backend-vs-frontend)
3. [Cara Kerja Sistem (Alur Komunikasi)](#-cara-kerja-sistem-alur-komunikasi)
4. [Struktur Folder](#-struktur-folder)
5. [Prasyarat Sistem](#-prasyarat-sistem)
6. [Panduan Menjalankan Sistem](#-panduan-menjalankan-sistem)
7. [Akun Bawaan (Default Credentials)](#-akun-bawaan-default-credentials)
8. [Dokumentasi Endpoint REST API](#-dokumentasi-endpoint-rest-api)

---

## 🏛️ Arsitektur & Konsep Interoperabilitas

Pada praktikum Pertemuan 4 ini, interoperabilitas diterapkan melalui komunikasi **REST API (JSON over HTTP)**:
- **Layanan Backend** berdiri sendiri (*standalone service*) yang mengelola basis data SQLite, autentikasi keamanan, validasi kuota token, dan proxy AI stream.
- **Layanan Frontend** bertindak sebagai *consumer* independen yang berkomunikasi murni melalui HTTP request ke endpoint Backend tanpa menyentuh database backend secara langsung.

```mermaid
flowchart TD
    subgraph Klien_Eksternal ["Klien Eksternal / Developer"]
        DevApp["Aplikasi Klien / cURL / Postman"]
    end

    subgraph Frontend_App ["Frontend (Laravel 11 - Port 8001)"]
        WebUI["Web Browser (UI)"]
        LaravelCtrl["Laravel Controllers (HTTP Client)"]
        WebUI --> LaravelCtrl
    end

    subgraph Backend_App ["Backend API Gateway (Express.js - Port 5000)"]
        AuthMid["JWT & API Key Middleware"]
        Router["Express Router (/api & /v1)"]
        TokenEngine["Token Quota & Tier Engine"]
        UpstreamProxy["Upstream AI Proxy (Stream)"]
        SQLite[(SQLite Database data.db)]
    end

    subgraph External_AI ["Penyedia AI Eksternal"]
        AIProvider["Upstream LLM Provider"]
    end

    LaravelCtrl -->|"REST API (JWT Bearer)"| Router
    DevApp -->|"AI Gateway (API Key: sk-enc-...)"| Router
    Router --> AuthMid
    AuthMid --> TokenEngine
    TokenEngine <--> SQLite
    TokenEngine --> UpstreamProxy
    UpstreamProxy <-->|"HTTPS Stream"| AIProvider
```

---

## ⚖️ Pemisahan Backend vs Frontend

| Aspek | 🟢 Backend (Service Provider) | 🔵 Frontend (Client Consumer) |
| :--- | :--- | :--- |
| **Direktori** | `pert4/backend/` | `pert4/frontend/` |
| **Teknologi** | **Node.js, Express.js, SQLite3** | **PHP 8.2+, Laravel 11, Blade** |
| **Port Default** | `http://127.0.0.1:5000` | `http://127.0.0.1:8001` |
| **Tanggung Jawab** | - Mengelola database transaksi & data user (`data.db`)<br>- Enkripsi password (`bcrypt`) & pembuatan token JWT<br>- Pembuatan & validasi API Key (`sk-enc-...`)<br>- Pembatasan kuota token sesuai Tier (FREE, STARTER, SUPER)<br>- Proxy streaming kompatibel OpenAI (`/v1/chat/completions`)<br>- Logging pemakaian token (`usage_logs`) | - Antarmuka pengguna (Landing page, Login, Register)<br>- User Dashboard: status kuota, salin & buat ulang API Key, ganti Tier<br>- Admin Dashboard: statistik sistem, CRUD pengguna, konfigurasi Tier<br>- Menerjemahkan aksi UI menjadi panggilan HTTP REST ke port 5000 |
| **Basis Data** | Mengakses langsung SQLite `data.db` | **Tidak mengakses database backend langsung** (murni via REST API) |

---

## 🔄 Cara Kerja Sistem (Alur Komunikasi)

### 1. Alur Autentikasi & Dashboard
1. Pengguna membuka antarmuka web di browser (`http://127.0.0.1:8001`).
2. Saat login, form dikirim ke `AuthController` Laravel, lalu Laravel mengirim request `POST http://127.0.0.1:5000/api/auth/login` ke Express.
3. Backend Express memvalidasi kredensial pengguna via database SQLite dan mengembalikan respon JSON berisi data profil, tier, dan **JWT Token**.
4. Laravel menyimpan JWT token di sesi lokal dan mengarahkan pengguna ke halaman Dashboard.
5. Halaman Dashboard mengambil data dinamis melalui `GET http://127.0.0.1:5000/api/user/overview` dengan menyertakan header `Authorization: Bearer <token>`.

### 2. Alur Konsumsi AI Gateway
1. Pengembang mengambil API Key mereka dari dashboard (contoh: `sk-enc-xxxxxxxx`).
2. Pengembang mengirim request inferensi AI ke `POST http://127.0.0.1:5000/v1/chat/completions` dengan header `Authorization: Bearer sk-enc-xxxxxxxx`.
3. Middleware Express memvalidasi API Key, memeriksa apakah akun aktif, dan memastikan kuota token belum melampaui batas tier (`tokens_used < token_limit`).
4. Jika kuota mencukupi, request diteruskan ke penyedia AI hulu secara streaming.
5. Setelah stream selesai, Express menghitung token yang terpakai, menambah akumulasi `tokens_used`, dan mencatatnya ke tabel `usage_logs`.

---

## 📂 Struktur Folder

```text
pert4/
│
├── backend/                             # [BACKEND SERVICE]
│   ├── data.db                          # Database SQLite (dibuat otomatis)
│   ├── database.js                      # Koneksi SQLite, skema tabel, & auto-seeder
│   ├── package.json                     # Dependensi Node.js (express, sqlite3, jwt, cors)
│   ├── server.js                        # Server Express utama (Port 5000)
│   ├── upstream.js                      # Proxy koneksi streaming ke model AI
│   └── .env.example                     # Contoh variabel lingkungan backend
│
├── frontend/                            # [FRONTEND CLIENT]
│   ├── app/
│   │   └── Http/Controllers/
│   │       ├── AdminController.php      # Controller CRUD admin ke API Express
│   │       ├── AuthController.php       # Controller login/register via API Express
│   │       └── DashboardController.php  # Controller user dashboard & manajemen kuota
│   ├── resources/views/
│   │   ├── admin/index.blade.php        # Tampilan panel kontrol Administrator
│   │   ├── auth/login.blade.php         # Tampilan formulir login
│   │   ├── auth/register.blade.php      # Tampilan formulir registrasi
│   │   ├── dashboard/index.blade.php    # Tampilan dashboard pengguna & API Key
│   │   ├── layouts/app.blade.php        # Layout master Tailwind/Blade
│   │   └── welcome.blade.php            # Landing page publik
│   ├── routes/
│   │   └── web.php                      # Routing antarmuka Laravel
│   ├── .env.example                     # Konfigurasi frontend (EXPRESS_API_URL)
│   └── composer.json                    # Dependensi Laravel 11
│
├── .gitignore                           # File pengecualian Git
├── DESIGN.md                            # Panduan desain & standar antarmuka
├── README.md                            # Dokumentasi lengkap proyek
└── start.bat                            # Skrip otomatis menjalankan kedua server
```

---

## 💻 Prasyarat Sistem

Sebelum menjalankan proyek, pastikan perangkat telah terpasang:
- **Node.js** (versi 18 ke atas) & **npm**
- **PHP** (versi 8.2 ke atas) dengan ekstensi `curl`, `mbstring`, `openssl`, `sqlite3` aktif
- **Composer** (untuk dependensi Laravel)
- **Git**

---

## 🚀 Panduan Menjalankan Sistem

### Cara 1: Jalankan Otomatis dengan 1 Klik (Windows)
Cukup klik ganda file **`start.bat`** yang ada di root folder, atau jalankan melalui terminal:
```cmd
start.bat
```
Skrip ini akan otomatis membuka dua jendela terminal:
1. Server Backend Express pada port **5000**
2. Server Frontend Laravel pada port **8001**

Buka browser di alamat: **`http://127.0.0.1:8001`**

---

### Cara 2: Menjalankan Secara Manual

#### Langkah A: Jalankan Backend (Express.js)
Buka terminal pertama:
```bash
cd backend

# 1. Install dependensi
npm install

# 2. Buat file .env (opsional, sudah memiliki default di server.js)
cp .env.example .env

# 3. Jalankan server
node server.js
```
> Server backend akan berjalan di: **`http://127.0.0.1:5000`**  
> Database SQLite `data.db` dan akun awal akan otomatis dibuat saat server pertama kali dijalankan.

#### Langkah B: Jalankan Frontend (Laravel 11)
Buka terminal kedua:
```bash
cd frontend

# 1. Install dependensi composer
composer install

# 2. Siapkan file .env
cp .env.example .env

# 3. Generate Application Key
php artisan key:generate

# 4. Pastikan EXPRESS_API_URL di .env mengarah ke:
# EXPRESS_API_URL=http://127.0.0.1:5000

# 5. Jalankan server Laravel pada port 8001
php artisan serve --host=127.0.0.1 --port=8001
```
> Buka antarmuka web di: **`http://127.0.0.1:8001`**

---

## 🔑 Akun Bawaan (Default Credentials)

Sistem telah dilengkapi dengan data akun bawaan untuk pengujian:

| Role | Email | Password | Tier | Batas Kuota Token |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin@enchant.id` | `admin123` | **SUPER** | 100,000,000 Token |
| **Member Biasa** | `user@enchant.id` | `user123` | **FREE** | 1,000,000 Token |

> **Catatan Tier Kuota:**
> - **FREE**: 1,000,000 Token (Model: Gemini Flash Low)
> - **STARTER**: 5,000,000 Token (Model: Gemini Flash, GPT OSS Medium)
> - **SUPER**: 20,000,000 Token (Model: Claude Sonnet, Gemini Pro, High Thinking)

---

## 📡 Dokumentasi Endpoint REST API

Semua endpoint backend dapat diakses pada base URL: `http://127.0.0.1:5000`

### 1. Autentikasi (`/api/auth`)
| Method | Endpoint | Keterangan | Header Wajib |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/auth/register` | Mendaftarkan akun member baru (default tier: FREE) | - |
| `POST` | `/api/auth/login` | Login dan mendapatkan token JWT | - |
| `GET` | `/api/auth/me` | Memeriksa data sesi pengguna aktif | `Authorization: Bearer <JWT>` |

### 2. Dashboard Pengguna (`/api/user`)
| Method | Endpoint | Keterangan | Header Wajib |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/user/overview` | Mengambil data kuota, tier aktif, dan riwayat log | `Authorization: Bearer <JWT>` |
| `POST` | `/api/user/regenerate-key` | Membuat ulang API Key pengguna | `Authorization: Bearer <JWT>` |
| `POST` | `/api/user/upgrade-tier` | Mengubah status tier paket langganan | `Authorization: Bearer <JWT>` |

### 3. Manajemen Administrator (`/api/admin`)
| Method | Endpoint | Keterangan | Header Wajib |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/admin/stats` | Statistik agregat sistem dan log transaksi global | `Authorization: Bearer <Admin_JWT>` |
| `GET` | `/api/admin/users` | Mendapatkan seluruh daftar pengguna | `Authorization: Bearer <Admin_JWT>` |
| `POST` | `/api/admin/users` | Menambahkan pengguna baru secara manual | `Authorization: Bearer <Admin_JWT>` |
| `PUT` | `/api/admin/users/:id` | Mengedit status, role, tier, dan kuota pengguna | `Authorization: Bearer <Admin_JWT>` |
| `DELETE` | `/api/admin/users/:id` | Menghapus akun pengguna dari sistem | `Authorization: Bearer <Admin_JWT>` |
| `PUT` | `/api/admin/tiers/:tier` | Memperbarui batas kuota dan model pada tier | `Authorization: Bearer <Admin_JWT>` |

### 4. AI Gateway Proxy Kompatibel OpenAI (`/v1`)
| Method | Endpoint | Keterangan | Header Wajib |
| :--- | :--- | :--- | :--- |
| `POST` | `/v1/chat/completions` | Endpoint inferensi percakapan AI (streaming) | `Authorization: Bearer <API_KEY>` |
| `GET` | `/v1/models` | Mengambil daftar model yang diizinkan untuk tier | `Authorization: Bearer <API_KEY>` |

Contoh cURL pemanggilan AI Gateway:
```bash
curl http://127.0.0.1:5000/v1/chat/completions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer sk-enc-xxxxxxxxxxxxxxxx" \
  -d '{
    "model": "ag/gemini-3.8-flash",
    "messages": [
      {"role": "user", "content": "Halo, jelaskan konsep interoperabilitas secara singkat!"}
    ],
    "stream": true
  }'
```
