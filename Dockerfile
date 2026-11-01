FROM node:20-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.2-fpm-alpine
RUN apk add --no-cache nginx postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html
COPY . .
COPY --from=builder /app/public/build ./public/build

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# Setup Nginx
RUN echo 'server { \
    listen 8080; \
    root /var/www/html/public; \
    index index.php index.html; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        include fastcgi_params; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
    } \
}' > /etc/nginx/http.d/default.conf

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

# Auto Migrate & Start Server
CMD sh -c "php artisan migrate:fresh --seed --force && php-fpm -D && nginx -g 'daemon off;'"