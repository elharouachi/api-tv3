# Ajouter un endpoint ou un champ

## Créer un nouveau endpoint

  * Créer une entité doctrine
    * Dans `src/Entity`
    * Mettre à jour le schéma de base de données:
      ```bash
      ./bin/console doctrine:schema:update -f
      ```
## Ajouter un champ à un endpoint

  * Ajouter une propriété à l'entité Doctrine associée au endpoint
    * Dans `src/Entity`
    * Mettre à jour le schéma de base de données:
      ```bash
      ./bin/console doctrine:schema:update -f
      ```
  * Ajouter, si besoin, un champ à l'index index ElasticSearch associé à l'entité
    * Dans `config/fos_elastica.yaml`
    * Mettre à jour l'index dans ElasticSearch:
      ```bash
      ./bin/console fos:elastica:populate
      ```
  * Créer la migration Doctrine
    * Dans `src/Migrations`
  * Mettre à jour les fixtures
    * Dans `src/Resources/fixtures`
    * Mettre à jour le fichier `src/DataFixtures/ApiFixtures.php`
