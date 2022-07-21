<?php

declare(strict_types=1);

require_once __DIR__ . '/src/NumberRow.php';
require_once __DIR__ . '/src/Matrix.php';

use MatrixCalculator\Matrix;

echo new Matrix([]) . PHP_EOL;
echo new Matrix([[1]]) . PHP_EOL;
echo (new Matrix([[3]]))->multiply(new Matrix([[2]])) . PHP_EOL;
echo (new Matrix([[1, 3]]))->multiply(new Matrix([[2], [8]])) . PHP_EOL;
