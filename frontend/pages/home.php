<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>MMS - Home</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
</head>
<body>
    <nav class=\"navbar navbar-expand-lg navbar-dark bg-dark\">
        <div class=\"container\">
            <a class=\"navbar-brand\" href=\"/\">🏍️ MMS</a>
            <div class=\"navbar-nav ms-auto\">
                <a class=\"nav-link\" href=\"/\">Home</a>
                <a class=\"nav-link\" href=\"/login\">Login</a>
                <a class=\"nav-link\" id=\"dashboardLink\" href=\"/dashboard\" style=\"display:none;\">Dashboard</a>
                <a class=\"nav-link\" id=\"logoutLink\" href=\"#\" style=\"display:none;\">Logout</a>
            </div>
        </div>
    </nav>

    <div class=\"container mt-5\">
        <h1>Available Motorcycles</h1>
        <div class=\"row\" id=\"motorcyclesList\">
            <div class=\"col-12\"><div class=\"spinner-border\" role=\"status\"></div> Loading...</div>
        </div>
    </div>

    <script>
        const API_BASE = '<?php echo API_BASE; ?>';
        let token = localStorage.getItem('token');

        if (token) {
            document.getElementById('dashboardLink').style.display = 'block';
            document.getElementById('logoutLink').style.display = 'block';
            document.getElementById('loginLink').style.display = 'none';
        }

        async function loadMotorcycles() {
            try {
                const res = await fetch(`${API_BASE}/motorcycles`);
                const data = await res.json();
                const list = document.getElementById('motorcyclesList');
                list.innerHTML = '';
                data.data.forEach(m => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    col.innerHTML = `
                        <div class=\"card\">
                            <img src=\"${m.image_url || 'https://via.placeholder.com/300x200?text=Motorcycle'}\" class=\"card-img-top\" alt=\"${m.make} ${m.model}\">
                            <div class=\"card-body\">
                                <h5 class=\"card-title\">${m.make} ${m.model} (${m.year})</h5>
                                <p class=\"card-text\">$${m.price_per_day}/day</p>
                                <p class=\"badge bg-${m.status === 'available' ? 'success' : 'warning'} \">${m.status}</p>
                                <button class=\"btn btn-primary\" onclick=\"viewMotorcycle(${m.id})\">View</button>
                            </div>
                        </div>
                    `;
                    list.appendChild(col);
                });
            } catch (e) {
                console.error(e);
            }
        }

        function viewMotorcycle(id) {
            window.location.href = `/rent?id=${id}`;
        }

        loadMotorcycles();
    </script>
</body>
</html>
