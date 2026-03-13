<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit Tracker API Playground</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .json-key { color: #0ea5e9; }
        .json-string { color: #10b981; }
        .json-number { color: #f59e0b; }
        .json-boolean { color: #8b5cf6; }
        .json-null { color: #ef4444; }
        pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 1.5rem;
            border-radius: 0.5rem;
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="gradient-bg text-white py-8 shadow-lg">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-2">🎯 Habit Tracker API</h1>
            <p class="text-gray-100">Interactive API Testing & Documentation</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel - Endpoints -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-2xl font-bold mb-4">📡 API Endpoints</h2>

                    <!-- Auth Section -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2 text-purple-600">🔐 Authentication</h3>
                        <div class="space-y-2">
                            <button onclick="testLogin()" class="w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded transition">
                                <span class="font-mono text-sm text-blue-600">POST</span> /auth/login
                            </button>
                            <button onclick="testMe()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded transition">
                                <span class="font-mono text-sm text-green-600">GET</span> /auth/me
                            </button>
                        </div>
                    </div>

                    <!-- Habits Section -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2 text-purple-600">✅ Habits</h3>
                        <div class="space-y-2">
                            <button onclick="getHabits()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded transition">
                                <span class="font-mono text-sm text-green-600">GET</span> /habits
                            </button>
                            <button onclick="createHabit()" class="w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded transition">
                                <span class="font-mono text-sm text-blue-600">POST</span> /habits
                            </button>
                            <button onclick="getHabitStats()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded transition">
                                <span class="font-mono text-sm text-green-600">GET</span> /habits/1/stats
                            </button>
                        </div>
                    </div>

                    <!-- Logs Section -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2 text-purple-600">📊 Habit Logs</h3>
                        <div class="space-y-2">
                            <button onclick="logHabit()" class="w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded transition">
                                <span class="font-mono text-sm text-blue-600">POST</span> /habits/1/log
                            </button>
                            <button onclick="getHabitLogs()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded transition">
                                <span class="font-mono text-sm text-green-600">GET</span> /habits/1/logs
                            </button>
                        </div>
                    </div>

                    <!-- Heroes Section -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2 text-purple-600">🦸 Heroes</h3>
                        <div class="space-y-2">
                            <button onclick="getHeroes()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded transition">
                                <span class="font-mono text-sm text-green-600">GET</span> /heroes
                            </button>
                            <button onclick="getUserHeroes()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded transition">
                                <span class="font-mono text-sm text-green-600">GET</span> /user/heroes
                            </button>
                        </div>
                    </div>

                    <!-- Health -->
                    <div>
                        <h3 class="font-semibold text-lg mb-2 text-purple-600">❤️ System</h3>
                        <button onclick="healthCheck()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded transition">
                            <span class="font-mono text-sm text-green-600">GET</span> /health
                        </button>
                    </div>

                    <!-- Token Input -->
                    <div class="mt-6 pt-6 border-t">
                        <label class="block text-sm font-medium mb-2">🔑 Bearer Token</label>
                        <input type="text" id="token" placeholder="Paste your token here"
                               class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-purple-500 text-sm font-mono">
                        <button onclick="quickLogin()" class="mt-2 w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">
                            Quick Login (test@example.com)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Response -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">📤 Response</h2>
                        <div id="status" class="text-sm"></div>
                    </div>

                    <div id="request-info" class="mb-4 p-4 bg-gray-50 rounded hidden">
                        <div class="text-sm space-y-1">
                            <p><strong>Method:</strong> <span id="req-method"></span></p>
                            <p><strong>URL:</strong> <span id="req-url" class="font-mono text-xs"></span></p>
                            <p><strong>Status:</strong> <span id="req-status"></span></p>
                        </div>
                    </div>

                    <div id="response" class="text-gray-500 text-center py-12">
                        👆 Choose endpoint in left for test
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = "{{ config('app.url') }}/api";
        let token = '';

        // Auto-load token from localStorage
        window.addEventListener('load', () => {
            const savedToken = localStorage.getItem('api_token');
            if (savedToken) {
                document.getElementById('token').value = savedToken;
                token = savedToken;
            }
        });

        // Update token when input changes
        document.getElementById('token').addEventListener('input', (e) => {
            token = e.target.value;
            localStorage.setItem('api_token', token);
        });

        function showResponse(method, url, status, data) {
            document.getElementById('request-info').classList.remove('hidden');
            document.getElementById('req-method').textContent = method;
            document.getElementById('req-method').className = method === 'GET' ? 'text-green-600 font-bold' : 'text-blue-600 font-bold';
            document.getElementById('req-url').textContent = url;
            document.getElementById('req-status').textContent = status;
            document.getElementById('req-status').className = status >= 200 && status < 300 ? 'text-green-600 font-bold' : 'text-red-600 font-bold';

            const formatted = syntaxHighlight(JSON.stringify(data, null, 2));
            document.getElementById('response').innerHTML = `<pre class="text-left">${formatted}</pre>`;
        }

        function syntaxHighlight(json) {
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                let cls = 'json-number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'json-key';
                    } else {
                        cls = 'json-string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'json-boolean';
                } else if (/null/.test(match)) {
                    cls = 'json-null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }

        async function quickLogin() {
            try {
                const response = await axios.post(`${API_URL}/auth/login`, {
                    email: 'test@example.com',
                    password: 'password'
                });

                token = response.data.token;
                document.getElementById('token').value = token;
                localStorage.setItem('api_token', token);

                showResponse('POST', '/auth/login', response.status, response.data);
                alert('✅ Logged in successfully! Token saved.');
            } catch (error) {
                showResponse('POST', '/auth/login', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function testLogin() {
            try {
                const response = await axios.post(`${API_URL}/auth/login`, {
                    email: 'test@example.com',
                    password: 'password'
                });
                showResponse('POST', '/auth/login', response.status, response.data);
            } catch (error) {
                showResponse('POST', '/auth/login', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function testMe() {
            try {
                const response = await axios.get(`${API_URL}/auth/me`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('GET', '/auth/me', response.status, response.data);
            } catch (error) {
                showResponse('GET', '/auth/me', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function getHabits() {
            try {
                const response = await axios.get(`${API_URL}/habits`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('GET', '/habits', response.status, response.data);
            } catch (error) {
                showResponse('GET', '/habits', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function createHabit() {
            try {
                const response = await axios.post(`${API_URL}/habits`, {
                    title: '🏃 Morning Run',
                    description: 'Run every morning for 30 minutes',
                    icon: '🏃',
                    color: '#10b981',
                    frequency: 'daily',
                    target_count: 1
                }, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('POST', '/habits', response.status, response.data);
            } catch (error) {
                showResponse('POST', '/habits', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function logHabit() {
            try {
                const response = await axios.post(`${API_URL}/habits/1/log`, {
                    count: 1,
                    note: 'Felt great! 💪'
                }, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('POST', '/habits/1/log', response.status, response.data);
            } catch (error) {
                showResponse('POST', '/habits/1/log', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function getHabitLogs() {
            try {
                const response = await axios.get(`${API_URL}/habits/1/logs`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('GET', '/habits/1/logs', response.status, response.data);
            } catch (error) {
                showResponse('GET', '/habits/1/logs', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function getHabitStats() {
            try {
                const response = await axios.get(`${API_URL}/habits/1/stats`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('GET', '/habits/1/stats', response.status, response.data);
            } catch (error) {
                showResponse('GET', '/habits/1/stats', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function getHeroes() {
            try {
                const response = await axios.get(`${API_URL}/heroes`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('GET', '/heroes', response.status, response.data);
            } catch (error) {
                showResponse('GET', '/heroes', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function getUserHeroes() {
            try {
                const response = await axios.get(`${API_URL}/user/heroes`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                showResponse('GET', '/user/heroes', response.status, response.data);
            } catch (error) {
                showResponse('GET', '/user/heroes', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }

        async function healthCheck() {
            try {
                const response = await axios.get(`${API_URL}/health`);
                showResponse('GET', '/health', response.status, response.data);
            } catch (error) {
                showResponse('GET', '/health', error.response?.status || 500, error.response?.data || {error: error.message});
            }
        }
    </script>
</body>
</html>


