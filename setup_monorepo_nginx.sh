#!/bin/bash
# Production setup script for MMS Monorepo (Laravel Backend + Custom PHP Frontend)
# Run as root/sudo on Ubuntu 20.04+

set -e

echo "🚀 Setting up Motorcycle Rental Management System (mms)..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# App name and paths
APP_NAME=\"mms\"
APP_DIR=\"/var/www/$APP_NAME\"
BACKEND_DIR=\"$APP_DIR/backend\"
FRONTEND_DIR=\"$APP_DIR/frontend\"

info() { echo -e \"${GREEN}[INFO]${NC} $1\"; }
warn() { echo -e \"${YELLOW}[WARN]${NC} $1\"; }
error() { echo -e \"${RED}[ERROR]${NC} $1\"; exit 1; }

# 1. Install system dependencies
info \"Installing Nginx, PHP-FPM, Composer, MySQL...\"
apt update
apt install -y nginx php8.2-fpm php8.2-mysql php8.2-cli php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath unzip curl

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer || error \"Composer install failed\"

# Install MySQL (or use existing)
if ! mysqladmin ping -hlocalhost -u root --password=\"\" &>/dev/null; then
    debconf-set-selections <<< \"mysql-server mysql-server/root_password password rootpass\"
    debconf-set-selections <<< \"mysql-server mysql-server/root_password_again password rootpass\"
    apt install -y mysql-server
    mysql -u root -prootpass -e \"CREATE DATABASE IF NOT EXISTS $APP_NAME;\"
fi

# 2. Deploy code
info \"Copying code to $APP_DIR...\"
mkdir -p \"$APP_DIR\"
rsync -av --exclude='setup_monorepo_nginx.sh' --exclude='.git' /workspaces/mms/ \"$APP_DIR/\" || error \"Code copy failed\"

cd \"$BACKEND_DIR\"
composer install --no-dev --optimize-autoloader
chown -R www-data:www-data .

# Generate Laravel key (assume .env copied/setup)
cp .env.example .env
php artisan key:generate --force
# Note: User must run php artisan migrate after

cd \"$FRONTEND_DIR\"
chown -R www-data:www-data .

# 3. Nginx configuration
info \"Configuring Nginx...\"
cat > /etc/nginx/sites-available/$APP_NAME << EOF
server {
    listen 80;
    server_name _;
    root $APP_DIR;
    index index.php index.html;

    # Frontend routes (/)
    location / {
        try_files \$uri \$uri/ /frontend/public/index.php?\$query_string;
    }

    # Backend API (/api/*)
    location /api {
        alias $BACKEND_DIR/public/;
        try_files \$uri \$uri/ /backend/public/index.php?\$query_string;

        location ~ \.php\$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            include fastcgi_params;
        }
    }

    # Security: block sensitive files
    location ~ /\. {
        deny all;
    }

    # Static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control \"public, immutable\";
    }
}
EOF

ln -sf /etc/nginx/sites-available/$APP_NAME /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default

nginx -t && systemctl restart nginx || error \"Nginx config failed\"

# 4. PHP-FPM pool (already installed)
systemctl enable --now php8.2-fpm

# 5. Firewall
ufw allow 'Nginx Full' || true
ufw --force enable || true

info \"✅ Setup complete!\"
echo \"🌐 Frontend: http://localhost/\"
echo \"🔌 API: http://localhost/api\"
echo \"📊 DB: Name=$APP_NAME, root/rootpass\"
echo \"💡 Next: cd $BACKEND_DIR && php artisan migrate --seed (dev) or copy .env with DB creds\"

