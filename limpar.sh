
docker builder prune -af
docker image prune -af
docker system prune -a --volume s
docker system prune -f
docker system df
find . -name "._*" -delete
rm -rf vendor/
rm -rf node_modules/
rm -rf composer.lock
rm -rf .docker/postgresql/data
rm -rf public/img/data
rm -rf Storage/app/img/data/
mkdir -p public/img/data/
mkdir -p Storage/app/img/data/

for ((i=0; i<=10; i++)); do
  rm -rf "composer $i.lock"
done
