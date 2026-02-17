## Générer les scripts de migration

```
docker exec -ti php_selen php bin/console doctrine:schema:update --dump-sql
```

## Installation
- installer le fichier google-service-account.json