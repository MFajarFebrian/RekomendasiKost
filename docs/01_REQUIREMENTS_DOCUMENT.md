# Requirements Document (RD)
## Sistem Rekomendasi Kost

---

## 1. Introduction

### 1.1 Purpose
Dokumen ini menjelaskan kebutuhan fungsional dan non-fungsional untuk Sistem Rekomendasi Kost yang menggunakan metode AHP-TOPSIS untuk memberikan rekomendasi kost terbaik kepada pengguna.

### 1.2 Scope
Sistem ini mencakup:
- Web application untuk pencarian dan rekomendasi kost
- Admin panel untuk manajemen data
- SPK engine menggunakan AHP-TOPSIS
- RESTful API untuk integrasi
- Deployment di Vercel dengan database cloud

### 1.3 Definitions & Acronyms

| Term | Definition |
|------|------------|
| SPK | Sistem Pendukung Keputusan |
| AHP | Analytical Hierarchy Process |
| TOPSIS | Technique for Order of Preference by Similarity to Ideal Solution |
| Kost | Tempat tinggal sementara (boarding house) |
| Kriteria | Parameter penilaian kost |

## 2. Functional Requirements

### 2.1 User Management (FR-UM)

#### FR-UM-01: User Registration
- **Priority**: High
- **Description**: User dapat mendaftar akun baru
- **Input**: Email, password, nama lengkap, nomor telepon
- **Process**: Validasi email unique, hash password, simpan ke database
- **Output**: Akun user baru, email konfirmasi
- **Validation**: 
  - Email format valid
  - Password minimal 8 karakter
  - Email belum terdaftar

#### FR-UM-02: User Login
- **Priority**: High
- **Description**: User dapat login ke sistem
- **Input**: Email, password
- **Process**: Verifikasi credentials, create session
- **Output**: Redirect ke dashboard, session token
- **Validation**: Credentials valid, akun aktif

#### FR-UM-03: User Profile Management
- **Priority**: Medium
- **Description**: User dapat update profil
- **Input**: Nama, telepon, foto profil, preferensi
- **Process**: Update data user di database
- **Output**: Profil terupdate
- **Validation**: Data format valid

### 2.2 Kost Management (FR-KM)

#### FR-KM-01: View Kost List
- **Priority**: High
- **Description**: User dapat melihat daftar kost
- **Input**: Filter (optional), pagination
- **Process**: Query database, apply filter
- **Output**: List kost dengan informasi dasar
- **Validation**: -

#### FR-KM-02: View Kost Detail
- **Priority**: High
- **Description**: User dapat melihat detail kost
- **Input**: Kost ID
- **Process**: Query detail kost dari database
- **Output**: Detail lengkap kost (nama, harga, fasilitas, foto, lokasi, dll)
- **Validation**: Kost ID valid

#### FR-KM-03: Add Kost (Admin)
- **Priority**: High
- **Description**: Admin dapat menambah data kost baru
- **Input**: Nama, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas, foto, deskripsi
- **Process**: Validasi input, simpan ke database
- **Output**: Kost baru ditambahkan
- **Validation**: 
  - Semua field required terisi
  - Jarak > 0
  - Harga > 0
  - Rating 1-5

#### FR-KM-04: Edit Kost (Admin)
- **Priority**: High
- **Description**: Admin dapat mengubah data kost
- **Input**: Kost ID, updated data
- **Process**: Validasi, update database
- **Output**: Data kost terupdate
- **Validation**: Same as FR-KM-03

#### FR-KM-05: Delete Kost (Admin)
- **Priority**: Medium
- **Description**: Admin dapat menghapus data kost
- **Input**: Kost ID
- **Process**: Soft delete atau hard delete
- **Output**: Kost dihapus
- **Validation**: Kost ID valid, konfirmasi delete

### 2.3 SPK Recommendation System (FR-SPK)

#### FR-SPK-01: Configure Criteria Weights (Admin)
- **Priority**: High
- **Description**: Admin dapat mengatur bobot kriteria menggunakan AHP
- **Input**: Matriks perbandingan berpasangan (6x6)
- **Process**: 
  1. Input pairwise comparison
  2. Normalisasi matriks
  3. Hitung eigenvalue & eigenvector
  4. Hitung Consistency Ratio (CR)
  5. Jika CR < 0.1, simpan bobot
- **Output**: Bobot kriteria tersimpan
- **Validation**: CR < 0.1 (konsisten)

#### FR-SPK-02: Calculate Recommendations
- **Priority**: High
- **Description**: Sistem menghitung rekomendasi kost menggunakan TOPSIS
- **Input**: Data kost, bobot kriteria
- **Process**:
  1. Normalisasi matriks keputusan
  2. Pembobotan matriks ternormalisasi
  3. Tentukan A+ dan A-
  4. Hitung D+ dan D-
  5. Hitung nilai preferensi V
  6. Ranking berdasarkan V
- **Output**: List kost terurut berdasarkan ranking
- **Validation**: Data kost tersedia, bobot valid

#### FR-SPK-03: View Recommendation Results
- **Priority**: High
- **Description**: User dapat melihat hasil rekomendasi
- **Input**: User preferences (optional filter)
- **Process**: Ambil hasil perhitungan TOPSIS
- **Output**: Top 10 kost rekomendasi dengan score
- **Validation**: -

#### FR-SPK-04: View Calculation Details
- **Priority**: Medium
- **Description**: User/Admin dapat melihat detail perhitungan
- **Input**: Kost ID atau calculation session
- **Process**: Tampilkan step-by-step calculation
- **Output**: 
  - Matriks ternormalisasi
  - Matriks terbobot
  - D+, D-, V values
  - Ranking explanation
- **Validation**: -

### 2.4 Search & Filter (FR-SF)

#### FR-SF-01: Search Kost
- **Priority**: High
- **Description**: User dapat search kost by nama
- **Input**: Search keyword
- **Process**: Query database dengan LIKE
- **Output**: Filtered kost list
- **Validation**: -

#### FR-SF-02: Filter by Criteria
- **Priority**: High
- **Description**: User dapat filter kost berdasarkan kriteria
- **Input**: 
  - Harga range (min-max)
  - Jarak kampus max
  - Jarak market max
  - Kebersihan min
  - Keamanan min
  - Fasilitas min
- **Process**: Apply WHERE conditions
- **Output**: Filtered kost list
- **Validation**: Min <= Max

#### FR-SF-03: Sort Results
- **Priority**: Medium
- **Description**: User dapat sort hasil
- **Input**: Sort by (harga, jarak_kampus, rating)
- **Process**: ORDER BY query
- **Output**: Sorted list
- **Validation**: -

### 2.5 Comparison (FR-CMP)

#### FR-CMP-01: Compare Kost
- **Priority**: Medium
- **Description**: User dapat membandingkan 2-4 kost
- **Input**: Array of Kost IDs (2-4)
- **Process**: Fetch data, display side-by-side
- **Output**: Comparison table
- **Validation**: 2 <= count <= 4

### 2.6 Admin Dashboard (FR-AD)

#### FR-AD-01: View Statistics
- **Priority**: Medium
- **Description**: Admin dapat melihat statistik
- **Output**:
  - Total kost
  - Total users
  - Popular kost (most viewed)
  - Average price
  - Distribution by criteria
- **Validation**: -

#### FR-AD-02: View Logs
- **Priority**: Low
- **Description**: Admin dapat melihat activity logs
- **Output**: User activities, calculation history
- **Validation**: -

## 3. Non-Functional Requirements

### 3.1 Performance (NFR-PERF)

#### NFR-PERF-01: Page Load Time
- **Requirement**: Halaman harus load dalam < 3 detik
- **Measurement**: Google Lighthouse, GTmetrix
- **Priority**: High

#### NFR-PERF-02: API Response Time
- **Requirement**: API response < 1 detik untuk 95% requests
- **Measurement**: Server logs, monitoring tools
- **Priority**: High

#### NFR-PERF-03: Concurrent Users
- **Requirement**: Support minimal 100 concurrent users
- **Measurement**: Load testing (JMeter, k6)
- **Priority**: Medium

#### NFR-PERF-04: Database Query Performance
- **Requirement**: Query execution < 500ms
- **Measurement**: Database profiling
- **Priority**: High

### 3.2 Security (NFR-SEC)

#### NFR-SEC-01: Password Security
- **Requirement**: Password harus di-hash menggunakan bcrypt/argon2
- **Priority**: High

#### NFR-SEC-02: SQL Injection Prevention
- **Requirement**: Gunakan prepared statements untuk semua queries
- **Priority**: High

#### NFR-SEC-03: XSS Prevention
- **Requirement**: Sanitize semua user input
- **Priority**: High

#### NFR-SEC-04: HTTPS
- **Requirement**: Semua traffic harus melalui HTTPS
- **Priority**: High

#### NFR-SEC-05: Session Management
- **Requirement**: Session timeout 30 menit, secure cookies
- **Priority**: High

### 3.3 Usability (NFR-USE)

#### NFR-USE-01: Responsive Design
- **Requirement**: Support mobile (320px+), tablet (768px+), desktop (1024px+)
- **Priority**: High

#### NFR-USE-02: Browser Compatibility
- **Requirement**: Support Chrome, Firefox, Safari, Edge (latest 2 versions)
- **Priority**: High

#### NFR-USE-03: Accessibility
- **Requirement**: WCAG 2.1 Level AA compliance
- **Priority**: Medium

#### NFR-USE-04: User Feedback
- **Requirement**: Loading indicators, error messages yang jelas
- **Priority**: High

### 3.4 Reliability (NFR-REL)

#### NFR-REL-01: Uptime
- **Requirement**: 99% uptime
- **Priority**: High

#### NFR-REL-02: Data Backup
- **Requirement**: Daily automated backup
- **Priority**: High

#### NFR-REL-03: Error Handling
- **Requirement**: Graceful error handling, no exposed stack traces
- **Priority**: High

### 3.5 Maintainability (NFR-MAIN)

#### NFR-MAIN-01: Code Documentation
- **Requirement**: Semua function harus memiliki docblock
- **Priority**: Medium

#### NFR-MAIN-02: Code Standards
- **Requirement**: Follow PSR-12 (PHP), ESLint (JavaScript)
- **Priority**: Medium

#### NFR-MAIN-03: Version Control
- **Requirement**: Git dengan semantic versioning
- **Priority**: High

### 3.6 Scalability (NFR-SCAL)

#### NFR-SCAL-01: Database Scalability
- **Requirement**: Support hingga 10,000 kost records
- **Priority**: Medium

#### NFR-SCAL-02: Horizontal Scaling
- **Requirement**: Architecture mendukung horizontal scaling
- **Priority**: Low

## 4. System Constraints

### 4.1 Technical Constraints
- **TC-01**: Backend harus menggunakan PHP 8.0+
- **TC-02**: Database harus MySQL/MariaDB compatible
- **TC-03**: Deployment di Vercel (serverless)
- **TC-04**: Frontend vanilla JavaScript (no heavy frameworks)

### 4.2 Business Constraints
- **BC-01**: Budget: Free tier services
- **BC-02**: Timeline: 7 minggu development
- **BC-03**: Team: 1-2 developers

### 4.3 Regulatory Constraints
- **RC-01**: GDPR compliance untuk data user
- **RC-02**: Indonesian data protection laws

## 5. User Stories

### 5.1 As a Student (User)
1. **US-01**: Sebagai mahasiswa, saya ingin mencari kost terdekat dari kampus agar mudah commute
2. **US-02**: Sebagai mahasiswa, saya ingin filter kost berdasarkan budget agar sesuai kemampuan
3. **US-03**: Sebagai mahasiswa, saya ingin melihat rekomendasi kost terbaik agar tidak perlu riset manual
4. **US-04**: Sebagai mahasiswa, saya ingin membandingkan beberapa kost agar bisa memilih yang paling sesuai
5. **US-05**: Sebagai mahasiswa, saya ingin melihat foto dan fasilitas kost agar tahu kondisi sebenarnya

### 5.2 As an Admin
1. **US-06**: Sebagai admin, saya ingin menambah data kost baru agar database selalu update
2. **US-07**: Sebagai admin, saya ingin mengatur bobot kriteria agar rekomendasi sesuai prioritas
3. **US-08**: Sebagai admin, saya ingin melihat statistik penggunaan agar tahu kost yang populer
4. **US-09**: Sebagai admin, saya ingin menghapus kost yang sudah tidak tersedia agar data akurat
5. **US-10**: Sebagai admin, saya ingin melihat detail perhitungan SPK agar bisa verify accuracy

## 6. Use Cases

### 6.1 UC-01: Get Kost Recommendations

**Actor**: User (Student)  
**Precondition**: User sudah login  
**Main Flow**:
1. User mengakses halaman rekomendasi
2. System menampilkan form preferensi (optional)
3. User submit preferensi atau skip
4. System menjalankan algoritma TOPSIS
5. System menampilkan top 10 rekomendasi dengan score
6. User dapat klik detail untuk info lengkap

**Alternative Flow**:
- 4a. Jika belum ada bobot kriteria, gunakan default weights
- 5a. Jika tidak ada kost yang match filter, tampilkan semua kost

**Postcondition**: User mendapat list rekomendasi

### 6.2 UC-02: Configure Criteria Weights

**Actor**: Admin  
**Precondition**: Admin sudah login  
**Main Flow**:
1. Admin mengakses halaman konfigurasi AHP
2. System menampilkan matriks perbandingan 6x6
3. Admin input nilai perbandingan (1-9 scale)
4. System auto-fill reciprocal values
5. Admin submit matriks
6. System hitung normalisasi dan consistency ratio
7. Jika CR < 0.1, system simpan bobot baru
8. System tampilkan success message

**Alternative Flow**:
- 7a. Jika CR >= 0.1, tampilkan error "Matriks tidak konsisten, silakan revisi"
- 7b. Admin dapat reset atau revisi input

**Postcondition**: Bobot kriteria baru tersimpan

### 6.3 UC-03: Manage Kost Data

**Actor**: Admin  
**Precondition**: Admin sudah login  
**Main Flow (Add)**:
1. Admin klik "Tambah Kost"
2. System tampilkan form input
3. Admin isi data kost dan upload foto
4. Admin submit form
5. System validasi input
6. System simpan ke database
7. System tampilkan success message

**Alternative Flow**:
- 5a. Jika validasi gagal, tampilkan error message
- 6a. Jika upload foto gagal, simpan tanpa foto

**Postcondition**: Kost baru ditambahkan

## 7. Acceptance Criteria

### 7.1 General
- [ ] Semua functional requirements terimplementasi
- [ ] Semua non-functional requirements terpenuhi
- [ ] UI sesuai dengan design mockup
- [ ] Responsive di mobile, tablet, desktop
- [ ] No critical bugs

### 7.2 SPK Accuracy
- [ ] Hasil TOPSIS konsisten dengan manual calculation
- [ ] AHP consistency ratio < 0.1
- [ ] Ranking berubah sesuai perubahan bobot

### 7.3 Performance
- [ ] Page load < 3 detik
- [ ] API response < 1 detik
- [ ] Support 100 concurrent users

### 7.4 Security
- [ ] Password di-hash
- [ ] No SQL injection vulnerability
- [ ] No XSS vulnerability
- [ ] HTTPS enforced

## 8. Dependencies

### 8.1 External Services
- **Vercel**: Hosting & serverless functions
- **Neon/PlanetScale**: Database hosting
- **Cloudinary** (optional): Image hosting
- **SendGrid** (optional): Email service

### 8.2 Libraries/Frameworks
- **PHP**: PDO, Composer
- **JavaScript**: Fetch API, Chart.js (untuk visualisasi)
- **CSS**: Bootstrap 5 atau custom CSS

## 9. Assumptions

1. User memiliki internet connection
2. User menggunakan modern browser
3. Admin memahami konsep AHP-TOPSIS
4. Data kost akurat dan up-to-date
5. Vercel free tier cukup untuk traffic awal

## 10. Out of Scope

Fitur berikut **TIDAK** termasuk dalam scope v1.0:
- Payment gateway untuk booking
- Real-time chat dengan pemilik kost
- Mobile app (native iOS/Android)
- Integration dengan Google Maps API
- Review & rating system dari user
- Notification system (email/push)
- Multi-language support
- Advanced analytics & reporting

---

**Document Version**: 1.0  
**Last Updated**: 2026-01-04  
**Status**: Draft  
**Approved By**: -
