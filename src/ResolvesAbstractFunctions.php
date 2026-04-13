<?php

declare(strict_types=1);

namespace Duon\Wire;

use ReflectionFunctionAbstract;
use ReflectionParameter;

/** @psalm-api */
trait ResolvesAbstractFunctions
{
	abstract protected function creator(): CreatorInterface;

	protected function resolveArgs(
		ReflectionFunctionAbstract $rfn,
		array $predefinedArgs,
		array $predefinedTypes,
		?callable $injectCallback,
	): array {
		return new ArgumentResolver($this->creator())->resolve(
			$rfn,
			$predefinedArgs,
			$predefinedTypes,
			$injectCallback,
		);
	}

	protected function resolveParam(
		ReflectionParameter $param,
		array $predefinedTypes,
		?callable $injectCallback,
	): mixed {
		return new ParameterResolver($this->creator())->resolve(
			$param,
			$predefinedTypes,
			$injectCallback,
		);
	}

	/** @return array<non-empty-string, mixed> */
	protected function resolveInjectedArgs(
		ReflectionFunctionAbstract $rfn,
		array $predefinedTypes,
		?callable $injectCallback,
	): array {
		return new ArgumentResolver($this->creator())->resolveInjectedArgs(
			$rfn,
			$predefinedTypes,
			$injectCallback,
		);
	}
}
