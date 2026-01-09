# Backend Specification - SPK Engine
## Sistem Rekomendasi Kost (AHP-TOPSIS)

---

## 1. Backend Architecture

### 1.1 Architecture Pattern
- **Pattern**: MVC (Model-View-Controller)
- **Structure**:
  ```
  backend/
  ├── config/
  │   ├── database.php
  │   └── constants.php
  ├── models/
  │   ├── Kost.php
  │   ├── User.php
  │   └── SPK.php
  ├── controllers/
  │   ├── KostController.php
  │   ├── AuthController.php
  │   └── SPKController.php
  ├── services/
  │   ├── AHPService.php
  │   └── TOPSISService.php
  ├── utils/
  │   ├── Database.php
  │   ├── Validator.php
  │   └── Response.php
  └── api/
      └── index.php
  ```

### 1.2 Technology Stack
- **PHP Version**: 8.0+
- **Database**: PDO (MySQL/MariaDB)
- **Authentication**: JWT (JSON Web Tokens)
- **Validation**: Custom validator class
- **Error Handling**: Try-catch with custom exceptions

---

## 2. SPK Engine Specification

### 2.1 AHP (Analytical Hierarchy Process)

#### 2.1.1 Class: `AHPService`

**Purpose**: Menghitung bobot kriteria menggunakan metode AHP

**Methods**:

##### `calculateWeights(array $pairwiseMatrix): array`

**Input**:
```php
$pairwiseMatrix = [
    'Jarak Kampus' => [1, 2, 0.25, 1, 0.6667, 0.5],
    'Jarak Market' => [0.5, 1, 0.125, 0.5, 0.3333, 0.25],
    'Harga' => [4, 8, 1, 4, 2.6667, 2],
    'Kebersihan' => [1, 2, 0.25, 1, 0.6667, 0.5],
    'Keamanan' => [1.5, 3, 0.375, 1.5, 1, 0.75],
    'Fasilitas' => [2, 4, 0.5, 2, 1.3333, 1]
];
```

**Process**:
1. **Normalisasi Matriks**:
   ```php
   // Hitung total setiap kolom
   for ($j = 0; $j < $n; $j++) {
       $columnSum[$j] = array_sum(array_column($matrix, $j));
   }
   
   // Normalisasi: setiap elemen dibagi total kolom
   for ($i = 0; $i < $n; $i++) {
       for ($j = 0; $j < $n; $j++) {
           $normalized[$i][$j] = $matrix[$i][$j] / $columnSum[$j];
       }
   }
   ```

2. **Hitung Bobot (Priority Vector)**:
   ```php
   // Rata-rata setiap baris
   for ($i = 0; $i < $n; $i++) {
       $weights[$i] = array_sum($normalized[$i]) / $n;
   }
   ```

3. **Hitung λmax (Lambda Max)**:
   ```php
   // Matrix * Weights
   for ($i = 0; $i < $n; $i++) {
       $aw[$i] = 0;
       for ($j = 0; $j < $n; $j++) {
           $aw[$i] += $matrix[$i][$j] * $weights[$j];
       }
   }
   
   // λmax = average(aw[i] / weights[i])
   $lambdaMax = 0;
   for ($i = 0; $i < $n; $i++) {
       $lambdaMax += $aw[$i] / $weights[$i];
   }
   $lambdaMax /= $n;
   ```

4. **Hitung Consistency Index (CI)**:
   ```php
   $CI = ($lambdaMax - $n) / ($n - 1);
   ```

5. **Hitung Consistency Ratio (CR)**:
   ```php
   $RI = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
   $CR = $CI / $RI[$n - 1];
   ```

**Output**:
```php
[
    'weights' => [
        'jarak_kampus' => 0.1,
        'jarak_market' => 0.05,
        'harga' => 0.4,
        'kebersihan' => 0.1,
        'keamanan' => 0.15,
        'fasilitas' => 0.2
    ],
    'lambdaMax' => 6.0,
    'CI' => 0.0,
    'CR' => 0.0,
    'isConsistent' => true
]
```

**Validation**:
- CR < 0.1 → Konsisten
- CR >= 0.1 → Tidak konsisten, perlu revisi

---

##### `savePairwiseMatrix(array $matrix): bool`

**Purpose**: Menyimpan matriks perbandingan ke database

**SQL**:
```sql
TRUNCATE TABLE temp_bobot;

INSERT INTO temp_bobot (kriteria, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas)
VALUES (?, ?, ?, ?, ?, ?, ?);
```

---

##### `saveNormalizedWeights(array $normalized, array $weights): bool`

**Purpose**: Menyimpan hasil normalisasi dan bobot

**SQL**:
```sql
TRUNCATE TABLE temp_normalisasi_kriteria;

INSERT INTO temp_normalisasi_kriteria 
(kriteria, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas, avg, matrix_aw)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
```

---

### 2.2 TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution)

#### 2.2.1 Class: `TOPSISService`

**Purpose**: Menghitung ranking kost menggunakan metode TOPSIS

**Methods**:

##### `calculateRanking(array $alternatives, array $weights): array`

**Input**:
```php
$alternatives = [
    ['nama' => 'Kost A', 'jarak_kampus' => 1.2, 'jarak_market' => 0.5, 'harga' => 2500000, 'kebersihan' => 5, 'keamanan' => 4, 'fasilitas' => 5],
    ['nama' => 'Kost B', 'jarak_kampus' => 2.5, 'jarak_market' => 1.0, 'harga' => 1299000, 'kebersihan' => 4, 'keamanan' => 5, 'fasilitas' => 5],
    // ...
];

$weights = [
    'jarak_kampus' => 0.1,
    'jarak_market' => 0.05,
    'harga' => 0.4,
    'kebersihan' => 0.1,
    'keamanan' => 0.15,
    'fasilitas' => 0.2
];
```

**Process**:

1. **Normalisasi Matriks Keputusan**:
   ```php
   // Hitung akar kuadrat sum of squares untuk setiap kriteria
   foreach ($criteria as $criterion) {
       $sumSquares = 0;
       foreach ($alternatives as $alt) {
           $sumSquares += pow($alt[$criterion], 2);
       }
       $sqrtSum[$criterion] = sqrt($sumSquares);
   }
   
   // Normalisasi: r_ij = x_ij / sqrt(sum(x_ij^2))
   foreach ($alternatives as $i => $alt) {
       foreach ($criteria as $criterion) {
           $normalized[$i][$criterion] = $alt[$criterion] / $sqrtSum[$criterion];
       }
   }
   ```

2. **Pembobotan Matriks Ternormalisasi**:
   ```php
   // y_ij = r_ij * w_j
   foreach ($normalized as $i => $alt) {
       foreach ($criteria as $criterion) {
           $weighted[$i][$criterion] = $alt[$criterion] * $weights[$criterion];
       }
   }
   ```

3. **Tentukan Solusi Ideal Positif (A+) dan Negatif (A-)**:
   ```php
   $costCriteria = ['jarak_kampus', 'jarak_market', 'harga'];
   $benefitCriteria = ['kebersihan', 'keamanan', 'fasilitas'];
   
   foreach ($criteria as $criterion) {
       $values = array_column($weighted, $criterion);
       
       if (in_array($criterion, $costCriteria)) {
           // Cost: A+ = min, A- = max
           $idealPositive[$criterion] = min($values);
           $idealNegative[$criterion] = max($values);
       } else {
           // Benefit: A+ = max, A- = min
           $idealPositive[$criterion] = max($values);
           $idealNegative[$criterion] = min($values);
       }
   }
   ```

4. **Hitung Jarak ke Solusi Ideal (D+ dan D-)**:
   ```php
   foreach ($weighted as $i => $alt) {
       $dPlus = 0;
       $dMinus = 0;
       
       foreach ($criteria as $criterion) {
           $dPlus += pow($alt[$criterion] - $idealPositive[$criterion], 2);
           $dMinus += pow($alt[$criterion] - $idealNegative[$criterion], 2);
       }
       
       $dPositive[$i] = sqrt($dPlus);
       $dNegative[$i] = sqrt($dMinus);
   }
   ```

5. **Hitung Nilai Preferensi (V)**:
   ```php
   foreach ($alternatives as $i => $alt) {
       $preferenceValue[$i] = $dNegative[$i] / ($dPositive[$i] + $dNegative[$i]);
   }
   ```

6. **Ranking**:
   ```php
   arsort($preferenceValue); // Sort descending by value
   ```

**Output**:
```php
[
    [
        'rank' => 1,
        'nama' => 'Kost Eleora Cikunir Tipe A',
        'score' => 0.766,
        'dPositive' => 0.031,
        'dNegative' => 0.102
    ],
    [
        'rank' => 2,
        'nama' => 'Kost Ezra Tipe A',
        'score' => 0.741,
        'dPositive' => 0.040,
        'dNegative' => 0.116
    ],
    // ...
]
```

---

##### `saveNormalization(array $normalized): bool`

**Purpose**: Menyimpan matriks ternormalisasi

**SQL**:
```sql
TRUNCATE TABLE temp_normalisasi;

INSERT INTO temp_normalisasi 
(nama, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas)
VALUES (?, ?, ?, ?, ?, ?, ?);
```

---

##### `saveDistances(array $dPositive, array $dNegative): bool`

**Purpose**: Menyimpan D+ dan D-

**SQL**:
```sql
TRUNCATE TABLE temp_d_pos;
TRUNCATE TABLE temp_d_neg;

INSERT INTO temp_d_pos (nama, dPositif) VALUES (?, ?);
INSERT INTO temp_d_neg (nama, dNegatif) VALUES (?, ?);
```

---

##### `savePreferenceValues(array $preferenceValues): bool`

**Purpose**: Menyimpan nilai preferensi

**SQL**:
```sql
TRUNCATE TABLE temp_nilai_pref;

INSERT INTO temp_nilai_pref (nama, val) VALUES (?, ?);
```

---

## 3. API Endpoints

### 3.1 Authentication

#### POST `/api/auth/register`
**Request**:
```json
{
  "email": "user@example.com",
  "password": "password123",
  "nama": "John Doe",
  "telepon": "08123456789"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user_id": 1,
    "email": "user@example.com",
    "nama": "John Doe"
  }
}
```

---

#### POST `/api/auth/login`
**Request**:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 1,
      "email": "user@example.com",
      "nama": "John Doe",
      "role": "user"
    }
  }
}
```

---

### 3.2 Kost Management

#### GET `/api/kost`
**Query Parameters**:
- `page` (int): Page number (default: 1)
- `limit` (int): Items per page (default: 10)
- `search` (string): Search by nama
- `min_harga` (float): Minimum price
- `max_harga` (float): Maximum price
- `max_jarak_kampus` (float): Max distance to campus

**Response**:
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "nama": "Kost Papipul",
        "jarak_kampus": 1.2,
        "jarak_market": 0.5,
        "harga": 2500000,
        "kebersihan": 5,
        "keamanan": 4,
        "fasilitas": 5,
        "foto_utama": "https://..."
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 50,
      "items_per_page": 10
    }
  }
}
```

---

#### GET `/api/kost/{id}`
**Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nama": "Kost Papipul",
    "jarak_kampus": 1.2,
    "jarak_market": 0.5,
    "harga": 2500000,
    "kebersihan": 5,
    "keamanan": 4,
    "fasilitas": 5,
    "deskripsi": "Kost nyaman dengan fasilitas lengkap",
    "alamat": "Jl. Example No. 123",
    "foto_utama": "https://...",
    "images": [
      {"url": "https://...", "caption": "Kamar"},
      {"url": "https://...", "caption": "Kamar Mandi"}
    ]
  }
}
```

---

#### POST `/api/kost` (Admin only)
**Request**:
```json
{
  "nama": "Kost Baru",
  "jarak_kampus": 1.5,
  "jarak_market": 0.8,
  "harga": 1500000,
  "kebersihan": 4,
  "keamanan": 4,
  "fasilitas": 5,
  "deskripsi": "Kost nyaman",
  "alamat": "Jl. Example"
}
```

---

### 3.3 SPK Endpoints

#### POST `/api/spk/ahp/configure`
**Request**:
```json
{
  "pairwise_matrix": {
    "Jarak Kampus": [1, 2, 0.25, 1, 0.6667, 0.5],
    "Jarak Market": [0.5, 1, 0.125, 0.5, 0.3333, 0.25],
    "Harga": [4, 8, 1, 4, 2.6667, 2],
    "Kebersihan": [1, 2, 0.25, 1, 0.6667, 0.5],
    "Keamanan": [1.5, 3, 0.375, 1.5, 1, 0.75],
    "Fasilitas": [2, 4, 0.5, 2, 1.3333, 1]
  }
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "weights": {
      "jarak_kampus": 0.1,
      "jarak_market": 0.05,
      "harga": 0.4,
      "kebersihan": 0.1,
      "keamanan": 0.15,
      "fasilitas": 0.2
    },
    "consistency_ratio": 0.0,
    "is_consistent": true
  }
}
```

---

#### POST `/api/spk/topsis/calculate`
**Request**:
```json
{
  "filters": {
    "max_harga": 2000000,
    "max_jarak_kampus": 3.0
  }
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "recommendations": [
      {
        "rank": 1,
        "kost_id": 8,
        "nama": "Kost Eleora Cikunir Tipe A",
        "score": 0.766,
        "details": {
          "jarak_kampus": 2.5,
          "jarak_market": 1.0,
          "harga": 956000,
          "kebersihan": 4,
          "keamanan": 4,
          "fasilitas": 5
        }
      }
    ],
    "calculation_details": {
      "total_alternatives": 20,
      "filtered_alternatives": 15,
      "weights_used": {
        "jarak_kampus": 0.1,
        "jarak_market": 0.05,
        "harga": 0.4,
        "kebersihan": 0.1,
        "keamanan": 0.15,
        "fasilitas": 0.2
      }
    }
  }
}
```

---

#### GET `/api/spk/topsis/details/{kost_id}`
**Response**:
```json
{
  "success": true,
  "data": {
    "kost_nama": "Kost Eleora",
    "normalized_values": {
      "jarak_kampus": 0.028,
      "jarak_market": 0.013,
      "harga": 0.061,
      "kebersihan": 0.022,
      "keamanan": 0.034,
      "fasilitas": 0.051
    },
    "weighted_values": {
      "jarak_kampus": 0.0028,
      "jarak_market": 0.00065,
      "harga": 0.0244,
      "kebersihan": 0.0022,
      "keamanan": 0.0051,
      "fasilitas": 0.0102
    },
    "d_positive": 0.031,
    "d_negative": 0.102,
    "preference_value": 0.766,
    "rank": 1
  }
}
```

---

## 4. Error Handling

### 4.1 Error Response Format
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid input data",
    "details": {
      "harga": "Price must be greater than 0",
      "kebersihan": "Rating must be between 1 and 5"
    }
  }
}
```

### 4.2 Error Codes
- `VALIDATION_ERROR`: Input validation failed
- `AUTH_ERROR`: Authentication failed
- `PERMISSION_ERROR`: Insufficient permissions
- `NOT_FOUND`: Resource not found
- `DATABASE_ERROR`: Database operation failed
- `CALCULATION_ERROR`: SPK calculation error
- `INCONSISTENT_MATRIX`: AHP matrix not consistent (CR >= 0.1)

---

## 5. Performance Optimization

### 5.1 Caching Strategy
- Cache AHP weights (invalidate on update)
- Cache TOPSIS results for 5 minutes
- Use Redis/Memcached for production

### 5.2 Database Optimization
- Index frequently queried columns
- Use prepared statements
- Batch inserts for TOPSIS calculations

### 5.3 Query Optimization
```php
// Bad: N+1 query
foreach ($kosts as $kost) {
    $images = getImages($kost['id']);
}

// Good: Single query with JOIN
$kosts = getKostsWithImages();
```

---

**Document Version**: 1.0  
**Last Updated**: 2026-01-04  
**Author**: Backend Team
