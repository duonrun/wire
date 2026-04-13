<?php

declare(strict_types=1);

namespace Duon\Wire;

use Duon\Wire\Exception\WireException;
use ReflectionFunctionAbstract;

final class ArgumentResolver
{
	public function __construct(
		private readonly CreatorInterface $creator,
	) {}

	public function resolve(
		ReflectionFunctionAbstract $rfn,
		array $predefinedArgs,
		array $predefinedTypes,
		?callable $injectCallback,
	): array {
		$injectedArgs = $this->resolveInjectedArgs($rfn, $predefinedTypes, $injectCallback);

		if ($injectedArgs !== [] && array_is_list($predefinedArgs) && $predefinedArgs !== []) {
			throw new WireException('When using Inject attributes, predefined args must be named');
		}

		$combinedArgs = array_merge(
			$injectedArgs,
			$predefinedArgs,
		);

		$args = [];
		$parameters = $rfn->getParameters();
		$countPredefined = count($combinedArgs);
		$parameterResolver = new ParameterResolver($this->creator);

		if (array_is_list($combinedArgs) && $countPredefined > 0) {
			// predefined args are not named, use them as they are
			$args = $combinedArgs;
			$parameters = array_slice($parameters, $countPredefined);
		}

		foreach ($parameters as $param) {
			$name = $param->getName();

			if (isset($combinedArgs[$name])) {
				/** @psalm-var list<mixed> */
				$args[] = $combinedArgs[$name];
			} else {
				/** @psalm-var list<mixed> */
				$args[] = $parameterResolver->resolve($param, $predefinedTypes, $injectCallback);
			}
		}

		return $args;
	}

	/** @return array<non-empty-string, mixed> */
	public function resolveInjectedArgs(
		ReflectionFunctionAbstract $rfn,
		array $predefinedTypes,
		?callable $injectCallback,
	): array {
		/** @var array<non-empty-string, mixed> */
		$result = [];

		foreach ($rfn->getParameters() as $param) {
			$injectAttr = $param->getAttributes(Inject::class)[0] ?? null;

			if ($injectAttr) {
				$instance = $injectAttr->newInstance();
				/** @psalm-suppress MixedAssignment */
				$result[$param->name] = Injected::value(
					$instance,
					$this->creator,
					$predefinedTypes,
					$injectCallback,
				);
			}
		}

		return $result;
	}
}
