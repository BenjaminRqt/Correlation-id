# Correlation ID

![PHP >= 8.3](https://img.shields.io/static/v1?label=PHP&message=8.3+-+8.5&color=787CB5&style=for-the-badge&logo=php)
![Symfony >= 6.0](https://img.shields.io/static/v1?label=Symfony&message=6.0+-+8.0&color=787CB5&style=for-the-badge&logo=symfony)
![phpstan Level 8](https://img.shields.io/static/v1?label=phpstan&message=Level%208&color=4CAF50&style=for-the-badge)

A Symfony bundle that provides an easy way to manage a **Correlation ID** across your application.

---

## Features

- Adds a Correlation ID to your application logs
- Propagates the Correlation ID to other applications via HTTP headers
- Exposes the Correlation ID in the response headers
- Automatically reuses the Correlation ID from incoming requests when present
- Generates a new Correlation ID when none is provided

---

## Installation

```sh
composer require benjamin-rqt/correlation-id
````

---

## Configuration

By default, the Correlation ID header name is `X-Correlation-ID`.
You can override it if needed.

### Available options

* `header_name` – The HTTP header name used for the Correlation ID.

`config/packages/correlation_id.yaml`:

```yaml
correlation_id:
    header_name: 'X-Correlation-ID'
```

---

## HTTP Behavior

* If a Correlation ID is present in the incoming request headers, it will be reused.
* Otherwise, a new Correlation ID will be generated and used for the entire request lifecycle.
* The Correlation ID is always added to the response headers.

---

## Messenger Middleware

This bundle provides a Messenger middleware that automatically attaches the Correlation ID to dispatched messages.

To enable it, simply add the middleware to your Messenger bus configuration.

```yaml
framework:
    messenger:
        buses:
            messenger.bus.default:
                middleware:
                    - correlation_id.middleware
```

This ensures that:

* The Correlation ID from the HTTP request is propagated to Messenger messages
* The same Correlation ID is available when handling messages asynchronously