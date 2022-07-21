<?php

declare(strict_types=1);

namespace MatrixCalculator;

use InvalidArgumentException;
use Stringable;

final class Matrix implements Stringable
{
    /** @var NumberRow[] */
    private array $rows = [];
    /** @var NumberRow[] */
    private array $columns = [];

    public function __construct(array $matrix) {
        $length = 0;

        foreach ($matrix as $row) {
            if (!is_array($row) || empty($row)) {
                throw new InvalidArgumentException();
            }
            if ($length && $length !== count($row)) {
                throw new InvalidArgumentException();
            }

            $index = 0;
            $wrappedRow = new NumberRow();

            foreach ($row as &$number) {
                if (!isset($this->columns[$index])) {
                    $this->columns[$index] = new NumberRow();
                }

                $wrappedRow->push($number);
                $this->columns[$index]->push($number);
                $index++;
            }

            $this->rows[] = $wrappedRow;
            $length = count($row);
        }
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
        $product = new self([]);

        foreach ($this->rows as $row) {
            $product->rows[] = $row->multiply($operand);
        }

        return $product;
    }

    private function multiplyBySelf(self $operand): self
    {
        if (count($this->columns) !== count($operand->rows)) {
            throw new InvalidArgumentException();
        }

        $product = [];

        foreach ($this->rows as $index => $row) {
            $product[$index] = [];

            foreach ($operand->columns as $column) {
                $product[$index][] = $row->multiply($column)->sum();
            }
        }

        return new self($product);
    }

    public function __toString(): string
    {
        ob_start();
        print_r($this->rows);

        return (string) ob_get_clean();
    }
}
