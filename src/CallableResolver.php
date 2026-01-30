<?php

declare(strict_types=1);

namespace Duon\Wire;

use Closure;
use Override;
use ReflectionFunction;

/** @psalm-api */
class CallableResolver
{
	use ResolvesAbstractFunctions;

	public function __construct(
		protected readonly CreatorInterface $creator,
	) {}

	/** @psalm-param callable-array|callable $callable */
	public function resolve(
		array|callable $callable,
		array $predefinedArgs = [],
		array $predefinedTypes = [],
		?callable $injectCallback = null,
	): array {
		$callable = Closure::fromCallable($callable);
		$rfn = new ReflectionFunction($callable);

		return $this->resolveArgs($rfn, $predefinedArgs, $predefinedTypes, $injectCallback);
	}

	#[Override]
	public function creator(): CreatorInterface
	{
		return $this->creator;
	}
}
