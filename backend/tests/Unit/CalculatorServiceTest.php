<?php
namespace Tests\Unit;


use PHPUnit\Framework\TestCase;
use App\Services\CalculatorService;


class CalculatorServiceTest extends TestCase
{
    protected $calculator;


    protected function setUp(): void {
        parent::setUp();
        $this->calculator = new CalculatorService();
    }


    public function test_add_function_returns_correct_sum() {
        $this->assertEquals(12, $this->calculator->add(5, 7));
    }


    public function test_subtract_function_returns_correct_result() {
        $this->assertEquals(6, $this->calculator->subtract(10, 4));
    }
}
