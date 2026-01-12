/**
 * API Module - Sistem Rekomendasi Kost
 * Fetch wrapper and API endpoints
 */

// Determine API base URL based on environment
const API_BASE_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? (window.location.pathname.includes('/RekomendasiKost_2/') ? '/RekomendasiKost_2/api' : '/RekomendasiKost/api')
    : '/api';

/**
 * Base API class for making HTTP requests
 */
class API {
    /**
     * Make an HTTP request
     */
    static async request(endpoint, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                ...options,
                headers,
                credentials: 'include' // Include cookies for session
            });

            const data = await response.json();

            if (!data.success) {
                throw new APIError(data.error.message, data.error.code, response.status);
            }

            return data.data;
        } catch (error) {
            if (error instanceof APIError) {
                throw error;
            }
            throw new APIError('Network error. Please try again.', 'NETWORK_ERROR', 0);
        }
    }

    static get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    }

    static post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(body)
        });
    }

    static put(endpoint, body) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body)
        });
    }

    static delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
}

/**
 * Custom API Error
 */
class APIError extends Error {
    constructor(message, code, status) {
        super(message);
        this.code = code;
        this.status = status;
    }
}

/**
 * Kost API
 */
const KostAPI = {
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return API.get(`/kost${queryString ? '?' + queryString : ''}`);
    },
    getById: (id) => API.get(`/kost/${id}`),
    create: (data) => API.post('/kost', data),
    update: (id, data) => API.put(`/kost/${id}`, data),
    delete: (id) => API.delete(`/kost/${id}`),
    getStats: () => API.get('/kost/stats')
};

/**
 * SPK API (AHP & TOPSIS)
 */
const SPKAPI = {
    // AHP
    getWeights: () => API.get('/spk/ahp/weights'),
    configureAHP: (pairwiseMatrix) => API.post('/spk/ahp/configure', { pairwise_matrix: pairwiseMatrix }),
    getAHPDetails: () => API.get('/spk/ahp/details'),

    // TOPSIS
    calculate: (filters = {}, limit = 10) => API.post('/spk/topsis/calculate', { filters, limit }),
    getResults: () => API.get('/spk/topsis/results'),
    getDetails: (kostId) => API.get(`/spk/topsis/details/${kostId}`)
};

// Export for use in other scripts
window.API = API;
window.KostAPI = KostAPI;
window.SPKAPI = SPKAPI;
window.APIError = APIError;

