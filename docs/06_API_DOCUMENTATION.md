# API Documentation
## Sistem Rekomendasi Kost - RESTful API

---

## Base URL

**Development**: `http://localhost/RekomendasiKost/api`  
**Production**: `https://your-domain.vercel.app/api`

---

## Authentication

### Bearer Token

Most endpoints require authentication using JWT Bearer token.

**Header**:
```
Authorization: Bearer <token>
```

**Token Expiration**: 24 hours

---

## Response Format

### Success Response

```json
{
  "success": true,
  "data": { /* response data */ },
  "message": "Optional success message"
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "details": { /* optional error details */ }
  }
}
```

---

## API Endpoints

## 1. Authentication

### 1.1 Register

**POST** `/auth/register`

**Request Body**:
```json
{
  "email": "user@example.com",
  "password": "password123",
  "nama": "John Doe",
  "telepon": "08123456789"
}
```

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "email": "user@example.com",
    "nama": "John Doe",
    "role": "user"
  },
  "message": "Registration successful"
}
```

**Errors**:
- `400` - Validation error (email already exists, invalid format)
- `500` - Server error

---

### 1.2 Login

**POST** `/auth/login`

**Request Body**:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response** (200 OK):
```json
{
  "success": true,
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

**Errors**:
- `401` - Invalid credentials
- `403` - Account inactive

---

### 1.3 Logout

**POST** `/auth/logout`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

### 1.4 Get Current User

**GET** `/auth/me`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "email": "user@example.com",
    "nama": "John Doe",
    "telepon": "08123456789",
    "role": "user",
    "foto_profil": "https://...",
    "created_at": "2026-01-04T12:00:00Z"
  }
}
```

---

## 2. Kost Management

### 2.1 Get All Kost

**GET** `/kost`

**Query Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| page | integer | No | Page number (default: 1) |
| limit | integer | No | Items per page (default: 10, max: 100) |
| search | string | No | Search by nama |
| min_harga | number | No | Minimum price filter |
| max_harga | number | No | Maximum price filter |
| max_jarak_kampus | number | No | Maximum distance to campus |
| min_kebersihan | integer | No | Minimum cleanliness rating (1-5) |
| min_keamanan | integer | No | Minimum security rating (1-5) |
| min_fasilitas | integer | No | Minimum facility rating (1-5) |
| sort_by | string | No | Sort field (harga, jarak_kampus, created_at) |
| sort_order | string | No | Sort order (asc, desc) |

**Example Request**:
```
GET /kost?page=1&limit=10&max_harga=2000000&sort_by=harga&sort_order=asc
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "nama": "Kost Papipul Pakuwon Mezanine",
        "jarak_kampus": 1.2,
        "jarak_market": 0.5,
        "harga": 2500000,
        "kebersihan": 5,
        "keamanan": 4,
        "fasilitas": 5,
        "foto_utama": "https://...",
        "alamat": "Jl. Example No. 123",
        "created_at": "2026-01-01T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 50,
      "items_per_page": 10,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

---

### 2.2 Get Kost by ID

**GET** `/kost/{id}`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nama": "Kost Papipul Pakuwon Mezanine",
    "jarak_kampus": 1.2,
    "jarak_market": 0.5,
    "harga": 2500000,
    "kebersihan": 5,
    "keamanan": 4,
    "fasilitas": 5,
    "deskripsi": "Kost nyaman dengan fasilitas lengkap...",
    "alamat": "Jl. Example No. 123",
    "latitude": -6.2088,
    "longitude": 106.8456,
    "foto_utama": "https://...",
    "images": [
      {
        "id": 1,
        "url": "https://...",
        "caption": "Kamar Tidur",
        "urutan": 1
      },
      {
        "id": 2,
        "url": "https://...",
        "caption": "Kamar Mandi",
        "urutan": 2
      }
    ],
    "is_active": true,
    "created_at": "2026-01-01T10:00:00Z",
    "updated_at": "2026-01-04T12:00:00Z"
  }
}
```

**Errors**:
- `404` - Kost not found

---

### 2.3 Create Kost (Admin Only)

**POST** `/kost`

**Headers**: `Authorization: Bearer <admin_token>`

**Request Body**:
```json
{
  "nama": "Kost Baru",
  "jarak_kampus": 1.5,
  "jarak_market": 0.8,
  "harga": 1500000,
  "kebersihan": 4,
  "keamanan": 4,
  "fasilitas": 5,
  "deskripsi": "Kost nyaman dan strategis",
  "alamat": "Jl. Example No. 456",
  "latitude": -6.2088,
  "longitude": 106.8456,
  "foto_utama": "https://..."
}
```

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 21,
    "nama": "Kost Baru",
    /* ... other fields ... */
  },
  "message": "Kost created successfully"
}
```

**Errors**:
- `400` - Validation error
- `401` - Unauthorized
- `403` - Forbidden (not admin)

---

### 2.4 Update Kost (Admin Only)

**PUT** `/kost/{id}`

**Headers**: `Authorization: Bearer <admin_token>`

**Request Body**: Same as Create Kost

**Response** (200 OK):
```json
{
  "success": true,
  "data": { /* updated kost data */ },
  "message": "Kost updated successfully"
}
```

---

### 2.5 Delete Kost (Admin Only)

**DELETE** `/kost/{id}`

**Headers**: `Authorization: Bearer <admin_token>`

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Kost deleted successfully"
}
```

---

## 3. SPK - AHP

### 3.1 Get Current Weights

**GET** `/spk/ahp/weights`

**Response** (200 OK):
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
    "last_updated": "2026-01-04T10:00:00Z"
  }
}
```

---

### 3.2 Configure AHP Weights (Admin Only)

**POST** `/spk/ahp/configure`

**Headers**: `Authorization: Bearer <admin_token>`

**Request Body**:
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

**Response** (200 OK):
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
    "lambda_max": 6.0,
    "consistency_index": 0.0,
    "consistency_ratio": 0.0,
    "is_consistent": true
  },
  "message": "AHP weights configured successfully"
}
```

**Errors**:
- `400` - Inconsistent matrix (CR >= 0.1)
- `401` - Unauthorized
- `403` - Forbidden (not admin)

---

### 3.3 Get AHP Calculation Details

**GET** `/spk/ahp/details`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "pairwise_matrix": { /* 6x6 matrix */ },
    "normalized_matrix": { /* normalized values */ },
    "weights": { /* final weights */ },
    "lambda_max": 6.0,
    "consistency_index": 0.0,
    "consistency_ratio": 0.0,
    "random_index": 1.24
  }
}
```

---

## 4. SPK - TOPSIS

### 4.1 Calculate Recommendations

**POST** `/spk/topsis/calculate`

**Request Body** (optional filters):
```json
{
  "filters": {
    "max_harga": 2000000,
    "max_jarak_kampus": 3.0,
    "min_kebersihan": 3,
    "min_keamanan": 3,
    "min_fasilitas": 3
  },
  "limit": 10
}
```

**Response** (200 OK):
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
        "d_positive": 0.031,
        "d_negative": 0.102,
        "details": {
          "jarak_kampus": 2.5,
          "jarak_market": 1.0,
          "harga": 956000,
          "kebersihan": 4,
          "keamanan": 4,
          "fasilitas": 5,
          "foto_utama": "https://..."
        }
      },
      {
        "rank": 2,
        "kost_id": 12,
        "nama": "Kost Ezra Tipe A",
        "score": 0.741,
        "d_positive": 0.040,
        "d_negative": 0.116,
        "details": { /* ... */ }
      }
    ],
    "calculation_metadata": {
      "total_alternatives": 20,
      "filtered_alternatives": 15,
      "weights_used": {
        "jarak_kampus": 0.1,
        "jarak_market": 0.05,
        "harga": 0.4,
        "kebersihan": 0.1,
        "keamanan": 0.15,
        "fasilitas": 0.2
      },
      "calculated_at": "2026-01-04T12:30:00Z",
      "execution_time_ms": 45
    }
  }
}
```

---

### 4.2 Get TOPSIS Calculation Details

**GET** `/spk/topsis/details/{kost_id}`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "kost_id": 8,
    "kost_nama": "Kost Eleora Cikunir Tipe A",
    "original_values": {
      "jarak_kampus": 2.5,
      "jarak_market": 1.0,
      "harga": 956000,
      "kebersihan": 4,
      "keamanan": 4,
      "fasilitas": 5
    },
    "normalized_values": {
      "jarak_kampus": 0.028031766863111,
      "jarak_market": 0.013076415684014,
      "harga": 0.060637036100999,
      "kebersihan": 0.021725180800562,
      "keamanan": 0.034412331403762,
      "fasilitas": 0.051164195188689
    },
    "weighted_normalized_values": {
      "jarak_kampus": 0.0028031766863111,
      "jarak_market": 0.00065382078420068,
      "harga": 0.024254814440400,
      "kebersihan": 0.0021725180800562,
      "keamanan": 0.0051618497105643,
      "fasilitas": 0.010232839037738
    },
    "ideal_positive": {
      "jarak_kampus": 0.0005606353372622,
      "jarak_market": 0.0002615283136803,
      "harga": 0.044399503421233,
      "kebersihan": 0.027156476000703,
      "keamanan": 0.043015414254703,
      "fasilitas": 0.051164195188689
    },
    "ideal_negative": {
      "jarak_kampus": 0.039244473608355,
      "jarak_market": 0.01961462352602,
      "harga": 0.11734154475612,
      "kebersihan": 0.016293885600422,
      "keamanan": 0.017206165701881,
      "fasilitas": 0.030698517113213
    },
    "d_positive": 0.031297027558658,
    "d_negative": 0.1024873938512,
    "preference_value": 0.76606373725101,
    "rank": 1
  }
}
```

---

## 5. User Preferences

### 5.1 Get User Preferences

**GET** `/preferences`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "max_harga": 2000000,
    "max_jarak_kampus": 3.0,
    "min_kebersihan": 4,
    "min_keamanan": 4,
    "min_fasilitas": 3
  }
}
```

---

### 5.2 Update User Preferences

**PUT** `/preferences`

**Headers**: `Authorization: Bearer <token>`

**Request Body**:
```json
{
  "max_harga": 2000000,
  "max_jarak_kampus": 3.0,
  "min_kebersihan": 4,
  "min_keamanan": 4,
  "min_fasilitas": 3
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": { /* updated preferences */ },
  "message": "Preferences updated successfully"
}
```

---

## 6. Statistics (Admin Only)

### 6.1 Get Dashboard Statistics

**GET** `/stats/dashboard`

**Headers**: `Authorization: Bearer <admin_token>`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "total_kost": 20,
    "total_users": 150,
    "calculations_today": 45,
    "calculations_total": 1250,
    "average_score": 0.65,
    "popular_kost": [
      {
        "kost_id": 8,
        "nama": "Kost Eleora",
        "view_count": 320
      }
    ],
    "price_distribution": {
      "0-1000000": 5,
      "1000000-1500000": 8,
      "1500000-2000000": 4,
      "2000000+": 3
    }
  }
}
```

---

## 7. Calculation History

### 7.1 Get Calculation History

**GET** `/history`

**Headers**: `Authorization: Bearer <token>`

**Query Parameters**:
- `page` (integer): Page number
- `limit` (integer): Items per page
- `type` (string): Filter by type (ahp, topsis)

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "calculation_type": "topsis",
        "input_data": { /* filters used */ },
        "result_data": { /* top 3 results */ },
        "execution_time": 0.045,
        "created_at": "2026-01-04T12:30:00Z"
      }
    ],
    "pagination": { /* ... */ }
  }
}
```

---

## Error Codes Reference

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `VALIDATION_ERROR` | 400 | Input validation failed |
| `AUTH_ERROR` | 401 | Authentication failed |
| `PERMISSION_ERROR` | 403 | Insufficient permissions |
| `NOT_FOUND` | 404 | Resource not found |
| `CONFLICT` | 409 | Resource conflict (e.g., duplicate email) |
| `INCONSISTENT_MATRIX` | 400 | AHP matrix CR >= 0.1 |
| `DATABASE_ERROR` | 500 | Database operation failed |
| `CALCULATION_ERROR` | 500 | SPK calculation error |
| `SERVER_ERROR` | 500 | Internal server error |

---

## Rate Limiting

- **Limit**: 100 requests per minute per IP
- **Header**: `X-RateLimit-Remaining`, `X-RateLimit-Reset`

**Response** (429 Too Many Requests):
```json
{
  "success": false,
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Too many requests. Please try again later.",
    "retry_after": 60
  }
}
```

---

## Pagination

All list endpoints support pagination:

**Query Parameters**:
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 10, max: 100)

**Response**:
```json
{
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_items": 50,
    "items_per_page": 10,
    "has_next": true,
    "has_prev": false
  }
}
```

---

## CORS

**Allowed Origins**: Configured in environment variables

**Allowed Methods**: `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`

**Allowed Headers**: `Content-Type`, `Authorization`

---

## Webhooks (Future)

Planned for future releases:
- New kost added
- Calculation completed
- Weights updated

---

**API Version**: 1.0  
**Last Updated**: 2026-01-04  
**Base URL**: `/api`
