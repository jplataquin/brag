#!/bin/bash

# BRAG - Ubuntu Server Automated Setup Script
# Installs Git, Nginx, MySQL 8.0, PHP 8.3, Composer, and NVM/Node.js

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}====================================================${NC}"
echo -e "${CYAN}   BRAG ARENA - AUTOMATED SERVER SETUP (UBUNTU)    ${NC}"
echo -e "${CYAN}====================================================${NC}"

# Check for root
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}Please run as root (use sudo).${NC}"
  exit
fi

# 1. Update System
echo -e "\n${GREEN}[1/7] Updating system packages...${NC}"
apt update && apt upgrade -y

# 2. Basic Dependencies
echo -e "\n${GREEN}[2/7] Installing basic dependencies...${NC}"
apt install -y software-properties-common curl zip unzip git sqlite3 ufw

# 3. Install PHP 8.3
echo -e "\n${GREEN}[3/7] Installing PHP 8.3 and extensions...${NC}"
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-sqlite3 php8.3-redis

# 4. Install Nginx
echo -e "\n${GREEN}[4/7] Installing Nginx...${NC}"
apt install -y nginx
systemctl enable nginx
systemctl start nginx

# 5. Install MySQL 8.0
echo -e "\n${GREEN}[5/7] Installing MySQL 8.0...${NC}"
apt install -y mysql-server
systemctl enable mysql
systemctl start mysql
echo -e "${CYAN}NOTE: Remember to run 'sudo mysql_secure_installation' after this script finishes.${NC}"

# 6. Install Composer
echo -e "\n${GREEN}[6/7] Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# 7. Install NVM & Node.js
echo -e "\n${GREEN}[7/7] Installing NVM and Node.js (LTS)...${NC}"
# Use a non-root user for NVM if possible, but here we install for the current shell
export NVM_DIR="$HOME/.nvm"
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm install --lts

# Configuration Recommendations
echo -e "\n${CYAN}====================================================${NC}"
echo -e "${GREEN}             SETUP COMPLETE!                       ${NC}"
echo -e "${CYAN}====================================================${NC}"
echo -e "Next steps for deployment:"
echo -e "1. Run ${CYAN}sudo mysql_secure_installation${NC}"
echo -e "2. Create your database: ${CYAN}mysql -u root -e 'CREATE DATABASE brag;'${NC}"
echo -e "3. Clone your repo and run: ${CYAN}composer install && npm install && npm run build${NC}"
echo -e "4. Copy .env: ${CYAN}cp .env.example .env && php artisan key:generate${NC}"
echo -e "5. Run migrations: ${CYAN}php artisan migrate${NC}"
echo -e "6. Configure Nginx site block in /etc/nginx/sites-available/${NC}"
echo -e "7. (Recommended) Run ${CYAN}php artisan octane:install${NC} if using Octane.${NC}"
echo -e "${CYAN}====================================================${NC}"
