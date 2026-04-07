<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>MMS - Login</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
</head>
<body>
    <div class=\"container mt-5\">
        <div class=\"row justify-content-center\">
            <div class=\"col-md-6\">
                <div class=\"card\">
                    <div class=\"card-header\">
                        <h3>Login</h3>
                    </div>
                    <div class=\"card-body\">
                        <form id=\"loginForm\">
                            <div class=\"mb-3\">
                                <label class=\"form-label\">Email</label>
                                <input type=\"email\" class=\"form-control\" id=\"email\" required>
                            </form>
                            <div class=\"mb-3\">
                                <label class=\"form-label\">Password</label>
                                <input type=\"password\" class=\"form-control\" id=\"password\" required>
                            </div>
                            <button type=\"submit\" class=\"btn btn-primary w-100\">Login</button>
                            <div id=\"message\" class=\"mt-3\"></div>
                        </form>
                    </div>
                </div>
                <div class=\"text-center mt-3\">
                    <a href=\"/\">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '<?php echo API_BASE; ?>';

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = document.getElementById('message');
            message.innerHTML = '<div class=\"spinner-border spinner-border-sm\"></div> Logging in...';

            try {
                const res = await fetch(`${API_BASE}/login`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    localStorage.setItem('token', data.token);
                    window.location.href = '/dashboard';
                } else {
                    message.innerHTML = '<div class=\"alert alert-danger\">Login failed</div>';
                }
            } catch (e) {
                message.innerHTML = '<div class=\"alert alert-danger\">Error: ' + e.message + '</div>';
            }
        });
    </script>
</body>
</html>
