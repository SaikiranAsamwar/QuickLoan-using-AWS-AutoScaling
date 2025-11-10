# QuickLoan – A Scalable & Highly Available Web App on AWS
QuickLoan is a dynamic web application built on PHP, HTML, CSS, and JavaScript. It provides a robust, user-friendly platform for loan applications, designed with a focus on scalability, high availability, and fault tolerance. Deployed on AWS, the application leverages a sophisticated architecture to ensure seamless performance under any load.

---

## 📌 Project Overview
- **Backend:** PHP (Apache / Nginx + PHP-FPM)
- **Frontend:** HTML, CSS, JavaScript
- **Database:** Amazon RDS (MySQL)
- **Cloud Platform:** AWS

The application is deployed inside a custom **VPC** with proper subnets, Security Groups, and scaling configurations.  
It uses **Auto Scaling** + **Application Load Balancer (ALB)** for high availability and fault tolerance.

---

## ⚙️ Architecture Workflow

1. **VPC Setup**
   - Created custom VPC with public & private subnets.
   - Configured route tables and Internet Gateway.

2. **EC2 Setup**
   - Launched an EC2 instance (Amazon Linux 2023, 2GB RAM).
   - Installed Nginx, PHP, and required extensions.
   - Deployed QuickLoan code (PHP, HTML, CSS, JS).
   - Validated application manually.

3. **Database Setup**
   - Created RDS (MySQL) instance in a private subnet.
   - Configured Security Groups to allow traffic only from EC2.
   - Updated `db_connect.php` with RDS endpoint, username, password, and db_name.

4. **AMI & Auto Scaling**
   - Created AMI from validated EC2 instance.
   - Configured Launch Template with AMI.
   - Created Auto Scaling Group with scaling policies (based on CPU usage).

5. **Load Balancer**
   - Configured Application Load Balancer (ALB).
   - Target group linked with Auto Scaling Group instances.
   - Health checks enabled for fault tolerance.

6. **Scaling & Monitoring**
   - Verified Auto Scaling (scale up/down with load).
   - Monitored logs and metrics using CloudWatch.

---
# Amazon Linux 2023 (2GB RAM) – PHP, MySQL, Nginx Setup Guide

## 1. Hostname & System Update
```bash
sudo hostnamectl set-hostname appsrv
sudo dnf update
```

## 2. Install and Enable Nginx
```bash
sudo dnf install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```

## 3. Install PHP + PHP-FPM
```bash
sudo dnf install php8.2 php-fpm php-mysqlnd php-pdo php-mbstring -y
php -v
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
sudo systemctl restart nginx
```

* php-fpm listens on socket or TCP, check `/etc/php-fpm.d/www.conf`

## 4. Install MariaDB Client/Server
```bash
sudo yum install mariadb105 -y
```
or
```bash
sudo dnf install mariadb-server -y
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

## 5. Create DB / Run init.sql
```bash
mysql -h <db_endpoint> -u <username> -p < init.sql
```
Example init.sql:
```sql
CREATE DATABASE IF NOT EXISTS quickloan CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE quickloan;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 6. Copy Application Files
```bash
sudo cp -r includes public /usr/share/nginx/html/
```

## 7. Permissions
```bash
sudo chown -R nginx:nginx /usr/share/nginx/html/public
sudo chmod -R 755 /usr/share/nginx/html/public
```

If SELinux enabled:
```bash
sudo restorecon -Rv /usr/share/nginx/html/public
sudo setsebool -P httpd_can_network_connect on
```

## 8. Configure Nginx
Edit `quickloan.conf`, set `server_name` and root. Then:
```bash
sudo cp /usr/share/nginx/html/nginx/quickloan.conf /etc/nginx/conf.d/
sudo nginx -t
sudo systemctl restart nginx
```

Example `quickloan.conf`:
```nginx
server {
    listen 80;
    server_name example.com;   # replace with domain or IP
    root /usr/share/nginx/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php-fpm/www.sock;
        # or fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
    }
}
```

## 9. Database Connection File
File: `/usr/share/nginx/html/includes/db_connect.php`
```php
<?php
$host = 'db_endpoint';
$db   = 'db_name';
$user = 'username';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    die('Database connection failed.');
}
?>
```

## 10. Troubleshooting
- **502 Bad Gateway**: PHP-FPM not reachable; check service + fastcgi_pass.
- **403 Forbidden**: File/dir permissions issue.
- **DB connection fails**: Security group, endpoint, credentials, SELinux bools.

## 11. Checklist
1. Hostname + system update
2. Install nginx
3. Install PHP + php-fpm
4. Install mariadb client/server
5. Run init.sql
6. Copy app files
7. Set permissions
8. Configure quickloan.conf
9. Restart nginx
10. Add db_connect.php

----

### 📦 Scaling Configuration
  - **Create AMI** from the configured EC2 instance.
  - **Launch Template** using AMI + instance type + security group.
  - **Auto Scaling Group:**
      - Minimum: 1
      - Desired: 2
      - Maximum: 4
      - Scaling policy: Based on CPU Utilization / Request Count.
  - **Application Load Balancer:**
      - Listener on port 80.
      - Target Group attached to ASG.
      - Health checks enabled.

### ✅ Verification

  - Open Load Balancer DNS name in browser → App should load.
  - Stress test EC2 → Verify Auto Scaling adds new instances.
  - Stop instance manually → Verify ALB routes traffic to healthy instances.

### 📊 Monitoring

  - **CloudWatch:** Monitor CPU, memory, and scaling events.
  - **RDS Logs & Metrics:** Monitor DB performance.
  - **ALB Access Logs:** Monitor incoming traffic.

---

## 👨‍💻 Author

**Saikiran Rajesh Asamwar**  
Certified AWS DevOps Engineer

- 🌐 GitHub: [@SaikiranAsamwar](https://github.com/SaikiranAsamwar)
- 💼 LinkedIn: [Saikiran Asamwar](https://www.linkedin.com/in/saikiran-asamwar/)
- 📧 Email: saikiranasamwar@gmail.com

---
