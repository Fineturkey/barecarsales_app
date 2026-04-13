FROM php:8.2-apache

# Install the mysqli and PDO MySQL extensions
RUN docker-php-ext-install mysqli pdo_mysql \
 && docker-php-ext-enable mysqli

# Enable mod_rewrite in case .htaccess rules are ever added
RUN a2enmod rewrite

# Copy all project files into the subdirectory that matches the
# internal link prefix used throughout the app (/barecarsales_app/…)
COPY . /var/www/html/barecarsales_app/

# Give Apache ownership of the files
RUN chown -R www-data:www-data /var/www/html/barecarsales_app

EXPOSE 80
