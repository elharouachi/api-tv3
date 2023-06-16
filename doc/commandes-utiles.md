# Commandes utiles

Les commandes PHP s'exécutent dans le conteneur "api-cinema-php-tools" comme suit:

```bash
docker-compose run --rm api-cinema-php-tools [commande]
```

## Doctrine

  * Créer le schéma:
    ```bash
    bin/console doctrine:schema:create
    ```
  * Supprimer le schéma:
    ```bash
    bin/console doctrine:schema:drop
    ```
  * Mettre à jour le schéma:
    ```bash
    bin/console doctrine:schema:update
    ```
  * Charger les fixtures de dev:
    ```bash
    bin/console doctrine:fixtures:load
    ```
