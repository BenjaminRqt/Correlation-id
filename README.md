# Correlation ID

![PHP >= 8.3](https://img.shields.io/static/v1?label=PHP&message=^8.3&color=787CB5&style=for-the-badge&logo=php)
![Symfony >= 6.0](https://img.shields.io/static/v1?label=symfony&message=%5E6.0&color=787CB5&style=for-the-badge&logo=symfony)
![phpstan Level 8](https://img.shields.io/static/v1?label=phpstan&message=Level%208&color=%3CCOLOR%3E&style=for-the-badge)

## Fonctionnalités

* Permet d'ajouter un CorrelationId dans les logs de l'application
* Permet de transmettre un CorrelationId un autre application via les Headers
* Permet de retrouver dans les headers de réponse d'une requête le correlationId de la requête

Si un correlationId est présent dans les headers de la requête, alors il sera repris automatiquement. Autrement, un nouveau 
correlationId sera généré est utilisé durant le process de la requête

## Installation

```sh
composer require com-company/correlation-id
```

## Configuration

La configuration par défaut nomme le header 'X-Correlation-ID', il est cependant possible de renommer le header.

* `header_name` - Le nom du header du correlationId.

`config/correlation_id.yaml`:

```yaml
correlation_id:
    header_name: 'X-Correlation-ID'
```

## Messenger Middleware

Le middleware de Messenger ajoutera l'ID de corrélation à vos messages. Il vous suffit d'ajouter le middleware à votre bus.
