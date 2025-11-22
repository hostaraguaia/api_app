find . -name "._*" -delete

chmod -R 777 storage/
[ -d ".docker/postgresql/data" ] && chmod -R 777 .docker/postgresql/data
[ -d "vendor" ] && chmod -R 777 vendor/
[ -d "node_modules" ] && chmod -R 777 node_modules/
[ -d "public/sorteios/data" ] && chmod -R 777 public/sorteios/data

rm -rf vendor/
rm -rf node_modules/
rm -rf composer.lock
rm -rf .docker/postgresql/data
rm -rf public/sorteios/data/
mkdir -p public/sorteios/data


for ((i=0; i<=10; i++)); do
  rm -rf "composer $i.lock"
done


# cp documentation/oauth/oauth-private.key  storage/
# cp documentation/oauth/oauth-public.key  storage/


# Build and start containers first
docker compose up -d --build

# Install PHP dependencies inside container
docker compose exec -u root app composer install

# Install Node dependencies inside container
docker compose exec -u root app npm install

# Build assets inside container
docker compose exec -u root app npm run build

# Publish assets
docker compose exec -u root app php artisan vendor:publish --tag=laravel-assets --ansi --force

# Enter container
docker compose exec app bash


# composer install
# #php artisan ui bootstrap --auth
# npm install
# composer update laravel/framework
# php artisan vendor:publish --tag=laravel-assets --ansi --force
# #npm run build
# docker compose up -d
# docker compose build app
# docker compose exec app bash


#php artisan migrateye
#php artisan module:seed rifayes
#php artisan passport:client --personal

