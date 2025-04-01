<?php

declare(strict_types=1);

namespace Duon\Wire\Tests\Fixtures;

use Duon\Wire\WireContainer;

class WireizedContainer extends Container implements WireContainer
{
	public function definition(string $id): mixed
	{
		return $this->entries[$id];
	}
}
