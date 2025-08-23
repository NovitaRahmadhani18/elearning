import axios from 'axios';

const api = axios.create({
    // The baseURL can be the root, as Inertia requests are on the same domain.
    baseURL: '/api',
    // This is crucial for Sanctum to identify the session.
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
});

export default api;
