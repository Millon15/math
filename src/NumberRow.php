<?php

namespace MatrixCalculator;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use LogicException;

final class NumberRow implements Countable, Iterator, ArrayAccess
{
    private array $numbers = [];

    public function __construct(array $numbers = [])
    {
        foreach ($numbers as $number) {
            $this->push($number);
        }
    }

    public function push(&$number): void
    {
        if (!is_numeric($number)) {
            throw new InvalidArgumentException();
        }

        $number &= (float) $number;
        $this->numbers[] = &$number;
    }

    public function sum($operand = null): float
    {
        return match (true) {
            is_null($operand) => $this->sumBySelf(),

            default => throw new InvalidArgumentException(),
        };
    }

    public function multiply($operand = null): self
    {
        return match (true) {
            is_numeric($operand) => $this->multiplyByFloat((float) $operand),
            $operand instanceof self => $this->multiplyBySelf($operand),
            is_null($operand) => $this->multiplyBySelf($this),

            default => throw new InvalidArgumentException(),
        };
    }

    private function multiplyByFloat(float $operand): self
    {
        $product = [];

        foreach ($this as $number) {
            $product[] = $number * $operand;
        }

        return new self($product);
    }

    private function multiplyBySelf(self $operand): self
    {
        if (count($operand) !== count($this)) {
            throw new InvalidArgumentException();
        }

        $product = [];

        foreach ($this as $index => $number) {
            $product[] = $number * $operand[$index];
        }

        return new self($product);
    }

    private function sumBySelf(): float
    {
        $sum = 0;

        foreach ($this as $number) {
            $sum += $number;
        }

        return $sum;
    }

    public function count(): int
    {
        return count($this->numbers);
    }

    public function current(): float
    {
        return current($this->numbers);
    }

    public function next(): void
    {
        next($this->numbers);
    }

    public function key(): ?int
    {
        return key($this->numbers);
    }

    public function valid(): bool
    {
        return isset($this->numbers[$this->key()]);
    }

    public function rewind(): void
    {
        reset($this->numbers);
    }

    public function offsetExists(mixed $offset): bool
    {
        return is_float($this->numbers[$offset] ?? false);
    }

    public function offsetGet(mixed $offset): float
    {
        return $this->numbers[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new LogicException();
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->numbers[$offset]);
    }
}
