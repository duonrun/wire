<?php

declare(strict_types=1);

namespace Duon\Wire\Tests\Fixtures;

use Duon\Wire\CreatorInterface;
use Duon\Wire\ResolvesAbstractFunctions;
use ReflectionFunctionAbstract;
use ReflectionParameter;

class ResolvesAbstractFunctionsProxy
{
	use ResolvesAbstractFunctions;

	public function __construct(
		private readonly CreatorInterface $creator,
	) {}

	public function param(
		ReflectionParameter $param,
		array $predefinedTypes = [],
		?callable $injectCallback = null,
	): mixed {
		return $this->resolveParam($param, $predefinedTypes, $injectCallback);
	}

	/** @return array<non-empty-string, mixed> */
	public function injectedArgs(
		ReflectionFunctionAbstract $rfn,
		array $predefinedTypes = [],
		?callable $injectCallback = null,
	): array {
		return $this->resolveInjectedArgs($rfn, $predefinedTypes, $injectCallback);
	}

	protected function creator(): CreatorInterface
	{
		return $this->creator;
	}
}
