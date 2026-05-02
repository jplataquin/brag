    #!/bin/bash

    # Mooplink - Ubuntu Server Automated Setup Script
    # Installs Git, Nginx, MySQL 8.0, PHP 8.3, Composer, and NVM/Node.js

    # Colors for output
    RED='\033[0;31m'
    GREEN='\033[0;32m'
    CYAN='\033[0;36m'
    NC='\033[0m' # No Color

    echo -e "${CYAN}====================================================${NC}"
    echo -e "${CYAN}   MOOPLINK - AUTOMATED SERVER SETUP (UBUNTU)    ${NC}"
    echo -e "${CYAN}====================================================${NC}"

    # Check for root
    if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root (use sudo).${NC}"
    exit
    fi

    # Function to wait for apt locks
    wait_for_apt_locks() {
      echo -e "\n${CYAN}Checking for active apt/dpkg processes...${NC}"
      local count=0
      while fuser /var/lib/dpkg/lock-frontend >/dev/null 2>&1 || fuser /var/lib/dpkg/lock >/dev/null 2>&1 || fuser /var/lib/apt/lists/lock >/dev/null 2>&1 ; do
          if [ $count -gt 24 ]; then # 24 * 5 seconds = 2 minutes
              echo -e "\n${RED}Apt lock has been held for over 2 minutes. This usually means a process crashed or is stuck.${NC}"
              echo -e "${CYAN}Attempting to clear stale locks and repair package manager...${NC}"
              killall apt apt-get dpkg unattended-upgrades 2>/dev/null
              rm -f /var/lib/apt/lists/lock
              rm -f /var/cache/apt/archives/lock
              rm -f /var/lib/dpkg/lock
              rm -f /var/lib/dpkg/lock-frontend
              dpkg --configure -a
              break
          fi
          echo -e "${CYAN}Waiting for background apt processes to finish (Attempt $((count+1))/24)...${NC}"
          sleep 5
          ((count++))
      done
      echo -e "${GREEN}Apt locks released or cleared. Proceeding...${NC}"
    }
    # 1. Update System
    wait_for_apt_locks
    echo -e "\n${GREEN}[1/8] Updating system packages...${NC}"
    apt update && apt upgrade -y

    # 1.5 Setup Swap Space
    echo -e "\n${GREEN}[2/8] Setting up 2GB Swap Space...${NC}"
    if grep -q "swapfile" /etc/fstab; then
        echo -e "${CYAN}Swap space already configured. Skipping.${NC}"
    else
        fallocate -l 2G /swapfile
        chmod 600 /swapfile
        mkswap /swapfile
        swapon /swapfile
        echo '/swapfile none swap sw 0 0' | tee -a /etc/fstab
        echo -e "${GREEN}Swap space created successfully.${NC}"
    fi

    # 2. Basic Dependencies
    echo -e "\n${GREEN}[3/8] Installing basic dependencies...${NC}"
    apt install -y software-properties-common curl zip unzip git sqlite3 ufw

    # 3. Install PHP 8.3
    echo -e "\n${GREEN}[4/8] Installing PHP 8.3 and extensions...${NC}"
    add-apt-repository ppa:ondrej/php -y
    apt update
    apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-sqlite3 php8.3-redis

    # 4. Install Nginx
    echo -e "\n${GREEN}[5/8] Installing Nginx...${NC}"
    apt install -y nginx
    systemctl enable nginx
    systemctl start nginx

    # 5. Install MySQL 8.0
    echo -e "\n${GREEN}[6/8] Installing MySQL 8.0...${NC}"
    apt install -y mysql-server
    systemctl enable mysql
    systemctl start mysql
    echo -e "${CYAN}NOTE: Remember to run 'sudo mysql_secure_installation' after this script finishes.${NC}"

    # 6. Install Composer
    echo -e "\n${GREEN}[7/8] Installing Composer...${NC}"
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer

    # 7. Install NVM & Node.js
    echo -e "\n${GREEN}[8/8] Installing NVM and Node.js (LTS)...${NC}"
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
    echo -e "2. Configure Nginx site block in /etc/nginx/sites-available/${NC}"
    echo -e "${CYAN}====================================================${NC}"
