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
    name: 'txurdi:2024:day01',
    description: 'Day 1 of the Advent code of 2024',
)]
class Day01Command extends Command
{
    private SymfonyStyle $io;

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

        $this->io->warning('Day 1. GOOOOOOOO');

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
        $data = $this->loadData();
        $col1 = SolveHelper::getTableCol($data, 0);
        $col2 = SolveHelper::getTableCol($data, 1);
        sort($col1);
        sort($col2);

//        var_dump($col1);
//        var_dump($col2);
//        exit();
        $cont = count($col1);
        $this->io->note("Processing ".$cont." rows.");
        $this->io->progressStart($cont);
        $sum = 0;
        foreach ($col1 as $key => $val1) {
            $sum += abs($col1[$key]-$col2[$key]);
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();

        $this->io->success('RESULT: '.$sum);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $col1 = SolveHelper::getTableCol($data, 0);
        $col2 = SolveHelper::getTableCol($data, 1);

        $cont = count($col1);
        $this->io->note("Processing ".$cont." rows.");
        $this->io->progressStart($cont);
        $sum = 0;
        foreach ($col1 as $val1) {
            $sum += ($val1 * SolveHelper::getNumberRepeated($col2, $val1));
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();

        $this->io->success('RESULT: '.$sum);

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day01.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day01_test.txr";

        return SolveHelper::fileToArrayByLineAndCol($filePath);
    }
}
