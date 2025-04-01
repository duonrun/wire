<?php

declare(strict_types=1);

namespace Duon\Wire\Tests\Fixtures;

class TestClassIntersectionTypeConstructor
{
	public function __construct(public TestClassApp&TestClassRequest $param) {}
}
