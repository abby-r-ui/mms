<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>MMS - Dashboard</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
</head>
<body>
    <nav class=\"navbar navbar-expand-lg navbar-dark bg-dark\">
        <div class=\"container\">
            <a class=\"navbar-brand\" href=\"/\">🏍️ MMS</a>
            <div class=\"navbar-nav\">
                <a class=\"nav-link\" href=\"/\">Home</a>
                <a class=\"nav-link\" href=\"/dashboard\">Dashboard</a>
                <button class=\"btn btn-outline-light ms-2\" onclick=\"logout()\">Logout</button>
            </div>
        </div>
    </nav>

    <div class=\"container mt-5\">
        <div id=\"dashboardContent\">
            <div class=\"spinner-border\" role=\"status\"></div> Loading...
        </div>
    </div>

    <script>
        const API_BASE = '<?php echo API_BASE; ?>';
        const token = localStorage.getItem('token');

        if (!token) {
            window.location.href = '/login';
        }

        async function loadDashboard() {
            try {
                const meRes = await fetch(`${API_BASE}/me`, {
                    headers: {'Authorization': `Bearer ${token}`}
                });
                const meData = await meRes.json();

                let content = `<h1>Welcome, ${meData.user.name} (${meData.user.role})</h1>`;

                if (meData.user.role === 'admin') {
                    const rentalsRes = await fetch(`${API_BASE}/rentals`, {
                        headers: {'Authorization': `Bearer ${token}`}
                    });
                    const rentalsData = await rentalsRes.json();
                    content += '<h2>All Rentals</h2><div class=\"row\">';
                    rentalsData.data.forEach(r => {
                        content += `
                            <div class=\"col-md-6 mb-3\">
                                <div class=\"card\">
                                    <div class=\"card-body\">
                                        <h6>${r.motorcycle.make} ${r.motorcycle.model}</h6>
                                        <p>${r.user.name} | $${r.total_price} | ${r.status}</p>
                                        <button class=\"btn btn-sm btn-secondary\" onclick=\"updateRental(${r.id}, 'confirmed')\">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    content += '</div>';
                } else {
                    const rentalsRes = await fetch(`${API_BASE}/rentals`, {
                        headers: {'Authorization': `Bearer ${token}`}
                    });
                    const rentalsData = await rentalsRes.json();
                    content += '<h2>My Rentals</h2>';
                    rentalsData.data.forEach(r => {
                        content += `<p>${r.motorcycle.model} | ${r.start_date} to ${r.end_date} | Status: ${r.status}</p>`;
                    });
                }

                document.getElementById('dashboardContent').innerHTML = content;
            } catch (e) {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        }

        async function updateRental(id, status) {
            const res = await fetch(`${API_BASE}/rentals/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({status})
            });
            if (res.ok) location.reload();
        }

        function logout() {
            fetch(`${API_BASE}/logout`, {
                method: 'POST',
                headers: {'Authorization': `Bearer ${token}`}
            }).then(() => {
                localStorage.removeItem('token');
                window.location.href = '/';
            });
        }

        loadDashboard();
    </script>
</body>
</html>
