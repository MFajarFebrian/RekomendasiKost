# Deployment Guide - Vercel
## Sistem Rekomendasi Kost

---

## 1. Overview

### 1.1 Challenge
Vercel secara native tidak mendukung PHP. Proyek ini menggunakan PHP untuk backend, sehingga perlu strategi khusus untuk deployment.

### 1.2 Solutions

Ada 3 pendekatan yang bisa digunakan:

| Approach | Pros | Cons | Recommended |
|----------|------|------|-------------|
| **1. Vercel PHP Runtime** | Minimal code changes | Limited PHP features, cold starts | ⭐⭐⭐ Good |
| **2. Convert to Node.js API** | Full Vercel support, better performance | Requires complete rewrite | ⭐⭐⭐⭐⭐ Best |
| **3. Hybrid (Static + External API)** | Frontend on Vercel, Backend elsewhere | Need separate hosting for backend | ⭐⭐ OK |

**Recommendation**: Use **Approach 2 (Node.js API)** for production, or **Approach 1 (PHP Runtime)** for quick deployment.

---

## 2. Approach 1: Vercel PHP Runtime

### 2.1 Setup

#### Install Vercel PHP Runtime

```bash
npm init -y
npm install vercel-php
```

#### Create `vercel.json`

```json
{
  "functions": {
    "api/**/*.php": {
      "runtime": "vercel-php@0.6.0"
    }
  },
  "routes": [
    {
      "src": "/api/(.*)",
      "dest": "/api/$1.php"
    },
    {
      "src": "/(.*)",
      "dest": "/$1"
    }
  ],
  "env": {
    "DB_HOST": "@db_host",
    "DB_NAME": "@db_name",
    "DB_USER": "@db_user",
    "DB_PASS": "@db_pass"
  }
}
```

### 2.2 File Structure

```
project/
├── api/
│   ├── kost/
│   │   ├── index.php          # GET /api/kost
│   │   └── [id].php           # GET /api/kost/123
│   ├── spk/
│   │   ├── ahp.php
│   │   └── topsis.php
│   └── auth/
│       ├── login.php
│       └── register.php
├── public/
│   ├── index.html
│   ├── assets/
│   └── pages/
├── vercel.json
└── package.json
```

### 2.3 PHP API Example

**`api/kost/index.php`**:
```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database connection
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all kost
    $stmt = $pdo->query("SELECT * FROM kost WHERE is_active = 1");
    $kosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $kosts
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => ['message' => $e->getMessage()]
    ]);
}
?>
```

### 2.4 Database Setup (Neon PostgreSQL)

1. **Create Neon Account**: https://neon.tech
2. **Create Database**
3. **Get Connection String**:
   ```
   postgres://user:pass@ep-xxx.us-east-2.aws.neon.tech/dbname
   ```

4. **Convert MySQL to PostgreSQL**:
   - Change `AUTO_INCREMENT` → `SERIAL`
   - Change `DOUBLE` → `NUMERIC` or `DOUBLE PRECISION`
   - Change `TINYINT(1)` → `BOOLEAN`

5. **Add to Vercel Environment Variables**:
   ```bash
   vercel env add DB_HOST
   vercel env add DB_NAME
   vercel env add DB_USER
   vercel env add DB_PASS
   ```

---

## 3. Approach 2: Convert to Node.js API (Recommended)

### 3.1 Setup

#### Initialize Node.js Project

```bash
npm init -y
npm install express mysql2 dotenv bcrypt jsonwebtoken cors
```

#### Create `api/index.js`

```javascript
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
require('dotenv').config();

const app = express();
app.use(cors());
app.use(express.json());

// Database connection pool
const pool = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10
});

// Routes
app.get('/api/kost', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM kost WHERE is_active = 1');
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, error: { message: error.message } });
  }
});

app.get('/api/kost/:id', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM kost WHERE id = ?', [req.params.id]);
    if (rows.length === 0) {
      return res.status(404).json({ success: false, error: { message: 'Not found' } });
    }
    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, error: { message: error.message } });
  }
});

// SPK Routes
const spkRouter = require('./routes/spk');
app.use('/api/spk', spkRouter);

// Export for Vercel
module.exports = app;
```

### 3.2 SPK Service in Node.js

**`api/services/topsis.js`**:
```javascript
class TOPSISService {
  static calculateRanking(alternatives, weights) {
    const criteria = ['jarak_kampus', 'jarak_market', 'harga', 'kebersihan', 'keamanan', 'fasilitas'];
    const costCriteria = ['jarak_kampus', 'jarak_market', 'harga'];
    
    // 1. Normalization
    const normalized = this.normalize(alternatives, criteria);
    
    // 2. Weighted normalization
    const weighted = this.applyWeights(normalized, weights, criteria);
    
    // 3. Ideal solutions
    const { idealPositive, idealNegative } = this.getIdealSolutions(weighted, criteria, costCriteria);
    
    // 4. Calculate distances
    const { dPositive, dNegative } = this.calculateDistances(weighted, idealPositive, idealNegative, criteria);
    
    // 5. Preference values
    const preferenceValues = this.calculatePreferenceValues(dPositive, dNegative);
    
    // 6. Ranking
    return this.rankAlternatives(alternatives, preferenceValues, dPositive, dNegative);
  }
  
  static normalize(alternatives, criteria) {
    const normalized = [];
    const sqrtSums = {};
    
    // Calculate sqrt of sum of squares
    criteria.forEach(criterion => {
      const sumSquares = alternatives.reduce((sum, alt) => sum + Math.pow(alt[criterion], 2), 0);
      sqrtSums[criterion] = Math.sqrt(sumSquares);
    });
    
    // Normalize
    alternatives.forEach(alt => {
      const normAlt = { nama: alt.nama };
      criteria.forEach(criterion => {
        normAlt[criterion] = alt[criterion] / sqrtSums[criterion];
      });
      normalized.push(normAlt);
    });
    
    return normalized;
  }
  
  static applyWeights(normalized, weights, criteria) {
    return normalized.map(alt => {
      const weighted = { nama: alt.nama };
      criteria.forEach(criterion => {
        weighted[criterion] = alt[criterion] * weights[criterion];
      });
      return weighted;
    });
  }
  
  static getIdealSolutions(weighted, criteria, costCriteria) {
    const idealPositive = {};
    const idealNegative = {};
    
    criteria.forEach(criterion => {
      const values = weighted.map(alt => alt[criterion]);
      
      if (costCriteria.includes(criterion)) {
        idealPositive[criterion] = Math.min(...values);
        idealNegative[criterion] = Math.max(...values);
      } else {
        idealPositive[criterion] = Math.max(...values);
        idealNegative[criterion] = Math.min(...values);
      }
    });
    
    return { idealPositive, idealNegative };
  }
  
  static calculateDistances(weighted, idealPositive, idealNegative, criteria) {
    const dPositive = [];
    const dNegative = [];
    
    weighted.forEach(alt => {
      let dPlus = 0;
      let dMinus = 0;
      
      criteria.forEach(criterion => {
        dPlus += Math.pow(alt[criterion] - idealPositive[criterion], 2);
        dMinus += Math.pow(alt[criterion] - idealNegative[criterion], 2);
      });
      
      dPositive.push({ nama: alt.nama, value: Math.sqrt(dPlus) });
      dNegative.push({ nama: alt.nama, value: Math.sqrt(dMinus) });
    });
    
    return { dPositive, dNegative };
  }
  
  static calculatePreferenceValues(dPositive, dNegative) {
    return dPositive.map((dp, i) => ({
      nama: dp.nama,
      value: dNegative[i].value / (dp.value + dNegative[i].value)
    }));
  }
  
  static rankAlternatives(alternatives, preferenceValues, dPositive, dNegative) {
    const results = preferenceValues.map((pv, i) => ({
      nama: pv.nama,
      score: pv.value,
      dPositive: dPositive[i].value,
      dNegative: dNegative[i].value,
      details: alternatives.find(alt => alt.nama === pv.nama)
    }));
    
    // Sort by score descending
    results.sort((a, b) => b.score - a.score);
    
    // Add rank
    results.forEach((result, i) => {
      result.rank = i + 1;
    });
    
    return results;
  }
}

module.exports = TOPSISService;
```

### 3.3 Vercel Configuration

**`vercel.json`**:
```json
{
  "version": 2,
  "builds": [
    {
      "src": "api/index.js",
      "use": "@vercel/node"
    },
    {
      "src": "public/**",
      "use": "@vercel/static"
    }
  ],
  "routes": [
    {
      "src": "/api/(.*)",
      "dest": "/api/index.js"
    },
    {
      "src": "/(.*)",
      "dest": "/public/$1"
    }
  ],
  "env": {
    "DB_HOST": "@db_host",
    "DB_NAME": "@db_name",
    "DB_USER": "@db_user",
    "DB_PASS": "@db_pass",
    "JWT_SECRET": "@jwt_secret"
  }
}
```

---

## 4. Database Options

### 4.1 Neon PostgreSQL (Recommended)

**Pros**:
- Generous free tier (0.5 GB storage, 1 GB data transfer)
- Serverless, auto-scaling
- PostgreSQL compatible

**Setup**:
1. Sign up: https://neon.tech
2. Create project
3. Get connection string
4. Migrate schema from MySQL to PostgreSQL

**Migration Script**:
```sql
-- Convert AUTO_INCREMENT to SERIAL
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  email VARCHAR(191) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(191) NOT NULL,
  role VARCHAR(20) DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Convert DOUBLE to NUMERIC
CREATE TABLE kost (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  jarak_kampus NUMERIC(10,2) NOT NULL,
  jarak_market NUMERIC(10,2) NOT NULL,
  harga NUMERIC(12,2) NOT NULL,
  kebersihan INTEGER CHECK (kebersihan BETWEEN 1 AND 5),
  keamanan INTEGER CHECK (keamanan BETWEEN 1 AND 5),
  fasilitas INTEGER CHECK (fasilitas BETWEEN 1 AND 5),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 4.2 PlanetScale MySQL

**Pros**:
- MySQL compatible (minimal migration)
- Generous free tier (5 GB storage, 1 billion row reads/month)
- Built on Vitess

**Setup**:
1. Sign up: https://planetscale.com
2. Create database
3. Get connection string
4. Import schema

**Note**: PlanetScale doesn't support foreign keys. Handle referential integrity in application layer.

---

### 4.3 Supabase

**Pros**:
- PostgreSQL + built-in auth
- Real-time subscriptions
- Free tier: 500 MB database, 1 GB file storage

**Setup**:
1. Sign up: https://supabase.com
2. Create project
3. Use built-in auth (can replace custom user management)
4. Get connection string

---

## 5. Deployment Steps

### 5.1 Prepare Project

```bash
# Install Vercel CLI
npm install -g vercel

# Login
vercel login

# Initialize project
vercel init
```

### 5.2 Set Environment Variables

```bash
vercel env add DB_HOST production
vercel env add DB_NAME production
vercel env add DB_USER production
vercel env add DB_PASS production
vercel env add JWT_SECRET production
```

### 5.3 Deploy

```bash
# Deploy to preview
vercel

# Deploy to production
vercel --prod
```

### 5.4 Custom Domain (Optional)

```bash
vercel domains add yourdomain.com
```

---

## 6. CI/CD with GitHub

### 6.1 Connect Repository

1. Go to Vercel Dashboard
2. Import Git Repository
3. Select GitHub repo
4. Configure build settings

### 6.2 Auto-deploy

- **Push to `main`** → Deploy to production
- **Push to other branches** → Deploy to preview

### 6.3 GitHub Actions (Optional)

**`.github/workflows/deploy.yml`**:
```yaml
name: Deploy to Vercel

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: amondnet/vercel-action@v20
        with:
          vercel-token: ${{ secrets.VERCEL_TOKEN }}
          vercel-org-id: ${{ secrets.ORG_ID }}
          vercel-project-id: ${{ secrets.PROJECT_ID }}
          vercel-args: '--prod'
```

---

## 7. Performance Optimization

### 7.1 Caching

```javascript
// Cache TOPSIS results
const cache = new Map();

app.post('/api/spk/topsis/calculate', async (req, res) => {
  const cacheKey = JSON.stringify(req.body);
  
  if (cache.has(cacheKey)) {
    return res.json({ success: true, data: cache.get(cacheKey), cached: true });
  }
  
  const results = await TOPSISService.calculateRanking(/* ... */);
  cache.set(cacheKey, results);
  
  // Expire after 5 minutes
  setTimeout(() => cache.delete(cacheKey), 5 * 60 * 1000);
  
  res.json({ success: true, data: results });
});
```

### 7.2 Database Connection Pooling

```javascript
const pool = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});
```

### 7.3 Image Optimization

Use Vercel Image Optimization:

```html
<img src="/_next/image?url=/path/to/image.jpg&w=640&q=75" alt="Kost">
```

Or use Cloudinary:

```javascript
const cloudinary = require('cloudinary').v2;

cloudinary.config({
  cloud_name: process.env.CLOUDINARY_NAME,
  api_key: process.env.CLOUDINARY_KEY,
  api_secret: process.env.CLOUDINARY_SECRET
});
```

---

## 8. Monitoring & Logging

### 8.1 Vercel Analytics

Enable in Vercel Dashboard → Analytics

### 8.2 Error Tracking (Sentry)

```bash
npm install @sentry/node
```

```javascript
const Sentry = require('@sentry/node');

Sentry.init({
  dsn: process.env.SENTRY_DSN,
  environment: process.env.VERCEL_ENV
});

app.use(Sentry.Handlers.errorHandler());
```

---

## 9. Troubleshooting

### 9.1 Cold Starts

**Problem**: Serverless functions have cold start delay

**Solution**:
- Keep functions warm with periodic pings
- Use connection pooling
- Optimize function size

### 9.2 Database Connection Limits

**Problem**: Too many connections

**Solution**:
- Use connection pooling
- Implement connection retry logic
- Use serverless-friendly database (Neon, PlanetScale)

### 9.3 CORS Issues

**Problem**: CORS errors in browser

**Solution**:
```javascript
app.use(cors({
  origin: process.env.FRONTEND_URL || '*',
  credentials: true
}));
```

---

**Document Version**: 1.0  
**Last Updated**: 2026-01-04  
**Author**: DevOps Team
