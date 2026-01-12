# Manual Book
## Sistem Rekomendasi Kost (SiKost)
### Menggunakan Metode AHP-TOPSIS

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Persyaratan Sistem](#2-persyaratan-sistem)
3. [Instalasi](#3-instalasi)
4. [Struktur Aplikasi](#4-struktur-aplikasi)
5. [Panduan Penggunaan](#5-panduan-penggunaan)
6. [Penjelasan Metode AHP-TOPSIS](#6-penjelasan-metode-ahp-topsis)
7. [API Documentation](#7-api-documentation)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Pendahuluan

### 1.1 Tentang SiKost

**SiKost (Sistem Rekomendasi Kost)** adalah aplikasi web yang membantu pengguna menemukan kost terbaik berdasarkan preferensi mereka. Sistem ini menggunakan metode **AHP (Analytical Hierarchy Process)** untuk menentukan bobot kriteria dan **TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution)** untuk melakukan perankingan alternatif.

### 1.2 Fitur Utama

| No | Fitur | Deskripsi |
|----|-------|-----------|
| 1 | Daftar Kost | Melihat semua kost yang tersedia dengan filter |
| 2 | Detail Kost | Melihat informasi lengkap setiap kost |
| 3 | Rekomendasi | Mendapatkan rekomendasi kost berdasarkan prioritas pengguna |
| 4 | Slider Bobot | Mengatur bobot kriteria secara interaktif |
| 5 | Perhitungan TOPSIS | Ranking otomatis berdasarkan metode ilmiah |

### 1.3 Kriteria Penilaian

Sistem menggunakan 6 kriteria untuk menilai kost:

| Kriteria | Tipe | Keterangan |
|----------|------|------------|
| Jarak Kampus | Cost | Semakin dekat semakin baik |
| Jarak Market | Cost | Semakin dekat semakin baik |
| Harga | Cost | Semakin murah semakin baik |
| Kebersihan | Benefit | Semakin tinggi semakin baik (skala 1-5) |
| Keamanan | Benefit | Semakin tinggi semakin baik (skala 1-5) |
| Fasilitas | Benefit | Semakin tinggi semakin baik (skala 1-5) |

---

## 2. Persyaratan Sistem

### 2.1 Server Requirements

| Komponen | Versi Minimum | Rekomendasi |
|----------|---------------|-------------|
| PHP | 7.4 | 8.0+ |
| MySQL/MariaDB | 5.7 | 8.0+ / MariaDB 10.4+ |
| Web Server | Apache 2.4 | Apache 2.4 dengan mod_rewrite |

### 2.2 Software yang Dibutuhkan

- **XAMPP** (Windows) - Paket lengkap Apache + MySQL + PHP
- **Browser Modern** - Chrome, Firefox, Edge, atau Safari versi terbaru

### 2.3 Konfigurasi PHP yang Dibutuhkan

```ini
extension=pdo_mysql
extension=mysqli
```

---

## 3. Instalasi

### 3.1 Langkah Instalasi

#### Langkah 1: Install XAMPP
1. Download XAMPP dari https://www.apachefriends.org/
2. Jalankan installer dan ikuti petunjuk
3. Pastikan Apache dan MySQL terinstall

#### Langkah 2: Copy Project
1. Copy folder `RekomendasiKost` ke direktori `C:\xampp\htdocs\`
2. Struktur folder harus menjadi: `C:\xampp\htdocs\RekomendasiKost\`

#### Langkah 3: Setup Database
1. Buka XAMPP Control Panel
2. Start **Apache** dan **MySQL**
3. Buka browser dan akses `http://localhost/phpmyadmin`
4. Buat database baru dengan nama: `spk_kost`
5. Pilih database `spk_kost`, klik tab **Import**
6. Pilih file `spk_kost.sql` dari folder project
7. Klik **Go** untuk mengimport

#### Langkah 4: Verifikasi Instalasi
1. Buka browser
2. Akses `http://localhost/RekomendasiKost/`
3. Jika halaman home muncul dengan data kost, instalasi berhasil

### 3.2 Konfigurasi Database

File konfigurasi database terletak di `config/database.php`:

```php
private $host = '127.0.0.1';
private $db_name = 'spk_kost';
private $username = 'root';
private $password = '';
private $charset = 'utf8mb4';
```

Ubah nilai-nilai di atas sesuai dengan konfigurasi MySQL Anda.

---

## 4. Struktur Aplikasi

### 4.1 Struktur Folder

```
RekomendasiKost/
├── api/
│   └── index.php           # Router API
├── assets/
│   ├── css/
│   │   ├── main.css        # Style utama
│   │   ├── components.css  # Komponen UI
│   │   └── responsive.css  # Responsive design
│   └── js/
│       ├── api.js          # API wrapper
│       ├── app.js          # Main JavaScript
│       └── utils.js        # Utility functions
├── config/
│   ├── constants.php       # Konstanta aplikasi
│   └── database.php        # Konfigurasi database
├── controllers/
│   ├── KostController.php  # Controller kost
│   └── SPKController.php   # Controller SPK (AHP/TOPSIS)
├── models/
│   ├── Kampus.php          # Model kampus
│   ├── Kost.php            # Model kost
│   └── SPK.php             # Model SPK
├── pages/
│   ├── kost-list.html      # Halaman daftar kost
│   ├── kost-detail.html    # Halaman detail kost
│   └── recommendations.html # Halaman rekomendasi
├── services/
│   ├── AHPService.php      # Service AHP
│   └── TOPSISService.php   # Service TOPSIS
├── utils/
│   ├── Database.php        # Helper database
│   ├── Response.php        # Helper response API
│   └── Validator.php       # Validasi input
├── index.html              # Halaman utama
├── spk_kost.sql            # File SQL database
└── manualbook.md           # Manual book (file ini)
```

### 4.2 Deskripsi Komponen

| Folder | Fungsi |
|--------|--------|
| `api/` | Endpoint API untuk komunikasi frontend-backend |
| `assets/` | File statis (CSS, JavaScript) |
| `config/` | Konfigurasi aplikasi |
| `controllers/` | Logika bisnis dan handling request |
| `models/` | Akses dan manipulasi data database |
| `pages/` | Halaman-halaman aplikasi |
| `services/` | Implementasi algoritma AHP dan TOPSIS |
| `utils/` | Fungsi-fungsi utilitas |

---

## 5. Panduan Penggunaan

### 5.1 Halaman Utama (Home)

**URL:** `http://localhost/RekomendasiKost/`

Halaman utama menampilkan:
- Hero section dengan deskripsi sistem
- 6 kriteria penilaian yang digunakan
- Daftar kost terpopuler (6 kost teratas)
- Tombol navigasi ke halaman lain

### 5.2 Halaman Daftar Kost

**URL:** `http://localhost/RekomendasiKost/pages/kost-list.html`

#### Cara Menggunakan:

1. **Pilih Kampus** (wajib)
   - Klik dropdown "Pilih Kampus"
   - Pilih kampus yang diinginkan
   - Data kost akan dimuat sesuai area kampus

2. **Filter Kost**
   - **Cari**: Ketik nama kost untuk mencari
   - **Max Harga**: Filter berdasarkan harga maksimum
   - **Urutkan**: Urutkan berdasarkan harga atau jarak

3. **Lihat Detail**
   - Klik tombol "Detail" pada kartu kost untuk melihat informasi lengkap

### 5.3 Halaman Detail Kost

**URL:** `http://localhost/RekomendasiKost/pages/kost-detail.html?id={id}`

Halaman ini menampilkan:
- Foto kost
- Nama dan harga kost
- Rating rata-rata
- Jarak ke kampus dan market
- Rating per kriteria (Kebersihan, Keamanan, Fasilitas)
- Tabel detail kriteria dengan tipe (Cost/Benefit)

### 5.4 Halaman Rekomendasi

**URL:** `http://localhost/RekomendasiKost/pages/recommendations.html`

#### Cara Mendapatkan Rekomendasi:

1. **Pilih Kampus**
   - Pilih kampus dari dropdown
   - Ini menentukan area pencarian kost

2. **Atur Prioritas Kriteria**
   - Geser slider untuk setiap kriteria sesuai preferensi Anda
   - **Slider otomatis menyeimbangkan** agar total selalu 100%
   - Kriteria dengan nilai lebih tinggi = lebih diprioritaskan

   | Kriteria | Penjelasan |
   |----------|------------|
   | Jarak ke Kampus | Prioritas kedekatan dengan kampus |
   | Jarak ke Market | Prioritas kedekatan dengan minimarket |
   | Harga Terjangkau | Prioritas harga murah |
   | Kebersihan | Prioritas kebersihan kost |
   | Keamanan | Prioritas keamanan kost |
   | Fasilitas | Prioritas kelengkapan fasilitas |

3. **Cari Rekomendasi**
   - Klik tombol "🔍 Cari Rekomendasi Terbaik"
   - Sistem akan menghitung menggunakan metode TOPSIS
   - Hasil rekomendasi ditampilkan dalam bentuk ranking

4. **Membaca Hasil**
   - **Ranking 1** (🥇 Gold) = Rekomendasi terbaik
   - **Ranking 2** (🥈 Silver) = Rekomendasi kedua
   - **Ranking 3** (🥉 Bronze) = Rekomendasi ketiga
   - Persentase kecocokan menunjukkan seberapa cocok kost dengan preferensi Anda

---

## 6. Penjelasan Metode AHP-TOPSIS

### 6.1 Metode AHP (Analytical Hierarchy Process)

AHP digunakan untuk menghitung bobot prioritas setiap kriteria.

#### Tahapan AHP:

1. **Matriks Perbandingan Berpasangan**
   - Membandingkan kepentingan antar kriteria
   - Menggunakan skala Saaty (1-9)

2. **Normalisasi Matriks**
   - Setiap elemen dibagi dengan jumlah kolomnya
   - Rumus: `nᵢⱼ = aᵢⱼ / Σaᵢⱼ`

3. **Perhitungan Bobot**
   - Rata-rata setiap baris = bobot kriteria
   - Rumus: `Wᵢ = Σnᵢⱼ / n`

4. **Uji Konsistensi**
   - Menghitung Consistency Ratio (CR)
   - CR < 0.1 = Konsisten ✓

### 6.2 Metode TOPSIS

TOPSIS digunakan untuk melakukan perankingan alternatif (kost).

#### Tahapan TOPSIS:

1. **Normalisasi Matriks Keputusan**
   - Rumus: `rᵢⱼ = xᵢⱼ / √(Σxᵢⱼ²)`
   - Membuat semua kriteria dalam skala yang sama

2. **Matriks Normalisasi Terbobot**
   - Rumus: `yᵢⱼ = rᵢⱼ × wⱼ`
   - Mengalikan dengan bobot dari AHP

3. **Solusi Ideal Positif (A⁺) dan Negatif (A⁻)**
   - **Kriteria Benefit**: A⁺ = MAX, A⁻ = MIN
   - **Kriteria Cost**: A⁺ = MIN, A⁻ = MAX

4. **Jarak ke Solusi Ideal**
   - D⁺ = Jarak ke solusi ideal positif (terbaik)
   - D⁻ = Jarak ke solusi ideal negatif (terburuk)
   - Rumus: `Dᵢ = √[Σ(yᵢⱼ - Aⱼ)²]`

5. **Nilai Preferensi (V)**
   - Rumus: `Vᵢ = D⁻ᵢ / (D⁺ᵢ + D⁻ᵢ)`
   - Nilai 0-1, semakin tinggi semakin baik
   - Ranking berdasarkan nilai V tertinggi

---

## 7. API Documentation

### 7.1 Base URL

```
http://localhost/RekomendasiKost/api
```

### 7.2 Endpoints

#### GET /kampus
Mendapatkan daftar kampus.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama": "Universitas Gunadarma Kampus J1",
      "kode": "GD-J1",
      "kota": "Bekasi"
    }
  ]
}
```

#### GET /kost
Mendapatkan daftar kost dengan pagination.

**Parameters:**
| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| kampus_id | int | Filter berdasarkan kampus (wajib) |
| search | string | Cari berdasarkan nama |
| max_harga | int | Filter harga maksimum |
| sort_by | string | Urutkan: id, harga, jarak_kampus |
| page | int | Nomor halaman |
| limit | int | Jumlah per halaman (default: 10) |

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [...],
    "pagination": {
      "current_page": 1,
      "total_pages": 2,
      "total_items": 20,
      "has_prev": false,
      "has_next": true
    }
  }
}
```

#### GET /kost/{id}
Mendapatkan detail kost.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nama": "Kost Papipul Pakuwon",
    "jarak_kampus": 1.2,
    "jarak_market": 0.5,
    "harga": 2500000,
    "kebersihan": 5,
    "keamanan": 4,
    "fasilitas": 5
  }
}
```

#### POST /spk/topsis/calculate
Menghitung rekomendasi TOPSIS.

**Request Body:**
```json
{
  "kampus_id": 1,
  "weights": {
    "jarak_kampus": 16,
    "jarak_market": 16,
    "harga": 17,
    "kebersihan": 17,
    "keamanan": 17,
    "fasilitas": 17
  },
  "limit": 10
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "recommendations": [
      {
        "rank": 1,
        "nama": "Kost Khazanah VIP",
        "score": 0.714,
        "d_positive": 0.041,
        "d_negative": 0.103,
        "details": {...}
      }
    ],
    "calculation_metadata": {
      "total_alternatives": 20,
      "weights_used": {...},
      "execution_time_ms": 15.23
    }
  }
}
```

#### GET /spk/ahp/weights
Mendapatkan bobot kriteria saat ini.

#### GET /spk/ahp/details
Mendapatkan detail perhitungan AHP.

---

## 8. Troubleshooting

### 8.1 Masalah Umum

#### "Gagal memuat data" pada halaman

**Penyebab:** Database tidak terkoneksi atau belum diimport.

**Solusi:**
1. Pastikan MySQL sudah running di XAMPP
2. Cek database `spk_kost` sudah ada di phpMyAdmin
3. Import ulang file `spk_kost.sql`
4. Cek konfigurasi di `config/database.php`

#### Halaman blank/tidak muncul

**Penyebab:** Apache tidak running atau path salah.

**Solusi:**
1. Pastikan Apache sudah running di XAMPP
2. Cek folder project ada di `C:\xampp\htdocs\RekomendasiKost\`
3. Akses dengan URL yang benar: `http://localhost/RekomendasiKost/`

#### Error 500 Internal Server Error

**Penyebab:** Error pada PHP.

**Solusi:**
1. Cek log error di `C:\xampp\apache\logs\error.log`
2. Pastikan PHP version >= 7.4
3. Pastikan extension PDO MySQL aktif

#### Dropdown kampus kosong

**Penyebab:** Tabel `kampus` kosong atau API error.

**Solusi:**
1. Cek tabel `kampus` di phpMyAdmin
2. Import ulang `spk_kost.sql` jika kosong
3. Cek console browser untuk error

### 8.2 Testing Koneksi Database

Akses `http://localhost/RekomendasiKost/test_db.php`

- **"Connection successful!"** = Database terkoneksi
- **Error message** = Ada masalah koneksi, periksa konfigurasi

### 8.3 Cek API Manual

Buka URL berikut di browser untuk testing:

| Endpoint | URL |
|----------|-----|
| Daftar Kampus | `http://localhost/RekomendasiKost/api/kampus` |
| Daftar Kost | `http://localhost/RekomendasiKost/api/kost?kampus_id=1` |
| Bobot AHP | `http://localhost/RekomendasiKost/api/spk/ahp/weights` |

---

## Kontak & Dukungan

Jika mengalami masalah atau membutuhkan bantuan:
- Email: info@sikost.com
- Dokumentasi teknis tersedia di folder `docs/`

---

**© 2026 SiKost - Sistem Rekomendasi Kost**
**Built with AHP-TOPSIS Method**
