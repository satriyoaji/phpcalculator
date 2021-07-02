<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Jakmall\Recruitment\Calculator\History\CommandHistoryServiceProvider;

class PowerCommand extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;

    public $logme;

    public function __construct(CommandHistoryManagerInterface $logger)
    {
        parent::__construct();
        $commandVerb = $this->getCommandVerb();

        $this->signature = sprintf(
            '%s {numbers* : The numbers to be %s}',
            $commandVerb,
            $this->getCommandPassiveVerb()
        );
        $this->description = sprintf('%s all given Numbers', ucfirst($commandVerb));
    }

    protected function configure()
    {
        $this->setName('power');
        $this->addArgument('numbers', InputArgument::IS_ARRAY | InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handle();
    }

    protected function getCommandVerb(): string
    {
        return 'power';
    }

    protected function getCommandPassiveVerb(): string
    {
        return 'power';
    }

    public function handle(): void
    {
        $numbers = $this->getInput();
        $description = $this->generateCalculationDescription($numbers);
        $result = $this->calculateAll($numbers);
        $this->logme->log([
            'command' => $this->getCommandVerb(),
            'operation' => $description,
            'result' => $result
        ]);
        $this->comment(sprintf('%s = %s', $description, $result));
    }

    protected function getInput(): array
    {
        return $this->argument('numbers');
    }

    protected function generateCalculationDescription(array $numbers): string
    {
        $operator = $this->getOperator();
        $glue = sprintf(' %s ', $operator);

        return implode($glue, $numbers);
    }

    protected function getOperator(): string
    {
        return '^';
    }

    /**
     * @param array $numbers
     *
     * @return float|int
     */
    protected function calculateAll(array $numbers)
    {
        $number = array_pop($numbers);

        if (count($numbers) <= 0) {
            return $number;
        }

        return $this->calculate($this->calculateAll($numbers), $number);
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        return pow($number1,$number2);
    }
}
