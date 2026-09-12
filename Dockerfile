FROM php:8.2-apache
COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads
RUN chown -R www-data:www-data /var/www/html
RUN a2enmod rewrite
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
EXPOSE 80
