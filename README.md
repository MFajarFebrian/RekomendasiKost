# Sistem Rekomendasi Kost

![Design Preview](C:/Users/E31/.gemini/antigravity/brain/5d78f325-73ef-44ab-b2b8-185b58250279/uploaded_image_1767529942981.png)

> **Sistem Pendukung Keputusan (SPK)** untuk rekomendasi kost menggunakan metode **AHP-TOPSIS**

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql)](https://mysql.com)
[![Vercel](https://img.shields.io/badge/Deploy-Vercel-000000?logo=vercel)](https://vercel.com)

---

## 📋 Deskripsi

Sistem Rekomendasi Kost adalah aplikasi web yang membantu pengguna menemukan kost (tempat tinggal sementara) terbaik berdasarkan multiple criteria menggunakan metode:

- **AHP (Analytical Hierarchy Process)**: Menentukan bobot kriteria
- **TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution)**: Meranking alternatif kost

### ✨ Fitur Utama

- 🔍 **Pencarian & Filter**: Cari kost berdasarkan harga, jarak, dan rating
- 🎯 **Rekomendasi Cerdas**: Sistem ranking otomatis menggunakan AHP-TOPSIS
- 📊 **Detail Perhitungan**: Transparansi proses SPK dengan visualisasi
- 🔐 **Admin Panel**: Manajemen data kost dan konfigurasi bobot kriteria
- 📱 **Responsive Design**: Mobile-first, modern UI/UX
- ☁️ **Cloud Ready**: Siap deploy di Vercel

---

## 🎯 Kriteria Penilaian

| Kriteria | Tipe | Bobot Default | Keterangan |
|----------|------|---------------|------------|
| Jarak ke Kampus | Cost | 10% | Semakin dekat semakin baik |
| Jarak ke Market | Cost | 5% | Semakin dekat semakin baik |
| Harga | Cost | 40% | Semakin murah semakin baik |
| Kebersihan | Benefit | 10% | Skala 1-5 |
| Keamanan | Benefit | 15% | Skala 1-5 |
| Fasilitas | Benefit | 20% | Skala 1-5 |

---

## 🛠️ Tech Stack

### Frontend
- HTML5, CSS3, JavaScript (ES6+)
- Modern design dengan Glassmorphism
- Responsive & Mobile-first

### Backend
- PHP 8.0+ dengan PDO
- RESTful API architecture
- JWT Authentication

### Database
- MySQL 8.0+ / MariaDB 10.4+
- Cloud options: Neon PostgreSQL, PlanetScale

### Deployment
- Vercel (Frontend + Serverless Functions)
- XAMPP (Local development)

---

## 📁 Struktur Proyek

```
RekomendasiKost/
├── docs/                          # 📚 Dokumentasi
│   ├── 00_PROJECT_OVERVIEW.md
│   ├── 01_REQUIREMENTS_DOCUMENT.md
│   ├── 02_DATABASE_SPECIFICATION.md
│   ├── 03_BACKEND_SPECIFICATION.md
│   ├── 04_FRONTEND_SPECIFICATION.md
│   ├── 05_DEPLOYMENT_GUIDE.md
│   └── 06_API_DOCUMENTATION.md
├── spk_kost.sql                   # 💾 Database schema
├── backend/                       # 🔧 Backend (PHP)
│   ├── config/
│   ├── models/
│   ├── controllers/
│   ├── services/
│   │   ├── AHPService.php
│   │   └── TOPSISService.php
│   └── api/
├── frontend/                      # 🎨 Frontend
│   ├── index.html
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── pages/
└── README.md
```

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.0+
- MySQL 8.0+ / MariaDB 10.4+
- Composer (optional)
- XAMPP / WAMP / LAMP

### Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/yourusername/RekomendasiKost.git
   cd RekomendasiKost
   ```

2. **Import database**
   ```bash
   mysql -u root -p < spk_kost.sql
   ```

3. **Configure database connection**
   ```php
   // backend/config/database.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'spk_kost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Start local server**
   ```bash
   # Using XAMPP
   # Place project in C:\xampp\htdocs\RekomendasiKost
   # Start Apache & MySQL
   # Access: http://localhost/RekomendasiKost
   ```

5. **Default admin login**
   ```
   Email: admin@spkkost.com
   Password: password
   ```

---

## 📖 Dokumentasi

Dokumentasi lengkap tersedia di folder [`docs/`](docs/):

1. **[Project Overview](docs/00_PROJECT_OVERVIEW.md)** - Gambaran umum proyek
2. **[Requirements Document](docs/01_REQUIREMENTS_DOCUMENT.md)** - Functional & non-functional requirements
3. **[Database Specification](docs/02_DATABASE_SPECIFICATION.md)** - ERD, schema, dan queries
4. **[Backend Specification](docs/03_BACKEND_SPECIFICATION.md)** - AHP-TOPSIS implementation
5. **[Frontend Specification](docs/04_FRONTEND_SPECIFICATION.md)** - Design system & components
6. **[Deployment Guide](docs/05_DEPLOYMENT_GUIDE.md)** - Deploy ke Vercel
7. **[API Documentation](docs/06_API_DOCUMENTATION.md)** - RESTful API endpoints

---

## 🧮 Algoritma SPK

### 1. AHP (Analytical Hierarchy Process)

Menghitung bobot kriteria dari matriks perbandingan berpasangan:

```
1. Input matriks 6x6 perbandingan berpasangan
2. Normalisasi matriks
3. Hitung priority vector (bobot)
4. Hitung Consistency Ratio (CR)
5. Jika CR < 0.1 → Konsisten ✓
```

### 2. TOPSIS

Meranking kost berdasarkan kedekatan dengan solusi ideal:

```
1. Normalisasi matriks keputusan
2. Pembobotan dengan hasil AHP
3. Tentukan A+ (ideal positif) dan A- (ideal negatif)
4. Hitung jarak D+ dan D-
5. Hitung nilai preferensi: V = D- / (D+ + D-)
6. Ranking berdasarkan V tertinggi
```

**Contoh Output**:
```
Rank 1: Kost Eleora Cikunir Tipe A (Score: 0.766)
Rank 2: Kost Ezra Tipe A (Score: 0.741)
Rank 3: Kost Khazanah Tipe Vvip Executive (Score: 0.716)
```

---

## 🌐 API Usage

### Get Recommendations

```javascript
// POST /api/spk/topsis/calculate
const response = await fetch('/api/spk/topsis/calculate', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    filters: {
      max_harga: 2000000,
      max_jarak_kampus: 3.0
    }
  })
});

const data = await response.json();
console.log(data.data.recommendations);
```

### Configure AHP Weights (Admin)

```javascript
// POST /api/spk/ahp/configure
const response = await fetch('/api/spk/ahp/configure', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${adminToken}`
  },
  body: JSON.stringify({
    pairwise_matrix: {
      "Jarak Kampus": [1, 2, 0.25, 1, 0.6667, 0.5],
      "Jarak Market": [0.5, 1, 0.125, 0.5, 0.3333, 0.25],
      // ... 4 more rows
    }
  })
});
```

Lihat [API Documentation](docs/06_API_DOCUMENTATION.md) untuk detail lengkap.

---

## 🚢 Deployment

### Deploy ke Vercel

1. **Install Vercel CLI**
   ```bash
   npm install -g vercel
   ```

2. **Login**
   ```bash
   vercel login
   ```

3. **Deploy**
   ```bash
   vercel --prod
   ```

4. **Setup Database**
   - Gunakan Neon PostgreSQL atau PlanetScale
   - Set environment variables di Vercel Dashboard

Lihat [Deployment Guide](docs/05_DEPLOYMENT_GUIDE.md) untuk panduan lengkap.

---

## 🧪 Testing

### Manual Testing

1. **Test Pencarian Kost**
   - Buka `/pages/kost-list.html`
   - Gunakan filter harga, jarak, rating
   - Verify hasil sesuai filter

2. **Test Rekomendasi**
   - Buka `/pages/recommendations.html`
   - Klik "Calculate Recommendations"
   - Verify ranking sesuai dengan score

3. **Test Admin Panel**
   - Login sebagai admin
   - Tambah/edit/hapus kost
   - Konfigurasi AHP weights
   - Verify CR < 0.1

### API Testing

```bash
# Test dengan curl
curl -X POST http://localhost/RekomendasiKost/api/spk/topsis/calculate \
  -H "Content-Type: application/json" \
  -d '{"filters": {"max_harga": 2000000}}'
```

---

## 📊 Screenshots

### Home Page
![Home](docs/screenshots/home.png)

### Recommendations
![Recommendations](docs/screenshots/recommendations.png)

### Admin Dashboard
![Admin](docs/screenshots/admin.png)

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👥 Authors

- **Development Team** - Initial work

---

## 🙏 Acknowledgments

- Design inspiration from [Veco Template](https://www.figma.com/community)
- AHP-TOPSIS methodology references
- Open source community

---

## 📞 Support

Untuk pertanyaan atau dukungan:

- 📧 Email: support@spkkost.com
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/RekomendasiKost/issues)
- 📖 Docs: [Documentation](docs/)

---

## 🗺️ Roadmap

- [x] Core SPK functionality (AHP-TOPSIS)
- [x] Admin panel
- [x] RESTful API
- [ ] User authentication & profiles
- [ ] Image upload & gallery
- [ ] Google Maps integration
- [ ] Review & rating system
- [ ] Email notifications
- [ ] Mobile app (React Native)
- [ ] Advanced analytics

---

## 📈 Project Status

**Version**: 1.0.0  
**Status**: ✅ Production Ready  
**Last Updated**: 2026-01-16

---

<div align="center">

**Made with ❤️ using PHP & MySQL**

[⬆ Back to Top](#sistem-rekomendasi-kost)

</div>
