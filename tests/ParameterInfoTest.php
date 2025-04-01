<?php

declare(strict_types=1);

namespace Duon\Wire\Tests;

use Duon\Wire\ParameterInfo;
use Duon\Wire\Tests\Fixtures\TestClassApp;
use Duon\Wire\Tests\Fixtures\TestClassUnionTypeConstructor;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;
use ReflectionFunction;

#[CoversClass(ParameterInfo::class)]
final class ParameterInfoTest extends TestCase
{
	public function testParameterInfoClass(): void
	{
		$rcls = new ReflectionClass(TestClassUnionTypeConstructor::class);
		$constructor = $rcls->getConstructor();
		$param = $constructor->getParameters()[0];

		$this->assertSame(
			'Duon\Wire\Tests\Fixtures\TestClassUnionTypeConstructor::__construct(' .
			'..., Duon\Wire\Tests\Fixtures\TestClassApp|' .
			'Duon\Wire\Tests\Fixtures\TestClassRequest $param, ...)',
			ParameterInfo::info($param),
		);
	}

	public function testParameterInfoFunction(): void
	{
		$rfun = new ReflectionFunction(function (TestClassApp $app) {
			$app->debug();
		});
		$param = $rfun->getParameters()[0];

		$this->assertSame(
			'Duon\Wire\Tests\ParameterInfoTest::{closure:Duon\Wire\Tests\ParameterInfoTest' .
			'::testParameterInfoFunction():33}(..., Duon\Wire\Tests\Fixtures\TestClassApp $app, ...)',
			ParameterInfo::info($param),
		);
	}
}
