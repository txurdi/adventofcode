<?php

namespace App\Command\y2024;

use App\Util\SolveHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'txurdi:2024:day07',
    description: 'Day 7 of the Advent code of 2024',
)]
class Day07Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="07";

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('half', InputArgument::REQUIRED, 'First (1) or second (2) half of the puzzle')
        ;
    }
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $half = $input->getArgument('half');

        $this->io->warning('Day '.self::DAY.'..... GOOOOOOOO!!!');

        switch ($half) {
            case 1:
                if ($this->executeHalf1($input, $output)) {
                    return Command::SUCCESS;
                }
            case 2:
                if ($this->executeHalf2($input, $output)) {
                    return Command::SUCCESS;
                }

        }

        return Command::FAILURE;
    }

    private function executeHalf1(InputInterface $input, OutputInterface $output): int
    {
        $equations = $this->loadData();
        $result = 0;

        foreach ($equations as $equation) {
            list($testValue, $numbersString) = explode(':', $equation);
            $numbers = explode(' ', trim($numbersString));
            $results = $this->calculateEquation($numbers);
            if (in_array($testValue, $results)) {
                $result += (int)$testValue;
            }
//            var_dump($testValue, $numbers);
        }

        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();

        $result = '?';

        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLine($filePath);
    }

    private function calculateEquation(array $numbers) : array
    {

        if (count($numbers) == 2) {
            return  [
                $numbers[0] + $numbers[1],
                $numbers[0] * $numbers[1],
                (int)($numbers[0] . $numbers[1]),
            ];
        } else {
            $results = [];
            $lastNumber = $numbers[count($numbers) - 1];
            array_pop($numbers);
            $posibleResults = $this->calculateEquation($numbers);
            foreach ($posibleResults as $posibleResult) {
                $results[] = $posibleResult * $lastNumber;
                $results[] = $posibleResult + $lastNumber;
                $results[] = (int)($posibleResult . $lastNumber);
            }
            return $results;
        }
    }

}
