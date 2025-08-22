<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CalculatorService;

class CalculatorServiceTest extends TestCase
{
    protected $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CalculatorService();
    }

    public function test_add_function_returns_correct_sum()
    {
        $result = $this->calculator->add(5, 7);
        $this->assertEquals(12, $result);
    }

    public function test_subtract_function_returns_correct_result()
    {
        $result = $this->calculator->subtract(10, 4);
        $this->assertEquals(6, $result);
    }
}
