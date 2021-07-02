<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class HistoryClearCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = "history:clear";
    /**
     * @var string
     */
    protected $description = "Clear history";

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
        $this->setName('history:clear');
        $this->addArgument('commands');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handle();
    }

    public function handle(): void
    {
        $commands = $this->getCommand();

        $manage = new CommandHistoryServiceProvider();
        if(!empty($commands))
        {
            $action=$manage->clear($commands);
            if($action == false)
            {
                $this->comment(sprintf('Data %s not found', $commands));
            }else{
                $this->comment(sprintf('Data with ID %s is removed', $commands));
            }
        }else{
            $manage->clearAll();
            $this->comment('All history is cleared');
        }
    }

    protected function getCommand()
    {
        return $this->argument('commands');
    }

}