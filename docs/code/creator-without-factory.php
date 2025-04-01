<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Duon\Wire\Creator;
// A PSR-11 container implementation like
// https://duon.dev/registry or https://php-di.org
use Duon\Wire\Tests\Fixtures\Container;

$container = new Container();
$creator = new Creator($container);

// Or without container
$creator = new Creator();
