<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;

use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;
use Jakmall\Recruitment\Calculator\Http\Controller\JakmallController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HistoryController extends JakmallController
{
    public function index(Request $request)
    {
        $command = new CommandHistoryServiceProvider();
        $command->driver = $request->driver? $request->driver:'latest';
        $data=$command->findAll();
        $count = count($data);

        $new_data=[];
        if($count)
        {
            $operator=["+","-","/","^","*"];
            $precedence=0;
            foreach($data as $row)
            {
                $precedence+=1;
                $input="[".str_replace($operator,",",$row['operation'])."]";
                $new_data[]=[
                    'id'=> $row['id'],
                    'command'=>$row['command'],
                    'operation'=>$row['operation'],
                    'input'=> $input,
                    'result'=>$row['result']
                ];
            }
        }
        $output = [
            'driver'=> $request->driver,
            'count' => (int) $count,
            'data' => $new_data
        ];
        return $this->JsonOutput($output);
    }

    public function show($id)
    {
        $id_array = [];
        $id_array[] = $id;
        $command = new CommandHistoryServiceProvider();
        $command->driver = 'composite';
        $data = $command->findAll($id_array);
        $count = count($data);

        $new_data = [];
        if ($count) {
            $operator = ["+", "-", "/", "^", "*"];
            $precedence = 0;
            foreach ($data as $row) {
                $precedence += 1;
                $input = "[" . str_replace($operator, ",", $row['operation']) . "]";
                $new_data[] = [
                    'command' => $row['command'],
                    'operation' => $row['operation'],
                    'input' => $input,
                    'result' => $row['result']
                ];
            }
        }
        $output = [
            'count' => (int) $count,
            'data' => $new_data
        ];
        return $this->JsonOutput($output);
    }

    public function remove($id)
    {
        $id_array = [];
        $id_array[] = $id;
        $command = new CommandManage();
        $command->clear($id_array);

        return $this->JsonOutput([
            'message'=>'Succes'
        ]);
    }
}