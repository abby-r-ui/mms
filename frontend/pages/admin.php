<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>MMS - Admin</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
</head>
<body>
    <nav class=\"navbar navbar-expand-lg navbar-dark bg-dark\">
        <div class=\"container\">
            <a class=\"navbar-brand\" href=\"/\">🏍️ MMS Admin</a>
            <div class=\"navbar-nav\">
                <a class=\"nav-link\" href=\"/dashboard\">Dashboard</a>
                <button class=\"btn btn-outline-light ms-2\" onclick=\"logout()\">Logout</button>
            </div>
        </div>
    </nav>

    <div class=\"container mt-5\">
        <h1>Admin Panel</h1>
        <button class=\"btn btn-primary mb-3\" data-bs-toggle=\"modal\" data-bs-target=\"#addMotorcycleModal\">+ Add Motorcycle</button>
        <div id=\"adminContent\">
            <div class=\"spinner-border\" role=\"status\"></div> Loading...
        </div>
    </div>

    <!-- Add Motorcycle Modal -->
    <div class=\"modal fade\" id=\"addMotorcycleModal\">
        <div class=\"modal-dialog\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Add Motorcycle</h5>
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
                </div>
                <form id=\"addMotorcycleForm\">
                    <div class=\"modal-body\">
                        <div class=\"mb-3\">
                            <label class=\"form-label\">Make</label>
                            <input type=\"text\" class=\"form-control\" id=\"make\" required>
                        </div>
                        <div class=\"mb-3\">
                            <label class=\"form-label\">Model</label>
                            <input type=\"text\" class=\"form-control\" id=\"model\" required>
                        </div>
                        <div class=\"mb-3\">
                            <label class=\"form-label\">Year</label>
                            <input type=\"number\" class=\"form-control\" id=\"year\" required>
                        </div>
                        <div class=\"mb-3\">
                            <label class=\"form-label\">Price per day</label>
                            <input type=\"number\" step=\"0.01\" class=\"form-control\" id=\"price_per_day\" required>
                        </div>
                        <div class=\"mb-3\">
                            <label class=\"form-label\">Status</label>
                            <select class=\"form-control\" id=\"status\">
                                <option value=\"available\">Available</option>
                                <option value=\"maintenance\">Maintenance</option>
                            </select>
                        </div>
                        <div class=\"mb-3\">
                            <label class=\"form-label\">Image URL</label>
                            <input type=\"url\" class=\"form-control\" id=\"image_url\">
                        </div>
                    </div>
                    <div class=\"modal-footer\">
                        <button type=\"submit\" class=\"btn btn-primary\">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '<?php echo API_BASE; ?>';
        const token = localStorage.getItem('token');

        if (!token) {
            window.location.href = '/login';
        }

        async function loadAdmin() {
            try {
                const meRes = await fetch(`${API_BASE}/me`, {
                    headers: {'Authorization': `Bearer ${token}`}
                });
                const meData = await meRes.json();

                if (meData.user.role !== 'admin') {
                    window.location.href = '/dashboard';
                    return;
                }

                const motorcyclesRes = await fetch(`${API_BASE}/motorcycles`, {
                    headers: {'Authorization': `Bearer ${token}`}
                });
                const motorcyclesData = await motorcyclesRes.json();

                let content = '<h2>Motorcycles</h2><div class=\"row\">';
                motorcyclesData.data.forEach(m => {
                    content += `
                        <div class=\"col-md-4 mb-4\">
                            <div class=\"card\">
                                <img src=\"${m.image_url || 'https://via.placeholder.com/300x200'}\">
                                <div class=\"card-body\">
                                    <h6>${m.make} ${m.model}</h6>
                                    <p>$${m.price_per_day}</p>
                                    <button class=\"btn btn-sm btn-warning\" onclick=\"editMotorcycle(${m.id})\">Edit</button>
                                    <button class=\"btn btn-sm btn-danger\" onclick=\"deleteMotorcycle(${m.id})\">Delete</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                content += '</div>';

                document.getElementById('adminContent').innerHTML = content;
            } catch (e) {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        }

        document.getElementById('addMotorcycleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                make: document.getElementById('make').value,
                model: document.getElementById('model').value,
                year: document.getElementById('year').value,
                price_per_day: document.getElementById('price_per_day').value,
                status: document.getElementById('status').value,
                image_url: document.getElementById('image_url').value,
            };

            try {
                const res = await fetch(`${API_BASE}/motorcycles`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData),
                });

                if (res.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('addMotorcycleModal')).hide();
                    loadAdmin();
                }
            } catch (e) {
                console.error(e);
            }
        });

        function logout() {
            // logout logic
            localStorage.removeItem('token');
            window.location.href = '/';
        }

        loadAdmin();
    </script>
</body>
</html>
