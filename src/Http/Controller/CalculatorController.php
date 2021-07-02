<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;

use Jakmall\Recruitment\Calculator\Http\Controller\JakmallController;
use Illuminate\Http\Request;
use Jakmall\Recruitment\Calculator\Commands\AddCommand;
use Jakmall\Recruitment\Calculator\Commands\SubtractCommand;
use Jakmall\Recruitment\Calculator\Commands\DivideCommand;
use Jakmall\Recruitment\Calculator\Commands\MultiplyCommand;
use Jakmall\Recruitment\Calculator\Commands\PowerCommand;

class CalculatorController extends JakmallController
{
    public function calculate(Request $request, $action)
    {
        $class = null;
        switch ($action) {
            case 'add':
                $class = new AddCommand();
                break;
            case 'subtract':
                $class = new SubtractCommand();
                break;
            case 'divide':
                $class = new DivideCommand();
                break;
            case 'multiply':
                $class = new MultiplyCommand();
                break;
            case 'power':
                $class = new PowerCommand();
                break;
            case 'pow':
                $class = new PowerCommand();
                break;
        }
        $data=[];
        if(!is_null($class))
        {
            $input_decode= json_decode($request->input[0]);
            $data = $class->apiHandle($input_decode);
        }

        return $this->JsonOutput($data);
    }
}