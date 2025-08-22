<?php

namespace App\Http\Controllers;

use App\Services\CalculatorService;
use Illuminate\Http\Request;


class PhpUnitTestController extends Controller
{
    protected $calculator;
    public function __construct(CalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }
    public function add(Request $request)
    {
        $a=$request->input('a');
        $b=$request->input('b');
        $result=$this->calculator->add($a,$b);
        return response()->json($result);
    }
    public function subtract(Request $request){
        $a=$request->input('a');
        $b=$request->input('b');
        $result=$this->calculator->subtract($a,$b);
        return response()->json($result);
    }
}
