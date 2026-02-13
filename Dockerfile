# Railway deploy: use PHP 8.2 (Nixpacks no longer supports 8.0).
# Local dev can stay on PHP 7.4; this file is only for deploy.
FROM php:8.2-cli

RUN docker-php-ext-install pdo_mysql

WORKDIR /app

COPY . .

# Railway injects PORT at runtime; must run in shell so $PORT is expanded
EXPOSE 8080
ENTRYPOINT []
CMD ["/bin/sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]
