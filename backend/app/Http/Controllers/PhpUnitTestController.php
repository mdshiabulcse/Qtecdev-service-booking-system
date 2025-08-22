<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Services\Contracts\CalculatorInterface;


class PhpUnitTestController extends Controller
{
    protected $calculator;


    public function __construct(CalculatorInterface $calculator) {
        $this->calculator = $calculator;
    }


    public function add(Request $request) {
        $a = $request->input('a', 10);
        $b = $request->input('b', 20);
        return response()->json(['result' => $this->calculator->add($a, $b)]);
    }


    public function subtract(Request $request) {
        $a = $request->input('a', 20);
        $b = $request->input('b', 10);
        return response()->json(['result' => $this->calculator->subtract($a, $b)]);
    }
}
