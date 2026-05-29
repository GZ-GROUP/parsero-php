# ─── Dockerfile ──────────────────────────────────────────────────────────────
# Imagen de producción para el Sistema de Gestión de Calificaciones
# Compatible con Dokploy (despliegue basado en Docker)
#
# Build:  docker build -t calificaciones .
# Run:    docker run -p 8080:80 calificaciones
# ─────────────────────────────────────────────────────────────────────────────

FROM php:8.2-apache

# Habilitar mod_rewrite (por si se agregan URLs amigables en el futuro)
RUN a2enmod rewrite

# Copiar el código fuente al directorio raíz de Apache
COPY src/ /var/www/html/

# Configurar permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar Apache para permitir .htaccess y FollowSymLinks
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Puerto expuesto (Apache por defecto)
EXPOSE 80

# El CMD por defecto de la imagen php:apache ya inicia Apache