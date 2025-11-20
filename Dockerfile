# Dockerfile para entorno PHP CLI
FROM php:8.3-cli


# Instala extensiones y utilidades necesarias
RUN apt-get update \
    && apt-get install -y git unzip \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el código fuente
WORKDIR /app
COPY . /app

# Instala dependencias de Composer si existe composer.json
RUN if [ -f composer.json ]; then composer install --no-interaction; fi

# Comando por defecto: abre una shell interactiva
CMD ["bash"]
