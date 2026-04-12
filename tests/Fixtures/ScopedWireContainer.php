<?php

declare(strict_types=1);

namespace Duon\Wire\Tests\Fixtures;

use Closure;
use Duon\Wire\WireContainer;
use Exception;
use Psr\Container\NotFoundExceptionInterface as NotFoundException;

final class ScopedWireContainer implements WireContainer
{
	public const string SHARED = 'shared';
	public const string SCOPED = 'scoped';
	public const string TRANSIENT = 'transient';

	/**
	 * @var array<non-empty-string, array{definition: mixed, lifetime: string}>
	 */
	protected array $entries = [];

	/** @var array<non-empty-string, mixed> */
	protected array $instances = [];

	public function __construct(
		protected readonly ?self $parent = null,
	) {}

	public function scope(): self
	{
		return new self($this->root());
	}

	public function add(string $id, mixed $entry = null, string $lifetime = self::SHARED): void
	{
		$this->entries[$id] = [
			'definition' => $entry ?? $id,
			'lifetime' => $lifetime,
		];
		unset($this->instances[$id]);
	}

	public function has(string $id): bool
	{
		return isset($this->entries[$id]) || $this->parent?->has($id) ?? false;
	}

	public function get(string $id): mixed
	{
		if (array_key_exists($id, $this->instances)) {
			return $this->instances[$id];
		}

		$resolved = $this->findEntry($id);

		if ($resolved === null) {
			throw $this->notFound();
		}

		[$entryOwner, $entry] = $resolved;
		$cacheContainer = match ($entry['lifetime']) {
			self::SHARED => $entryOwner,
			self::SCOPED => $this,
			self::TRANSIENT => null,
			default => $entryOwner,
		};

		if ($cacheContainer !== null && array_key_exists($id, $cacheContainer->instances)) {
			return $cacheContainer->instances[$id];
		}

		$result = $this->materialize($entry['definition']);

		if ($cacheContainer !== null) {
			$cacheContainer->instances[$id] = $result;
		}

		return $result;
	}

	public function definition(string $id): mixed
	{
		$resolved = $this->findEntry($id);

		if ($resolved === null) {
			throw $this->notFound();
		}

		return $resolved[1]['definition'];
	}

	/** @return null|array{0: self, 1: array{definition: mixed, lifetime: string}} */
	protected function findEntry(string $id): ?array
	{
		if (isset($this->entries[$id])) {
			return [$this, $this->entries[$id]];
		}

		return $this->parent?->findEntry($id);
	}

	protected function materialize(mixed $definition): mixed
	{
		if ($definition instanceof Closure) {
			return $definition();
		}

		if (is_string($definition)) {
			if ($this->has($definition)) {
				return $this->get($definition);
			}

			if (class_exists($definition)) {
				return new $definition();
			}
		}

		return $definition;
	}

	protected function root(): self
	{
		$root = $this;

		while ($root->parent !== null) {
			$root = $root->parent;
		}

		return $root;
	}

	protected function notFound(): NotFoundException
	{
		return new class extends Exception implements NotFoundException {};
	}
}
