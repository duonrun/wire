<?php

declare(strict_types=1);

namespace Duon\Wire\Tests\Fixtures;

use Duon\Wire\Inject;
use Duon\Wire\Type;

class TestClassNested
{
	public function __construct(
		#[Inject('callback', Type::Callback, id: 'injected id')]
		public readonly string $callback,
		public readonly TestClassPredefined $predefined,
	) {}
}
