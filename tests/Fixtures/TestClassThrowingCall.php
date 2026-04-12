<?php

declare(strict_types=1);

namespace Duon\Wire\Tests\Fixtures;

use Duon\Wire\Call;
use InvalidArgumentException;

#[Call('init')]
class TestClassThrowingCall
{
	public function init(): void
	{
		throw new InvalidArgumentException('call method failed');
	}
}
