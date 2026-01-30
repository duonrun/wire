# Duon Wire

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/622ad71c3dc4461d813ff854156deadf)](https://app.codacy.com/gh/duonrun/wire/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/622ad71c3dc4461d813ff854156deadf)](https://app.codacy.com/gh/duonrun/wire/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)
[![Psalm level](https://shepherd.dev/github/duonrun/wire/level.svg?)](https://shepherd.dev/github/duonrun/wire)
[![Psalm coverage](https://shepherd.dev/github/duonrun/wire/coverage.svg?)](https://shepherd.dev/github/duonrun/wire)

**_Wire_** provides an autowiring object creator that utilizes PHP's reflection
capabilities to automatically resolve constructor arguments recursively. It
additionally comes with classes that assist in resolving arguments of callables
such as functions, methods, closures or class constructors. It can be combined
with a PSR-11 dependency injection container.

**Wire** is a PHP dependency injection tool that automatically constructs
objects by resolving their dependencies. Using PHP's reflection API, **Wire**
recursively analyzes and fulfills constructor arguments without manual
configuration. It additionally includes utilities for resolving dependencies in
various callable types—including functions, methods, closures, and class
constructors. **_Wire_** seamlessly integrates with PSR-11 compliant dependency
injection containers.

Documentation can be found on the website: [duon.sh/wire](https://duon.sh/wire/)

## Installation

```bash
composer require duon/wire
```

## Basic usage

```php
use Duon\Wire\Wire;

class Value
{
    public function get(): string
    {
        return 'Autowired Value';
    }
}

class Model
{
    public function __construct(protected Value $value) {}

    public function value(): string
    {
        return $this->value->get();
    }
}

$creator = Wire::creator();
$model = $creator->create(Model::class);

assert($model instanceof Model);
assert($model->value() === 'Autowired Value');
```

## License

This project is licensed under the [MIT license](LICENSE.md).
