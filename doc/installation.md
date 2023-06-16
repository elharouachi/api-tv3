# Installation de l'environnement de développement

## Pré-requis

  * Docker
  * Docker Compose

Sous Ubuntu:

```bash
sudo apt-get install docker docker-compose
```

### Ajout des noms d'hôtes

Ajoutez cette ligne au fichier `/etc/hosts`:

```
127.0.0.1 api-cinema.dev.boiteimmo.fr
```


### Créer le network Docker

```bash
docker network create cinema-backend
```

## Lancement de l'environnement de développement

```bash
docker-compose up  --build -d
docker-compose run --rm api-cinema-php-tools composer install
docker-compose run --rm api-cinema-php-tools ./bin/console doctrine:schema:create
```
