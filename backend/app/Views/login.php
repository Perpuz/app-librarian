<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpuz</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <div class="login-container">
        <div class="card login-card shadow-lg border-0">
            <div class="text-center mb-4">
                <div class="bg-primary-brand rounded-circle d-inline-flex p-3 mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                     <i class="fas fa-book-reader text-white fs-2"></i>
                </div>
                <h3 class="fw-bold text-dark" style="font-family: 'Instrument Sans'; letter-spacing: -0.5px;">Perpuz</h3>
                <p class="text-secondary small">Sign in to manage library</p>
            </div>
            
            <form id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label text-secondary fw-semibold">Username</label>
                    <input type="text" class="form-control py-2" id="username" required placeholder="Enter username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-secondary fw-semibold">Password</label>
                    <input type="password" class="form-control py-2" id="password" required placeholder="Enter password">
                </div>
                
                <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold bg-primary-brand border-0">Sign In</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/api.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMsg = document.getElementById('errorMessage');

            // Reset error
            errorMsg.classList.add('d-none');
            errorMsg.innerText = '';

            try {
                const result = await api.post('/auth/login', { username, password });
                
                if (result.status === 200) {
                    localStorage.setItem('token', result.token);
                    localStorage.setItem('user', JSON.stringify(result.user));
                    window.location.href = '/dashboard'; // Updated to routed URL
                } else {
                     throw new Error(result.message || 'Login failed');
                }
            } catch (error) {
                errorMsg.classList.remove('d-none');
                errorMsg.innerText = error.message;
            }
        });
    </script>
</body>
</html>
