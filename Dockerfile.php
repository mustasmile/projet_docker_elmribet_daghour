# Utiliser l'image officielle PHP avec Apache
FROM php:7.4-apache

# Installer les dépendances pour l'extension PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html/

# Exposer le port 80
EXPOSE 80

# Configurer Apache pour exécuter le site
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Activer les modules Apache nécessaires
RUN a2enmod rewrite
