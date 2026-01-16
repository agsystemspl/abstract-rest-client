# Abstract REST Client

## Overview

A PHP (Guzzle) API Wrapper designed for simplicity and extensibility. This library provides an elegant, fluent, chainable interface for constructing complex API endpoints and handling various HTTP methods.

## Features

- 🔗 Fluent, chainable API design
- 🌐 Supports GET, POST, PUT, PATCH, DELETE, HEAD methods
- 🔧 Highly customizable and extensible
- 🔒 Safe URL encoding and path construction
- 📦 Flexible options management
- 🚀 Built on top of Guzzle HTTP

## Requirements

- PHP 7.0+
- Guzzle HTTP (^6.0 or ^7.0)

## Basic Usage

### Extending the Abstract Client

```php
use AGSystemsPl\RestClient\AbstractClient;

class MyApiClient extends AbstractClient
{
    protected function clientOptions(): array
    {
        return [
            'base_uri' => 'https://api.example.com/v1/'
        ];
    }
}

$client = new MyApiClient();

// Fluent path and method chaining
$response = $client->users->123->get(['format' => 'json']);
```

### Key Concepts

#### Path Construction
- Use property access or method calls to build paths
- Segments are automatically URL-encoded
- Null, false, and empty values are filtered out

#### Request Methods

```php
// GET request 
$client->users->get();

// POST request with JSON body
$client->users->post(['name' => 'John']);

// PUT request
$client->users->123->put(['name' => 'Updated']);

// DELETE request
$client->users->123->delete();
```

### Options Management

```php
// Set initial options
$client = new MyApiClient([
    'timeout' => 10,
    'verify' => false
]);

// Replace all options
$client->withOptions(['new' => 'configuration']);

// Merge additional options
$client->appendOptions(['extra' => 'settings']);
```

## Advanced Customization

Extend the abstract class to add custom behavior:

```php
class MyCustomClient extends AbstractClient
{
    // Override path handling
    protected function handlePath($segments): string
    {
        // Custom path processing logic
        return parent::handlePath($segments);
    }

    // Custom response processing
    protected function handleResponse($response)
    {
        // Custom logic, e.g., XML parsing
        return $response;
    }
}
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.

## Contact

agsystems.pl - [https://agsystems.pl](https://agsystems.pl)

Project Link: [https://github.com/agsystemspl/abstract-rest-client](https://github.com/agsystemspl/abstract-rest-client)