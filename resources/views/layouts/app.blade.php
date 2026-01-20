<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Facebook Chat Manager1211')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            @include('layouts.topbar')

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const API_BASE = "{{ url('/api') }}";

        // Wait for axios to be available (loaded by Vite)
        function waitForAxios() {
            return new Promise((resolve) => {
                if (window.axios) {
                    resolve();
                } else {
                    const checkInterval = setInterval(() => {
                        if (window.axios) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 50);
                }
            });
        }

        // Initialize axios configuration once it's loaded
        waitForAxios().then(() => {
            console.log('Axios loaded successfully!');

            // Configure axios defaults
            axios.defaults.baseURL = "{{ url('/') }}";
            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['Content-Type'] = 'application/json';

        // Add request interceptor to ensure token is always included
        axios.interceptors.request.use(
            config => {
                const token = localStorage.getItem('token');
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
                console.log('Request interceptor - Token:', token ? 'exists' : 'missing');
                console.log('Request interceptor - Auth header:', config.headers.Authorization);
                return config;
            },
            error => {
                return Promise.reject(error);
            }
        );

        // Auto-login for development
        async function ensureAuthenticated() {
            let token = localStorage.getItem('token');
            console.log('Current token in localStorage:', token ? 'exists' : 'not found');

            // If token exists, validate it first
            if (token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                console.log('Testing existing token...');
                console.log('Header set for validation:', axios.defaults.headers.common['Authorization']);

                try {
                    // Test token validity
                    const meResponse = await axios.get('/api/me');
                    console.log('Existing token is valid!', meResponse.data);
                    console.log('Final header check:', axios.defaults.headers.common['Authorization']);
                    return true;
                } catch (error) {
                    console.log('Existing token is invalid, clearing it...', error.response?.status);
                    localStorage.removeItem('token');
                    delete axios.defaults.headers.common['Authorization'];
                    token = null;
                }
            }

            // No valid token, need to login/register
            if (!token) {
                console.log('No valid token found, attempting auto-login...');
                try {
                    // Try to auto-login with default credentials
                    console.log('Trying to login...');
                    const response = await axios.post('/api/login', {
                        email: 'admin@example.com',
                        password: 'password'
                    });

                    token = response.data.token;
                    localStorage.setItem('token', token);
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    console.log('Login successful! Token saved and set.');
                    return true;
                } catch (error) {
                    // If login fails, try to register
                    console.log('Login failed, trying to register...', error.response?.data);
                    try {
                        const response = await axios.post('/api/register', {
                            name: 'Admin',
                            email: 'admin@example.com',
                            password: 'password',
                            password_confirmation: 'password'
                        });

                        token = response.data.token;
                        localStorage.setItem('token', token);
                        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                        console.log('Registration successful! Token saved and set.');
                        return true;
                    } catch (regError) {
                        console.error('Auto-authentication failed:', regError.response?.data || regError);
                        alert('Authentication failed. Please contact admin.');
                        return false;
                    }
                }
            }

            return false;
        }

            // Initialize auth on page load
            let authReady = false;
            ensureAuthenticated().then(success => {
                authReady = success !== false;
                console.log('Authentication ready:', authReady);
                console.log('API Base URL:', API_BASE);
                console.log('Auth header after init:', axios.defaults.headers.common['Authorization']);
            });

            // Helper function to ensure API calls wait for auth
            window.apiCall = async function(fn) {
                if (!authReady) {
                    await ensureAuthenticated();
                }
                return fn();
            };

            // Make ensureAuthenticated globally available
            window.ensureAuthenticated = ensureAuthenticated;
        });
    </script>
    @yield('scripts')
</body>
</html>