# Dokumentasi Perhitungan AHP-TOPSIS
## Sistem Rekomendasi Kost

---

## Struktur File Utama

| No | File | Fungsi |
|----|------|--------|
| 1 | `services/AHPService.php` | Perhitungan bobot kriteria dengan AHP |
| 2 | `services/TOPSISService.php` | Perhitungan perankingan dengan TOPSIS |
| 3 | `models/SPK.php` | Model database untuk menyimpan hasil perhitungan |
| 4 | `config/constants.php` | Konstanta (kriteria, skala Saaty, Random Index) |
| 5 | `pages/recommendations.html` | Halaman rekomendasi dengan slider bobot |

---

## BAGIAN 1: FITUR AHP (Analytical Hierarchy Process)

**File:** `services/AHPService.php`

```php
public function calculateWeights($pairwiseMatrix) {
    $n = $this->n;
    
    // Tahap 1: Hitung jumlah kolom
    $columnSums = array_fill(0, $n, 0);
    for ($j = 0; $j < $n; $j++) {
        for ($i = 0; $i < $n; $i++) {
            $columnSums[$j] += $pairwiseMatrix[$i][$j];
        }
    }
    
    // Tahap 2: Normalisasi matriks
    $normalized = [];
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $normalized[$i][$j] = $pairwiseMatrix[$i][$j] / $columnSums[$j];
        }
    }
    
    // Tahap 3: Hitung bobot (rata-rata baris)
    $weights = [];
    for ($i = 0; $i < $n; $i++) {
        $weights[$this->criteria[$i]] = array_sum($normalized[$i]) / $n;
    }
    
    // Tahap 4: Hitung Lambda Max
    $matrixAW = [];
    for ($i = 0; $i < $n; $i++) {
        $aw = 0;
        for ($j = 0; $j < $n; $j++) {
            $aw += $pairwiseMatrix[$i][$j] * $weights[$this->criteria[$j]];
        }
        $matrixAW[$i] = $aw;
    }
    $lambdaMax = 0;
    for ($i = 0; $i < $n; $i++) {
        $lambdaMax += $matrixAW[$i] / $weights[$this->criteria[$i]];
    }
    $lambdaMax /= $n;
    
    // Tahap 5: Consistency Index (CI)
    $CI = ($lambdaMax - $n) / ($n - 1);
    
    // Tahap 6: Consistency Ratio (CR)
    $RI = RANDOM_INDEX[$n];
    $CR = $CI / $RI;
    
    return [
        'weights' => $weights,
        'consistency_ratio' => $CR,
        'is_consistent' => $CR < 0.1
    ];
}
```

**Rumus:**
- Normalisasi: `nᵢⱼ = aᵢⱼ / Σaᵢⱼ`
- Bobot: `Wᵢ = Σnᵢⱼ / n`
- Lambda Max: `λmax = (1/n) × Σ[(A × W)ᵢ / Wᵢ]`
- CI: `CI = (λmax - n) / (n - 1)`
- CR: `CR = CI / RI` (konsisten jika CR < 0.1)

---

## BAGIAN 2: FITUR TOPSIS

**File:** `services/TOPSISService.php`

### Tahap 1: Normalisasi Matriks Keputusan

```php
private function normalize($alternatives) {
    $sqrtSums = [];
    
    foreach ($this->criteria as $criterion) {
        $sumSquares = 0;
        foreach ($alternatives as $alt) {
            $sumSquares += pow($alt[$criterion], 2);
        }
        $sqrtSums[$criterion] = sqrt($sumSquares);
    }
    
    $normalized = [];
    foreach ($alternatives as $alt) {
        $normAlt = ['nama' => $alt['nama']];
        foreach ($this->criteria as $criterion) {
            $normAlt[$criterion] = $alt[$criterion] / $sqrtSums[$criterion];
        }
        $normalized[] = $normAlt;
    }
    
    return $normalized;
}
```

**Rumus:** `rᵢⱼ = xᵢⱼ / √(Σxᵢⱼ²)`

---

### Tahap 2: Matriks Normalisasi Terbobot

```php
private function applyWeights($normalized, $weights) {
    $weighted = [];
    
    foreach ($normalized as $alt) {
        $weightedAlt = ['nama' => $alt['nama']];
        foreach ($this->criteria as $criterion) {
            $weightedAlt[$criterion] = $alt[$criterion] * $weights[$criterion];
        }
        $weighted[] = $weightedAlt;
    }
    
    return $weighted;
}
```

**Rumus:** `yᵢⱼ = rᵢⱼ × wⱼ`

---

### Tahap 3: Solusi Ideal Positif (A⁺) dan Negatif (A⁻)

```php
private function getIdealSolutions($weighted) {
    $idealPositive = [];
    $idealNegative = [];
    
    foreach ($this->criteria as $criterion) {
        $values = array_column($weighted, $criterion);
        
        if (in_array($criterion, $this->costCriteria)) {
            // Cost: semakin kecil semakin baik
            $idealPositive[$criterion] = min($values);
            $idealNegative[$criterion] = max($values);
        } else {
            // Benefit: semakin besar semakin baik
            $idealPositive[$criterion] = max($values);
            $idealNegative[$criterion] = min($values);
        }
    }
    
    return ['positive' => $idealPositive, 'negative' => $idealNegative];
}
```

**Rumus:**
- Kriteria Benefit: A⁺ = MAX(yᵢⱼ), A⁻ = MIN(yᵢⱼ)
- Kriteria Cost: A⁺ = MIN(yᵢⱼ), A⁻ = MAX(yᵢⱼ)

---

### Tahap 4: Jarak ke Solusi Ideal

```php
private function calculateDistances($weighted, $idealSolutions) {
    $dPositive = [];
    $dNegative = [];
    
    foreach ($weighted as $alt) {
        $dPlus = 0;
        $dMinus = 0;
        
        foreach ($this->criteria as $criterion) {
            $dPlus += pow($alt[$criterion] - $idealSolutions['positive'][$criterion], 2);
            $dMinus += pow($alt[$criterion] - $idealSolutions['negative'][$criterion], 2);
        }
        
        $dPositive[] = ['nama' => $alt['nama'], 'dPositif' => sqrt($dPlus)];
        $dNegative[] = ['nama' => $alt['nama'], 'dNegatif' => sqrt($dMinus)];
    }
    
    return ['positive' => $dPositive, 'negative' => $dNegative];
}
```

**Rumus:**
- `D⁺ᵢ = √[Σ(yᵢⱼ - A⁺ⱼ)²]`
- `D⁻ᵢ = √[Σ(yᵢⱼ - A⁻ⱼ)²]`

---

### Tahap 5: Nilai Preferensi (V)

```php
private function calculatePreferenceValues($distances) {
    $preferenceValues = [];
    
    for ($i = 0; $i < count($distances['positive']); $i++) {
        $dPlus = $distances['positive'][$i]['dPositif'];
        $dMinus = $distances['negative'][$i]['dNegatif'];
        
        $preferenceValues[] = [
            'nama' => $distances['positive'][$i]['nama'],
            'val' => $dMinus / ($dPlus + $dMinus)
        ];
    }
    
    usort($preferenceValues, function($a, $b) {
        return $b['val'] <=> $a['val'];
    });
    
    return $preferenceValues;
}
```

**Rumus:** `Vᵢ = D⁻ᵢ / (D⁺ᵢ + D⁻ᵢ)`

> Nilai V mendekati 1 = Alternatif terbaik

---

## BAGIAN 3: FITUR PENGATURAN AHP DENGAN SLIDER

**File:** `pages/recommendations.html`

### HTML Slider

```html
<div class="priority-slider">
    <label>📍 Jarak ke Kampus</label>
    <input type="range" class="weight-slider" id="w-jarak" 
           data-id="jarak" min="0" max="100" value="16">
    <span class="priority-value" id="v-jarak">16%</span>
</div>

<div class="priority-slider">
    <label>💰 Harga Terjangkau</label>
    <input type="range" class="weight-slider" id="w-harga" 
           data-id="harga" min="0" max="100" value="17">
    <span class="priority-value" id="v-harga">17%</span>
</div>
<!-- ... slider lainnya untuk: jarak_market, kebersihan, keamanan, fasilitas -->
```

---

### JavaScript: Balancing Slider (Total = 100%)

```javascript
const sliders = Array.from(document.querySelectorAll('.weight-slider'));

sliders.forEach(slider => {
    slider.addEventListener('input', (e) => {
        balanceSliders(e.target);
        updateValueLabels();
    });
});

function balanceSliders(changedSlider) {
    const changedId = changedSlider.dataset.id;
    const newValue = parseInt(changedSlider.value);
    const otherSliders = sliders.filter(s => s.dataset.id !== changedId);

    const totalOther = otherSliders.reduce((sum, s) => sum + parseInt(s.value), 0);
    const targetTotalOther = 100 - newValue;

    if (totalOther === 0) {
        // Distribusi sama rata jika semua 0
        const share = Math.floor(targetTotalOther / otherSliders.length);
        let currentAdded = 0;
        otherSliders.forEach((s, index) => {
            if (index === otherSliders.length - 1) {
                s.value = targetTotalOther - currentAdded;
            } else {
                s.value = share;
                currentAdded += share;
            }
        });
    } else {
        // Sesuaikan slider lain secara proporsional
        const factor = targetTotalOther / totalOther;
        let currentSum = newValue;

        otherSliders.forEach((s, index) => {
            let sValue = Math.round(parseInt(s.value) * factor);
            if (index === otherSliders.length - 1) {
                sValue = 100 - currentSum;
            }
            s.value = Math.max(0, sValue);
            currentSum += parseInt(s.value);
        });
    }
}

function updateValueLabels() {
    let total = 0;
    sliders.forEach(s => {
        const val = parseInt(s.value);
        document.getElementById(`v-${s.dataset.id}`).textContent = val + '%';
        total += val;
    });
    document.getElementById('total-weight').textContent = total;
}
```

---

### JavaScript: Kirim Bobot ke API TOPSIS

```javascript
async function findRecommendations() {
    const weights = {
        jarak_kampus: parseInt(document.getElementById('w-jarak').value),
        jarak_market: parseInt(document.getElementById('w-market').value),
        harga: parseInt(document.getElementById('w-harga').value),
        kebersihan: parseInt(document.getElementById('w-kebersihan').value),
        keamanan: parseInt(document.getElementById('w-keamanan').value),
        fasilitas: parseInt(document.getElementById('w-fasilitas').value)
    };

    const kampusId = document.getElementById('kampus-select').value;

    const data = await API.post('/spk/topsis/calculate', {
        weights: weights,
        kampus_id: kampusId || null,
        limit: 10
    });

    renderResults(data.recommendations);
}
```

---

## Tabel Database

| No | Tabel | Fungsi |
|----|-------|--------|
| 1 | `temp_bobot` | Matriks perbandingan berpasangan AHP |
| 2 | `temp_normalisasi_kriteria` | Hasil normalisasi dan bobot kriteria |
| 3 | `temp_normalisasi` | Matriks normalisasi TOPSIS |
| 4 | `temp_d_pos` | Jarak ke solusi ideal positif (D⁺) |
| 5 | `temp_d_neg` | Jarak ke solusi ideal negatif (D⁻) |
| 6 | `temp_nilai_pref` | Nilai preferensi dan ranking |
| 7 | `kost` | Data kost |

---

## Fitur Utama Sistem

| Fitur | Deskripsi |
|-------|-----------|
| **AHP** | Perhitungan bobot kriteria dengan validasi konsistensi |
| **TOPSIS** | Perankingan alternatif berdasarkan jarak ke solusi ideal |
| **Slider Bobot** | Pengaturan prioritas kriteria secara interaktif dengan auto-balancing 100% |
