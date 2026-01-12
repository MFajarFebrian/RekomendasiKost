# Frontend Specification
## Sistem Rekomendasi Kost

---

## 1. Frontend Architecture

### 1.1 Technology Stack
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with custom properties
- **JavaScript (ES6+)**: Vanilla JS, no heavy frameworks
- **Optional**: Bootstrap 5 for responsive grid

### 1.2 File Structure
```
frontend/
├── index.html
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   ├── components.css
│   │   └── responsive.css
│   ├── js/
│   │   ├── app.js
│   │   ├── api.js
│   │   ├── spk.js
│   │   └── utils.js
│   └── images/
│       ├── logo.png
│       └── icons/
├── pages/
│   ├── home.html
│   ├── kost-list.html
│   ├── kost-detail.html
│   ├── recommendations.html
│   ├── comparison.html
│   ├── login.html
│   └── admin/
│       ├── dashboard.html
│       ├── manage-kost.html
│       └── configure-ahp.html
└── components/
    ├── navbar.html
    ├── footer.html
    └── card.html
```

---

## 2. Design System

### 2.1 Color Palette

Based on the provided template (Veco design):

```css
:root {
  /* Primary Colors */
  --primary-purple: #6C63FF;
  --primary-blue: #4A90E2;
  --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  
  /* Secondary Colors */
  --secondary-light: #F8F9FA;
  --secondary-gray: #6C757D;
  --secondary-dark: #343A40;
  
  /* Accent Colors */
  --accent-success: #28A745;
  --accent-warning: #FFC107;
  --accent-danger: #DC3545;
  --accent-info: #17A2B8;
  
  /* Neutral Colors */
  --white: #FFFFFF;
  --light-gray: #F5F5F5;
  --medium-gray: #E0E0E0;
  --dark-gray: #333333;
  --black: #000000;
  
  /* Glassmorphism */
  --glass-bg: rgba(255, 255, 255, 0.1);
  --glass-border: rgba(255, 255, 255, 0.2);
  --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
}
```

### 2.2 Typography

```css
:root {
  /* Font Families */
  --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --font-heading: 'Outfit', 'Inter', sans-serif;
  --font-mono: 'Fira Code', 'Courier New', monospace;
  
  /* Font Sizes */
  --fs-xs: 0.75rem;    /* 12px */
  --fs-sm: 0.875rem;   /* 14px */
  --fs-base: 1rem;     /* 16px */
  --fs-lg: 1.125rem;   /* 18px */
  --fs-xl: 1.25rem;    /* 20px */
  --fs-2xl: 1.5rem;    /* 24px */
  --fs-3xl: 1.875rem;  /* 30px */
  --fs-4xl: 2.25rem;   /* 36px */
  --fs-5xl: 3rem;      /* 48px */
  
  /* Font Weights */
  --fw-light: 300;
  --fw-regular: 400;
  --fw-medium: 500;
  --fw-semibold: 600;
  --fw-bold: 700;
  
  /* Line Heights */
  --lh-tight: 1.25;
  --lh-normal: 1.5;
  --lh-relaxed: 1.75;
}
```

### 2.3 Spacing System

```css
:root {
  --space-1: 0.25rem;  /* 4px */
  --space-2: 0.5rem;   /* 8px */
  --space-3: 0.75rem;  /* 12px */
  --space-4: 1rem;     /* 16px */
  --space-5: 1.5rem;   /* 24px */
  --space-6: 2rem;     /* 32px */
  --space-8: 3rem;     /* 48px */
  --space-10: 4rem;    /* 64px */
  --space-12: 6rem;    /* 96px */
}
```

### 2.4 Border Radius

```css
:root {
  --radius-sm: 0.25rem;   /* 4px */
  --radius-md: 0.5rem;    /* 8px */
  --radius-lg: 1rem;      /* 16px */
  --radius-xl: 1.5rem;    /* 24px */
  --radius-full: 9999px;  /* Fully rounded */
}
```

### 2.5 Shadows

```css
:root {
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
```

---

## 3. Page Specifications

### 3.1 Home Page (`index.html`)

**Sections**:

1. **Hero Section**
   - Large heading: "We are the Best Professional Creative Agency"
   - Subheading with description
   - CTA button: "Get Started"
   - Hero illustration (isometric design)

2. **Services Section**
   - Title: "Choose our creative services"
   - Grid of 6 service cards:
     - Jarak Kampus (icon)
     - Jarak Market (icon)
     - Harga (icon)
     - Kebersihan (icon)
     - Keamanan (icon)
     - Fasilitas (icon)

3. **How It Works**
   - Step-by-step process
   - AHP-TOPSIS explanation (simplified)

4. **Featured Kost**
   - Top 6 recommended kost
   - Card with image, name, price, rating

**HTML Structure**:
```html
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Rekomendasi Kost</title>
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <a href="/" class="logo">Veco</a>
      <ul class="nav-menu">
        <li><a href="/">Home</a></li>
        <li><a href="/pages/kost-list.html">Our Work</a></li>
        <li><a href="/pages/recommendations.html">Pages</a></li>
        <li><a href="/pages/login.html">Contact us</a></li>
      </ul>
    </div>
  </nav>

  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <p class="hero-subtitle">We are professional</p>
        <h1 class="hero-title">We are the Best Professional Creative Agency</h1>
        <p class="hero-description">Lorem ipsum dolor sit amet...</p>
        <button class="btn btn-primary">Get Started</button>
      </div>
      <div class="hero-image">
        <img src="assets/images/hero-illustration.svg" alt="Hero">
      </div>
    </div>
  </section>

  <!-- More sections... -->
</body>
</html>
```

---

### 3.2 Kost List Page (`kost-list.html`)

**Features**:
- Search bar
- Filter sidebar (harga, jarak, rating)
- Sort dropdown (harga, jarak_kampus, rating)
- Grid of kost cards
- Pagination

**Kost Card Component**:
```html
<div class="kost-card">
  <div class="kost-image">
    <img src="..." alt="Kost Name">
    <span class="kost-badge">Recommended</span>
  </div>
  <div class="kost-content">
    <h3 class="kost-name">Kost Papipul Pakuwon</h3>
    <p class="kost-price">Rp 2.500.000 <span>/bulan</span></p>
    <div class="kost-meta">
      <span><i class="icon-location"></i> 1.2 km dari kampus</span>
      <span><i class="icon-star"></i> 4.5</span>
    </div>
    <div class="kost-features">
      <span class="badge">WiFi</span>
      <span class="badge">AC</span>
      <span class="badge">Kamar Mandi Dalam</span>
    </div>
    <button class="btn btn-outline">Lihat Detail</button>
  </div>
</div>
```

---

### 3.3 Kost Detail Page (`kost-detail.html`)

**Sections**:
1. **Image Gallery** (carousel)
2. **Kost Information**
   - Nama, harga, alamat
   - Rating breakdown (kebersihan, keamanan, fasilitas)
3. **Criteria Details**
   - Table with all criteria values
4. **Description**
5. **Location Map** (optional: Google Maps embed)
6. **CTA Buttons**
   - "Hubungi Pemilik"
   - "Tambah ke Perbandingan"

---

### 3.4 Recommendations Page (`recommendations.html`)

**Features**:
- Optional preference form
- Loading animation during calculation
- Results table with ranking
- Score visualization (progress bar)
- "Lihat Detail Perhitungan" button

**Results Table**:
```html
<table class="recommendations-table">
  <thead>
    <tr>
      <th>Rank</th>
      <th>Nama Kost</th>
      <th>Score</th>
      <th>Harga</th>
      <th>Jarak Kampus</th>
      <th>Rating</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <tr class="rank-1">
      <td><span class="rank-badge gold">1</span></td>
      <td>Kost Eleora Cikunir Tipe A</td>
      <td>
        <div class="score-bar">
          <div class="score-fill" style="width: 76.6%"></div>
          <span class="score-value">0.766</span>
        </div>
      </td>
      <td>Rp 956.000</td>
      <td>2.5 km</td>
      <td>⭐ 4.3</td>
      <td>
        <button class="btn btn-sm">Detail</button>
      </td>
    </tr>
  </tbody>
</table>
```

---

### 3.5 Comparison Page (`comparison.html`)

**Features**:
- Select 2-4 kost to compare
- Side-by-side comparison table
- Highlight best value in each criteria
- Visual comparison (radar chart using Chart.js)

---

### 3.6 Admin Dashboard (`admin/dashboard.html`)

**Widgets**:
1. **Statistics Cards**
   - Total Kost
   - Total Users
   - Calculations Today
   - Average Score

2. **Charts**
   - Kost distribution by price range (bar chart)
   - Popular kost (pie chart)
   - Calculation history (line chart)

3. **Recent Activities**
   - Latest calculations
   - New kost added
   - User registrations

---

### 3.7 Manage Kost (`admin/manage-kost.html`)

**Features**:
- Data table with search, sort, filter
- CRUD operations (Create, Read, Update, Delete)
- Inline editing or modal form
- Image upload

**Form Fields**:
```html
<form id="kost-form">
  <input type="text" name="nama" placeholder="Nama Kost" required>
  <input type="number" name="jarak_kampus" placeholder="Jarak Kampus (km)" step="0.1" required>
  <input type="number" name="jarak_market" placeholder="Jarak Market (km)" step="0.1" required>
  <input type="number" name="harga" placeholder="Harga (Rp)" required>
  <select name="kebersihan" required>
    <option value="">Kebersihan</option>
    <option value="1">1 - Sangat Buruk</option>
    <option value="2">2 - Buruk</option>
    <option value="3">3 - Cukup</option>
    <option value="4">4 - Baik</option>
    <option value="5">5 - Sangat Baik</option>
  </select>
  <select name="keamanan" required><!-- Same options --></select>
  <select name="fasilitas" required><!-- Same options --></select>
  <textarea name="deskripsi" placeholder="Deskripsi"></textarea>
  <input type="text" name="alamat" placeholder="Alamat">
  <input type="file" name="foto" accept="image/*" multiple>
  <button type="submit" class="btn btn-primary">Simpan</button>
</form>
```

---

### 3.8 Configure AHP (`admin/configure-ahp.html`)

**Features**:
- 6x6 pairwise comparison matrix
- Dropdown for comparison values (1-9 scale)
- Auto-fill reciprocal values
- Real-time CR calculation
- Visual feedback for consistency

**Matrix UI**:
```html
<table class="ahp-matrix">
  <thead>
    <tr>
      <th></th>
      <th>Jarak Kampus</th>
      <th>Jarak Market</th>
      <th>Harga</th>
      <th>Kebersihan</th>
      <th>Keamanan</th>
      <th>Fasilitas</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th>Jarak Kampus</th>
      <td>1</td>
      <td>
        <select name="jk_jm" data-reciprocal="jm_jk">
          <option value="1">1 - Equal</option>
          <option value="2">2</option>
          <option value="3">3 - Moderate</option>
          <!-- ... up to 9 -->
        </select>
      </td>
      <!-- More cells -->
    </tr>
    <!-- More rows -->
  </tbody>
</table>

<div class="ahp-results">
  <h3>Hasil Perhitungan</h3>
  <p>Consistency Ratio: <span id="cr-value">0.00</span></p>
  <p class="cr-status success">✓ Matriks Konsisten</p>
  
  <h4>Bobot Kriteria:</h4>
  <ul class="weights-list">
    <li>Jarak Kampus: <strong>10%</strong></li>
    <li>Jarak Market: <strong>5%</strong></li>
    <li>Harga: <strong>40%</strong></li>
    <li>Kebersihan: <strong>10%</strong></li>
    <li>Keamanan: <strong>15%</strong></li>
    <li>Fasilitas: <strong>20%</strong></li>
  </ul>
</div>
```

---

## 4. JavaScript Modules

### 4.1 API Module (`api.js`)

```javascript
const API_BASE_URL = '/api';

class API {
  static async request(endpoint, options = {}) {
    const token = localStorage.getItem('token');
    const headers = {
      'Content-Type': 'application/json',
      ...(token && { 'Authorization': `Bearer ${token}` }),
      ...options.headers
    };

    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      ...options,
      headers
    });

    const data = await response.json();
    
    if (!data.success) {
      throw new Error(data.error.message);
    }
    
    return data.data;
  }

  static async get(endpoint) {
    return this.request(endpoint, { method: 'GET' });
  }

  static async post(endpoint, body) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(body)
    });
  }

  static async put(endpoint, body) {
    return this.request(endpoint, {
      method: 'PUT',
      body: JSON.stringify(body)
    });
  }

  static async delete(endpoint) {
    return this.request(endpoint, { method: 'DELETE' });
  }
}

// Kost API
const KostAPI = {
  getAll: (params) => API.get(`/kost?${new URLSearchParams(params)}`),
  getById: (id) => API.get(`/kost/${id}`),
  create: (data) => API.post('/kost', data),
  update: (id, data) => API.put(`/kost/${id}`, data),
  delete: (id) => API.delete(`/kost/${id}`)
};

// SPK API
const SPKAPI = {
  configureAHP: (matrix) => API.post('/spk/ahp/configure', { pairwise_matrix: matrix }),
  calculateTOPSIS: (filters) => API.post('/spk/topsis/calculate', { filters }),
  getDetails: (kostId) => API.get(`/spk/topsis/details/${kostId}`)
};
```

---

### 4.2 SPK Module (`spk.js`)

```javascript
class SPKCalculator {
  // AHP Scale reference
  static AHP_SCALE = {
    1: 'Equal importance',
    2: 'Weak or slight',
    3: 'Moderate importance',
    4: 'Moderate plus',
    5: 'Strong importance',
    6: 'Strong plus',
    7: 'Very strong',
    8: 'Very, very strong',
    9: 'Extreme importance'
  };

  // Visualize TOPSIS results
  static visualizeResults(results) {
    const ctx = document.getElementById('resultsChart').getContext('2d');
    
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: results.map(r => r.nama),
        datasets: [{
          label: 'Preference Value',
          data: results.map(r => r.score),
          backgroundColor: 'rgba(108, 99, 255, 0.6)',
          borderColor: 'rgba(108, 99, 255, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            max: 1
          }
        }
      }
    });
  }

  // Display calculation details
  static displayCalculationDetails(details) {
    const container = document.getElementById('calculation-details');
    
    container.innerHTML = `
      <h3>Detail Perhitungan TOPSIS</h3>
      
      <h4>1. Nilai Ternormalisasi</h4>
      <table class="detail-table">
        <tr>
          <th>Kriteria</th>
          <th>Nilai</th>
        </tr>
        ${Object.entries(details.normalized_values).map(([k, v]) => `
          <tr>
            <td>${k}</td>
            <td>${v.toFixed(6)}</td>
          </tr>
        `).join('')}
      </table>
      
      <h4>2. Nilai Terbobot</h4>
      <table class="detail-table">
        ${Object.entries(details.weighted_values).map(([k, v]) => `
          <tr>
            <td>${k}</td>
            <td>${v.toFixed(6)}</td>
          </tr>
        `).join('')}
      </table>
      
      <h4>3. Jarak ke Solusi Ideal</h4>
      <p>D+ (Jarak ke Ideal Positif): <strong>${details.d_positive.toFixed(6)}</strong></p>
      <p>D- (Jarak ke Ideal Negatif): <strong>${details.d_negative.toFixed(6)}</strong></p>
      
      <h4>4. Nilai Preferensi</h4>
      <p>V = D- / (D+ + D-) = <strong>${details.preference_value.toFixed(6)}</strong></p>
      
      <h4>5. Ranking</h4>
      <p class="rank-result">Peringkat: <span class="rank-badge">#${details.rank}</span></p>
    `;
  }
}
```

---

### 4.3 Utils Module (`utils.js`)

```javascript
// Format currency
function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount);
}

// Format distance
function formatDistance(km) {
  if (km < 1) {
    return `${(km * 1000).toFixed(0)} m`;
  }
  return `${km.toFixed(1)} km`;
}

// Show loading
function showLoading(message = 'Loading...') {
  const loader = document.createElement('div');
  loader.className = 'loader-overlay';
  loader.innerHTML = `
    <div class="loader">
      <div class="spinner"></div>
      <p>${message}</p>
    </div>
  `;
  document.body.appendChild(loader);
}

function hideLoading() {
  const loader = document.querySelector('.loader-overlay');
  if (loader) loader.remove();
}

// Show toast notification
function showToast(message, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.textContent = message;
  document.body.appendChild(toast);
  
  setTimeout(() => toast.classList.add('show'), 100);
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}
```

---

## 5. Responsive Design

### 5.1 Breakpoints

```css
/* Mobile First */
/* Small devices (phones, 320px and up) */
@media (min-width: 320px) { }

/* Medium devices (tablets, 768px and up) */
@media (min-width: 768px) { }

/* Large devices (desktops, 1024px and up) */
@media (min-width: 1024px) { }

/* Extra large devices (large desktops, 1280px and up) */
@media (min-width: 1280px) { }
```

### 5.2 Mobile Navigation

```css
.navbar {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
}

@media (max-width: 768px) {
  .nav-menu {
    position: fixed;
    left: -100%;
    top: 70px;
    flex-direction: column;
    background-color: var(--white);
    width: 100%;
    transition: 0.3s;
    box-shadow: var(--shadow-lg);
  }
  
  .nav-menu.active {
    left: 0;
  }
}
```

---

## 6. Animations & Interactions

### 6.1 Hover Effects

```css
.kost-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.kost-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
}

.btn {
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
  width: 300px;
  height: 300px;
}
```

### 6.2 Loading Animations

```css
@keyframes spin {
  to { transform: rotate(360deg); }
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--light-gray);
  border-top-color: var(--primary-purple);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
```

---

## 7. Accessibility

### 7.1 ARIA Labels

```html
<button aria-label="Close modal" class="close-btn">×</button>
<img src="..." alt="Kost Papipul exterior view">
<nav aria-label="Main navigation">...</nav>
```

### 7.2 Keyboard Navigation

```javascript
// Trap focus in modal
modal.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeModal();
  if (e.key === 'Tab') trapFocus(e);
});
```

---

**Document Version**: 1.0  
**Last Updated**: 2026-01-04  
**Author**: Frontend Team
