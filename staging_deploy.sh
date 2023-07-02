set -e

echo "Deploying application ..."


set -e

echo "Deploying application ..."

git add .

git commit -m "Local changes" || true

git pull origin development

# Install composer dependencies
composer install  --no-interaction

# Clear the old cache
php artisan clear-compiled

# Run database migrations
php artisan migrate --force

# purge cache
php artisan config:clear


echo "Application deployed!"
