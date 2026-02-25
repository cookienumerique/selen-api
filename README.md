# Installation du projet


## Cloner le projet
```bash
git@github.com:cookienumerique/selen-api.git
```

## Installer les dépendances
```bash
composer install
```

## Se mettre sur la bonne branche 
```bash
git checkout x.xx.x
```

## Installer les fichiers de configuration
```bash
cp .env.example .env
```

## Installer docker-compose
Voir ici : https://geekindustry.org/comment-installer-docker-et-docker-compose/
Autoriser user selen 
```bash
sudo usermod -aG docker selen && exit
```

## Monter les containers
```bash
docker compose up -d
```

## Installer les dépendances
```bash
docker exec -ti php_selen composer install
```

## Restaurer la database

## Générer les clés
```bash
docker exec -ti php_selen php bin/console lexik:jwt:generate-keypair
```

## Copier les médias sur le server
```bash
rsync -avz -e "ssh -p {port}" /mnt/selen/ {user}@{domain}.fr:/mnt/selen/
```

## copier le fichier google-service-account.json
