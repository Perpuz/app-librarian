const API_BASE_URL = 'http://localhost:8081/api';

const api = {
    async request(endpoint, method = 'GET', data = null) {
        const token = localStorage.getItem('perpuz_token');
        const headers = {
            'Accept': 'application/json'
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            method,
            headers
        };

        if (data) {
            if (data instanceof FormData) {
                config.body = data;
                // Important: Do NOT set Content-Type for FormData
            } else {
                headers['Content-Type'] = 'application/json';
                config.body = JSON.stringify(data);
            }
        }

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
            const result = await response.json();

            if (!response.ok) {
                if (response.status === 401 && !window.location.pathname.includes('index.html')) {
                    localStorage.removeItem('perpuz_token');
                    window.location.href = 'index.html';
                }
                throw new Error(result.messages?.error || result.message || 'Something went wrong');
            }

            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    get(endpoint) {
        return this.request(endpoint, 'GET');
    },

    post(endpoint, data) {
        return this.request(endpoint, 'POST', data);
    },

    put(endpoint, data) {
        return this.request(endpoint, 'PUT', data);
    },

    delete(endpoint) {
        return this.request(endpoint, 'DELETE');
    }
};
