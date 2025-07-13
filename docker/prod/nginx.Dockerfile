FROM nginx:alpine

# Copy custom nginx config
COPY docker/prod/default.conf /etc/nginx/conf.d/default.conf

# Copy built static files from the Symfony build stage
COPY ./ /var/www/html/
