<?php

declare(strict_types=1);

namespace Duon\Wire;

use Duon\Wire\Exception\WireException;
use ReflectionNamedType;
use ReflectionParameter;

final class ParameterResolver
{
	public function __construct(
		private readonly CreatorInterface $creator,
	) {}

	public function resolve(
		ReflectionParameter $param,
		array $predefinedTypes,
		?callable $injectCallback,
	): mixed {
		$type = $param->getType();

		if ($type instanceof ReflectionNamedType) {
			$container = $this->creator->container();
			$typeName = ltrim($type->getName(), '?');

			if (isset($predefinedTypes[$typeName])) {
				return $predefinedTypes[$typeName];
			}

			if ($container?->has($typeName)) {
				return $container->get($typeName);
			}

			if (class_exists($typeName)) {
				return $this->creator->create(
					$typeName,
					predefinedTypes: $predefinedTypes,
					injectCallback: $injectCallback,
				);
			}

			if ($param->isDefaultValueAvailable()) {
				return $param->getDefaultValue();
			}

			throw new WireException(
				"Unresolvable parameter. Source: \n" . ParameterInfo::info($param),
			);
		}

		if ($type) {
			throw new WireException(
				"Cannot resolve union or intersection types. Source: \n" . ParameterInfo::info($param),
			);
		}

		throw new WireException(
			"To be resolvable, classes must have fully typed constructor parameters. Source: \n"
				. ParameterInfo::info($param),
		);
	}
}
