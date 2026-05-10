FROM php:8.5

# Install system dependencies and PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# This is the magic line that lets PHP talk to your MySQL container
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www


# FROM node:22-slim


# WORKDIR /app

# COPY package*.json ./

# RUN npm install

# COPY . .

# ENV PORT=3000
# ENV NODE_ENV=development

# EXPOSE 3000
# EXPOSE 4000

# CMD ["composer", "dev"]