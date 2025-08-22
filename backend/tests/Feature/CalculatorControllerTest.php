<?php
namespace Tests\Feature;


use Tests\TestCase;


class CalculatorControllerTest extends TestCase
{
    public function test_add_route_returns_correct_result() {
        $response = $this->get('/add?a=15&b=25');
        $response->assertStatus(200);
        $response->assertJson(['result' => 40]);
    }


    public function test_subtract_route_returns_correct_result() {
        $response = $this->get('/subtract?a=50&b=20');
        $response->assertStatus(200);
        $response->assertJson(['result' => 30]);
    }
}
