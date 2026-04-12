<?php

declare(strict_types=1);

namespace Duon\Wire\Tests;

use Duon\Wire\CallableResolver;
use Duon\Wire\Creator;
use Duon\Wire\Exception\WireException;
use Duon\Wire\Inject;
use Duon\Wire\Tests\Fixtures\TestClass;
use Duon\Wire\Tests\Fixtures\TestClassApp;
use Duon\Wire\Tests\Fixtures\TestClassUsingNested;

final class CallableResolverTest extends TestCase
{
	public function testGetClosureArgs(): void
	{
		$resolver = new CallableResolver($this->creator());
		$args = $resolver->resolve(
			static fn(Testclass $testobj, int $number = 13): string => $testobj::class . (string) $number,
		);

		$this->assertInstanceOf(TestClass::class, $args[0]);
		$this->assertSame(13, $args[1]);
	}

	public function testGetCallableObjectArgs(): void
	{
		$resolver = new CallableResolver($this->creator());
		$testobj = $this->creator()->create(TestClass::class);
		$args = $resolver->resolve($testobj);

		$this->assertSame('default', $args[0]);
		$this->assertSame(13, $args[1]);
	}

	public function testGetArgsWithPredefinedArgs(): void
	{
		$resolver = new CallableResolver($this->creator());
		$args = $resolver->resolve(
			static fn(Testclass $testobj, int $number): string => $testobj::class . (string) $number,
			predefinedArgs: ['number' => 17],
		);

		$this->assertInstanceOf(TestClass::class, $args[0]);
		$this->assertSame(17, $args[1]);
	}

	public function testGetArgsWithPredefinedTypes(): void
	{
		$resolver = new CallableResolver($this->creator());
		$args = $resolver->resolve(
			static fn(Testclass $testobj, int $number): string => $testobj::class . (string) $number,
			predefinedTypes: ['int' => 23],
		);

		$this->assertInstanceOf(TestClass::class, $args[0]);
		$this->assertSame(23, $args[1]);
	}

	public function testRejectsPositionalPredefinedArgsWhenInjectIsUsed(): void
	{
		$this->throws(WireException::class, 'predefined args must be named');

		$resolver = new CallableResolver($this->creator());
		$resolver->resolve(
			static fn(#[Inject('forced')] string $value): string => $value,
			predefinedArgs: ['positional'],
		);
	}

	public function testNestedClassesWithPredefinedAndInject(): void
	{
		$resolver = new CallableResolver($this->creator());
		$args = $resolver->resolve(
			[TestClassUsingNested::class, 'create'],
			injectCallback: static fn(Inject $inject): mixed => $inject->value . ' ' . $inject->meta['id'],
			predefinedTypes: ['string' => 'predefined-value'],
		);
		$result = TestClassUsingNested::create(...$args);

		$this->assertSame('callback injected id', $result->tcn->callback);
		$this->assertSame('predefined-value', $result->tcn->predefined->value);
	}

	public function testResolverUsesScopeLocalValues(): void
	{
		$root = $this->scopedWireContainer();
		$root->add(TestClassApp::class, new TestClassApp('root'));
		$scope = $root->scope();
		$scope->add(TestClassApp::class, new TestClassApp('scope'));
		$resolver = new CallableResolver(new Creator($scope));
		$args = $resolver->resolve(static fn(TestClassApp $app): string => $app->app());

		$this->assertSame('scope', $args[0]->app());
	}
}
