<?php
namespace App\Services;


use App\Services\Contracts\CalculatorInterface;


class CalculatorService implements CalculatorInterface
{
    public function add($a, $b) {
        return $a + $b;
    }


    public function subtract($a, $b) {
        return $a - $b;
    }
}
