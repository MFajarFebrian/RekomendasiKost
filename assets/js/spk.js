/**
 * SPK Visualization - Sistem Rekomendasi Kost
 */

const SPKCalculator = {
    AHP_SCALE: {
        1: 'Equal', 2: 'Weak', 3: 'Moderate', 4: 'Moderate+',
        5: 'Strong', 6: 'Strong+', 7: 'Very Strong', 8: 'Very Strong+', 9: 'Extreme'
    },

    CRITERIA: ['Jarak Kampus', 'Jarak Market', 'Harga', 'Kebersihan', 'Keamanan', 'Fasilitas'],

    renderResultsTable(results, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        let html = `
        <table class="table">
            <thead>
                <tr>
                    <th>Rank</th><th>Nama Kost</th><th>Score</th>
                    <th>Harga</th><th>Jarak</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
        `;

        results.forEach(r => {
            const badgeClass = getRankBadgeClass(r.rank);
            html += `
            <tr>
                <td><span class="rank-badge ${badgeClass}">${r.rank}</span></td>
                <td>${escapeHtml(r.nama)}</td>
                <td>
                    <div class="score-bar">
                        <div class="score-fill" style="width: ${r.score * 100}%"></div>
                        <span class="score-value">${formatNumber(r.score, 3)}</span>
                    </div>
                </td>
                <td>${formatCurrency(r.details.harga)}</td>
                <td>${formatDistance(r.details.jarak_kampus)}</td>
                <td>
                    <button class="btn btn-sm btn-outline" onclick="showDetails(${r.kost_id})">Detail</button>
                </td>
            </tr>`;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    },

    renderWeights(weights, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        let html = '<ul class="weights-list">';
        Object.entries(weights).forEach(([key, val]) => {
            const name = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            html += `<li><span>${name}</span><strong>${formatPercent(val)}</strong></li>`;
        });
        html += '</ul>';
        container.innerHTML = html;
    },

    renderDetails(details, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = `
        <h4>Nilai Ternormalisasi</h4>
        <table class="table">${this.renderValuesTable(details.normalized_values)}</table>
        
        <h4 class="mt-4">Nilai Terbobot</h4>
        <table class="table">${this.renderValuesTable(details.weighted_normalized_values)}</table>
        
        <h4 class="mt-4">Jarak Solusi Ideal</h4>
        <p>D+ (Positif): <strong>${formatNumber(details.d_positive, 6)}</strong></p>
        <p>D- (Negatif): <strong>${formatNumber(details.d_negative, 6)}</strong></p>
        
        <h4 class="mt-4">Nilai Preferensi</h4>
        <p class="text-xl">V = D- / (D+ + D-) = <strong class="text-primary">${formatNumber(details.preference_value, 6)}</strong></p>
        <p>Ranking: <span class="rank-badge ${getRankBadgeClass(details.rank)}">#${details.rank}</span></p>
        `;
    },

    renderValuesTable(values) {
        let html = '<tr>';
        Object.entries(values).forEach(([key, val]) => {
            html += `<th>${key.replace(/_/g, ' ')}</th>`;
        });
        html += '</tr><tr>';
        Object.values(values).forEach(val => {
            html += `<td>${formatNumber(val, 6)}</td>`;
        });
        html += '</tr>';
        return html;
    }
};

window.SPKCalculator = SPKCalculator;
