<?php

declare(strict_types=1);

namespace Duon\Wire\Tests\Fixtures;

use Duon\Wire\Call;

#[Call('increment')]
class TestClassCallCounter
{
	public int $calls = 0;

	public function increment(): void
	{
		$this->calls++;
	}
}
