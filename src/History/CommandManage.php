<?php

namespace Jakmall\Recruitment\Calculator\History;

use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

use Exception;

class CommandManage implements CommandHistoryManagerInterface
{
    public $driver;

    public $ResourceFile = [
        'file'      => 'calculator',
        'latest'    => 'latest',
        'composite' => 'composite'
    ];

    public $path_folder = 'storage/logs';

    public function __construct()
    {
        $this->create_log_file();
    }

    public function findAll($filter = null): array
    {
        $output = [];
        $findID = '';
        if (!empty($filter)) {
            $findID = $filter[0];
        }
        $file = strtr($this->driver, $this->ResourceFile);
        $log_file_path = __DIR__ .'../../'.$this->path_folder . '/' . $file . '.log';
        $f = fopen($log_file_path, 'r');
        if ($f === false) {
            throw new Exception("File not found");
        }
        $read = fread($f, filesize($log_file_path));
        if (empty($read)) {
            return array();
        }
        $explode_new_line = explode("\n", $read);

        $data = $explode_new_line;
        if ($findID > 0) {
            $data = [];
            $data[] = $explode_new_line[$findID - 1];
        }

        $count = 0;
        foreach ($data as $line) {
            $explode_segment = explode(":", $line);
            $id_unique = !empty($explode_segment[0]) ? $explode_segment[0] : "";
            if ($id_unique != "") {
                if($this->driver == 'latest' && $count == 10)
                {
                    break;
                }
                $output[] = [
                    'id' => $id_unique,
                    'time' => date('Y-m-d H:i:s', $explode_segment[1]),
                    'command' => $explode_segment[2],
                    'operation' => $explode_segment[3],
                    'result' => $explode_segment[4]
                ];
                $count ++;
            }
        }

        fclose($f);


        return $output;
    }

    public function find($findID)
    {
        return $this->OpenResource($findID);
    }

    public function log($data): bool
    {
        $current_time = strtotime(date('Y-m-d H:i:s'));
        $new_id = uniqid();
        $txt_append = $new_id . ':' . $current_time . ':' . $data['command'] .
            ':' . $data['operation'] . ':' . $data['result'].':'.$data['input'];
        //1:timeint:add:1+2+3:6\n
        foreach ($this->ResourceFile as $key => $value) {
            $this->log_single($key, $txt_append);
        }

        return true;
    }

    private function log_single($driver, $txt_append)
    {
        $file = strtr($driver, $this->ResourceFile);
        $log_file_path = __DIR__ . '../../'.$this->path_folder . '/' . $file . '.log';

        file_put_contents($log_file_path, $txt_append . "\n", FILE_APPEND);
        if ($driver == "latest") {
            $current_info = $this->get_current_info($driver);
            $count_line = $current_info['count_data'] ? $current_info['count_data'] : 0;
            $count = $count_line - 1;
            if ($count >= 10) {
                $first_info = $this->get_first_info($driver);
                $this->RemoveByLineDriver($driver, $first_info[0]);
            }
        }
    }

    public function clearAll(): bool
    {
        return $this->RemoveAll();
    }

    public function clear($findID): bool
    {
        foreach($this->ResourceFile as $res)
        {
            $this->RemoveByLineDriver($res, $findID);
        }
        return true;
    }

    private function RemoveAll()
    {
        $file= glob($this->path_folder.'/*');
        if(count($file))
        {
            foreach($file as $f)
            {
                unlink($f);
            }
        }
        return true;
    }

    private function RemoveByLineDriver($driver, $findID)
    {
        $output=[];
        $file = strtr($driver, $this->ResourceFile);
        $log_file_path = __DIR__ . '../../'.$this->path_folder . '/' . $file . '.log';
        $f = fopen($log_file_path, 'r');
        if ($f === false) {
            throw new Exception("File not found");
        }
        $file_size = filesize($log_file_path) ? filesize($log_file_path) : 1024;
        $data = fread($f, $file_size);
        if (empty($data)) {
            return false;
        }
        $explode = explode("\n", $data);
        $checking=false;
        foreach ($explode as $line) {
            $explode_segment = explode(":", $line);
            if(!isset($explode_segment[0]))
            {
                $id = $explode_segment[0];
                if ($findID != $id) {
                    $output[] = [
                        'id' => $explode_segment[0],
                        'time' => $explode_segment[1],
                        'command' => $explode_segment[2],
                        'operation' => $explode_segment[3],
                        'result' => $explode_segment[4]
                    ];
                }else{
                    $checking =true;
                }
            }
        }

        fclose($f);

        if($checking == false)
        {
            return true;
        }

        $file = strtr($driver, $this->ResourceFile);
        $log_file_path = __DIR__ . '../../'.$this->path_folder . '/' . $file . '.log';
        file_put_contents($log_file_path,"");

        foreach ($output as $row) {
            if(!empty($row['id']))
            {
                $txt_append = $row['id'] . ':' . $row['time'] . ':' . $row['command'] . ':' . $row['operation'] . ':' . $row['result'];
                file_put_contents($log_file_path, $txt_append . "\n", FILE_APPEND);
            }
        }


        return true;
    }


    protected function OpenResource($driver, $findID = "", $limit = 0)
    {
        $output = [];
        $file = strtr($driver, $this->ResourceFile);
        $path_file = __DIR__ . '../../'.$this->path_folder.'/' . $file;
        $f = fopen($path_file, 'r');
        if ($f === false) {
            throw new Exception("File not found");
        }
        $read = fread($f, filesize($path_file));
        if (empty($read)) {
            return array();
        }
        $explode_new_line = explode("\n", $read);

        $count = 0;
        foreach ($explode_new_line as $line) {
            $count += 1;
            $explode_segment = explode(":", $line);
            $id = $explode_segment[0];
            $output[] = [
                'id' => $id,
                'time' => date('Y-m-d H:i:s', $explode_segment[1]),
                'command' => $explode_segment[2],
                'operation' => $explode_segment[3],
                'result' => $explode_segment[4]
            ];
            if (!empty($findID) && ($id == $findID)) {
                break;
            }

            if ($limit > 0 && ($count == 12)) {
                break;
            }
        }

        fclose($f);

        return $output;
    }

    protected function get_current_info($driver_selected)
    {
        $file = strtr($driver_selected, $this->ResourceFile);
        $log_file_path = __DIR__ . '../../'.$this->path_folder . '/' . $file . '.log';
        $f = fopen($log_file_path, 'r');
        if ($f === false) {
            throw new Exception("File not found");
        }
        $file_size = filesize($log_file_path) ? filesize($log_file_path) : 1024;
        $read = fread($f, $file_size);
        if (empty($read)) {
            return 0;
        }
        $explode = explode("\n", $read);
        $current_line = $explode[count($explode) - 1];
        $explode_segment = explode(":", $current_line);
        return array(
            'last_id' => (int) $explode_segment[0],
            'count_data' => (int) count($explode)
        );
    }

    private function get_first_info($driver)
    {
        $file = strtr($driver, $this->ResourceFile);
        $log_file_path = $this->path_folder . '/' . $file . '.log';
        $f = fopen($log_file_path, 'r');
        if ($f === false) {
            throw new Exception("File not found");
        }
        $read = fread($f, filesize($log_file_path));
        if (empty($read)) {
            return array();
        }
        $explode_new_line = explode("\n", $read);
        $first_data=$explode_new_line[0];
        $explode_data=explode(":",$first_data);
        return $explode_data;
    }

    private function create_log_file()
    {
        $path_folder = __DIR__ . '/../../'.$this->path_folder;
        if (!file_exists($path_folder)) {
            mkdir($path_folder, 0777, true);
        }
    }
}