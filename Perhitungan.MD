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

**Penjelasan:** Fungsi `calculateWeights` menghitung bobot prioritas kriteria menggunakan metode AHP. Proses dimulai dengan menjumlahkan setiap kolom matriks perbandingan berpasangan, kemudian melakukan normalisasi dengan membagi setiap elemen dengan jumlah kolomnya. Bobot diperoleh dari rata-rata setiap baris matriks ternormalisasi. Untuk memvalidasi konsistensi penilaian pengguna, dihitung Lambda Max, Consistency Index (CI), dan Consistency Ratio (CR). Jika CR kurang dari 0.1 (10%), maka penilaian dianggap konsisten dan bobot dapat digunakan untuk perhitungan TOPSIS.

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

**Penjelasan:** Fungsi `normalize` melakukan normalisasi vektor pada matriks keputusan. Setiap nilai kriteria dibagi dengan akar kuadrat dari jumlah kuadrat seluruh nilai pada kriteria tersebut. Normalisasi ini memastikan semua kriteria berada dalam skala yang sama (0-1) sehingga dapat dibandingkan secara adil, terlepas dari satuan aslinya (meter, rupiah, atau skala 1-5).

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

**Penjelasan:** Fungsi `applyWeights` mengalikan setiap nilai ternormalisasi dengan bobot kriteria dari AHP. Proses ini menghasilkan matriks normalisasi terbobot (Y) yang mencerminkan preferensi pengguna. Kriteria dengan bobot lebih tinggi akan memiliki pengaruh lebih besar dalam menentukan ranking akhir.

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

**Penjelasan:** Fungsi `getIdealSolutions` menentukan solusi ideal positif (kondisi terbaik) dan negatif (kondisi terburuk). Untuk kriteria cost seperti jarak dan harga, nilai terkecil adalah yang terbaik. Sebaliknya untuk kriteria benefit seperti kebersihan dan keamanan, nilai terbesar adalah yang terbaik. Solusi ideal ini menjadi acuan untuk mengukur seberapa baik setiap alternatif kost.

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

**Penjelasan:** Fungsi `calculateDistances` menghitung jarak Euclidean setiap alternatif ke solusi ideal positif (D⁺) dan negatif (D⁻). Jarak dihitung dengan menjumlahkan kuadrat selisih nilai terbobot dengan nilai ideal, kemudian diakar kuadratkan. Alternatif yang baik memiliki D⁺ kecil (dekat dengan kondisi terbaik) dan D⁻ besar (jauh dari kondisi terburuk).

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

**Penjelasan:** Fungsi `calculatePreferenceValues` menghitung nilai preferensi (V) dengan membagi D⁻ dengan total jarak (D⁺ + D⁻). Nilai V berkisar 0-1, dimana nilai mendekati 1 menunjukkan alternatif terbaik. Hasil kemudian diurutkan secara descending sehingga kost dengan nilai V tertinggi berada di ranking pertama sebagai rekomendasi utama.

**Rumus:** `Vᵢ = D⁻ᵢ / (D⁺ᵢ + D⁻ᵢ)`

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

**Penjelasan:** Elemen HTML ini membuat slider interaktif untuk setiap kriteria. Input type range dengan nilai 0-100 memungkinkan pengguna mengatur bobot kriteria secara visual. Setiap slider memiliki label nama kriteria dan span untuk menampilkan nilai persentase secara real-time.

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

**Penjelasan:** JavaScript ini mengimplementasikan fitur auto-balancing dimana ketika satu slider digeser, slider lainnya akan menyesuaikan secara proporsional agar total selalu 100%. Fungsi `balanceSliders` menghitung faktor pengali berdasarkan perubahan nilai dan mendistribusikannya ke slider lain dengan tetap menjaga proporsi relatif. Fungsi `updateValueLabels` memperbarui tampilan persentase di UI secara real-time.

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

**Penjelasan:** Fungsi `findRecommendations` mengumpulkan nilai bobot dari semua slider dan mengirimkannya ke backend melalui API POST. Bobot yang dikirim dalam format persentase (0-100) akan dinormalisasi oleh server menjadi desimal (total = 1) sebelum digunakan dalam perhitungan TOPSIS. Hasil rekomendasi yang diterima kemudian ditampilkan dalam bentuk daftar ranking.

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
| **AHP** | Perhitungan bobot kriteria dengan validasi konsistensi (CR < 0.1) |
| **TOPSIS** | Perankingan alternatif berdasarkan jarak ke solusi ideal positif dan negatif |
| **Slider Bobot** | Pengaturan prioritas kriteria secara interaktif dengan auto-balancing 100% |
