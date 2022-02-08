# Docker Paas Development Environment

## Host: http://localhost:8888

### Requirements
1. `docker 20.10.12`
2. `docker-compose 1.29.2`
3. `git 2.25.1`

#### PHP
- `php:7.4-fpm-buster`
- `composer:2.0`
- `git unzip libzip-dev libicu-dev libonig-dev intl pdo_mysql zip bcmath mysqli`

#### NGINX
- `nginx:1.18-alpine`

### Ports
- `nginx: 8888`

### Volumes
- `./src:/var/www`

### Dotenv
- `.env`

### fastcgi_pass
-  `php:9000`


### Setup

1.  `[generate a new repository from this template](https://github.com/superwpheroes/paas/generate)`
2.  `git clone https://github.com/you/your-repo.git <your-project>`
3.  `cd <your-project>'
4.  `git submodule update --init --recursive`
5.  `docker-compose up`
