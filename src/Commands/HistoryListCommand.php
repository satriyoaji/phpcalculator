<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class HistoryListCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = "history:list {--driver=latest}";
    /**
     * @var string
     */
    protected $description = "Show history";

    /**
     * @var object
     */
    protected $commandHistory;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('history:list');
        $this->addArgument('commands');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handle();
    }

    public function handle(): void
    {
        $driver = $this->input->getOption('driver')? $this->input->getOption('driver'): 'latest';
        $commands = $this->getCommand();

        $manage = new CommandHistoryServiceProvider();
        $manage->driver= $driver;
        if(empty($driver))
        {
            $this->comment('Driver not set --driver=xxxx');
        }
        $data = $manage->findAll($commands);

        if (!empty($data)) {
            $this->createTable($data);
        } else {
            $this->comment('History is empty.');
        }
    }

    protected function getCommand()
    {
        return $this->argument('commands');
    }

    private function createTable($data)
    {
        $tableContent = [];
        foreach ($data as $key => $row) {
            $tableContent[] = [
                'id' => $row['id'],
                'command' => ucfirst($row['command']),
                'operation' => $row['operation'],
                'result' => $row['result']
            ];
        }
        $headers = ['ID', 'Command','Operation', 'Result'];
        $this->table($headers, $tableContent);
    }
}