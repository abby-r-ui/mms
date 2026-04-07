<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>MMS - Rent Motorcycle</title>
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
        <h1>Rent Motorcycle</h1>
        <div id=\"motorcycleDetails\" class=\"mb-4\"></div>
        <form id=\"rentForm\">
            <div class=\"mb-3\">
                <label class=\"form-label\">Start Date</label>
                <input type=\"date\" class=\"form-control\" id=\"startDate\" min=\"<?= date('Y-m-d') ?>\" required>
            </div>
            <div class=\"mb-3\">
                <label class=\"form-label\">End Date</label>
                <input type=\"date\" class=\"form-control\" id=\"endDate\" required>
            </div>
            <div class=\"mb-3\">
                <h5 id=\"totalPrice\">Total: $0</h5>
            </div>
            <button type=\"submit\" class=\"btn btn-success w-100\">Rent Now</button>
            <div id=\"message\" class=\"mt-3\"></div>
        </form>
    </div>

    <script>
        const API_BASE = '<?php echo API_BASE; ?>';
        const token = localStorage.getItem('token');
        const urlParams = new URLSearchParams(window.location.search);
        const motorcycleId = urlParams.get('id');

        if (!token || !motorcycleId) {
            window.location.href = '/';
        }

        async function loadMotorcycle() {
            const res = await fetch(`${API_BASE}/motorcycles/${motorcycleId}`, {
                headers: {'Authorization': `Bearer ${token}`}
            });
            const data = await res.json();
            document.getElementById('motorcycleDetails').innerHTML = `
                <div class=\"card\">
                    <img src=\"${data.data.image_url || 'https://via.placeholder.com/400x300'}\" class=\"card-img-top\">
                    <div class=\"card-body\">
                        <h4>${data.data.make} ${data.data.model} (${data.data.year})</h4>
                        <p>$${data.data.price_per_day}/day</p>
                        <p>${data.data.description}</p>
                    </div>
                </div>
            `;
        }

        document.getElementById('endDate').addEventListener('change', calculatePrice);
        document.getElementById('startDate').addEventListener('change', calculatePrice);

        function calculatePrice() {
            const start = new Date(document.getElementById('startDate').value);
            const end = new Date(document.getElementById('endDate').value);
            if (start && end && end > start) {
                const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('totalPrice').textContent = `Total: $${days * <?php echo json_encode($price_per_day ?? 100); ?>}`;
            }
        }

        document.getElementById('rentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = document.getElementById('message');
            message.innerHTML = '<div class=\"spinner-border\"></div> Booking...';

            try {
                const res = await fetch(`${API_BASE}/rentals`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        motorcycle_id: motorcycleId,
                        start_date: document.getElementById('startDate').value,
                        end_date: document.getElementById('endDate').value,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    message.innerHTML = '<div class=\"alert alert-success\">Booked! Check dashboard.</div>';
                    setTimeout(() => window.location.href = '/dashboard', 2000);
                } else {
                    message.innerHTML = '<div class=\"alert alert-danger\">Booking failed: ' + (data.message || 'Unknown error') + '</div>';
                }
            } catch (e) {
                message.innerHTML = '<div class=\"alert alert-danger\">Error: ' + e.message + '</div>';
            }
        });

        async function logout() {
            await fetch(`${API_BASE}/logout`, {
                method: 'POST',
                headers: {'Authorization': `Bearer ${token}`}
            });
            localStorage.removeItem('token');
            window.location.href = '/';
        }

        loadMotorcycle();
    </script>
</body>
</html>
