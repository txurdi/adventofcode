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
    name: 'txurdi:2024:day02',
    description: 'Day 2 of the Advent code of 2024',
)]
class Day02Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="02";

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

        $this->io->warning('Day '.self::DAY.'..... GOOOOOOOO');

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

        $nRow = count($data);
        $this->io->note("Processing ".$nRow." rows.");
        $this->io->progressStart($nRow);
        $safeCont = 0;
        foreach ($data as $row) {
            if ($this->isSafe($row)) {
                $safeCont++;
            }
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();

        $this->io->success('RESULT: '.$safeCont);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {

        $data = $this->loadData();

        $nRow = count($data);
        $this->io->note("Processing ".$nRow." rows.");
        $this->io->progressStart($nRow);
        $safeCont = 0;
        foreach ($data as $row) {
            if ($this->isSafeAfterProblemDumper($row)) {
                $safeCont++;
            }
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();

        $this->io->success('RESULT: '.$safeCont);

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";

        return SolveHelper::fileToArrayByLineAndCol($filePath);
    }

    private const DIRECTION_NONE = 0;
    private const DIRECTION_INCREASING = 1;
    private const DIRECTION_DECREASING = 2;

    private function isSafe(array $row):bool
    {
        $first = true;
        $direction = self::DIRECTION_NONE;
        foreach ($row as $value) {
            if ($first) {
                $first = false;
                $oldValue = $value;
            } else {
                if ($oldValue == $value) return false;
                if (abs($value - $oldValue) > 3) return false;
                if ($direction == self::DIRECTION_NONE) {
                    $direction = self::DIRECTION_INCREASING;
                    if ($value < $oldValue) $direction = self::DIRECTION_DECREASING;
                }
                if ($direction == self::DIRECTION_INCREASING) {
                    if ($value < $oldValue) return false;
                }
                if ($direction == self::DIRECTION_DECREASING) {
                    if ($value > $oldValue) return false;
                }
                $oldValue = $value;
            }
        }
        return true;
    }

    private function isSafeAfterProblemDumper(array $row):bool
    {
        foreach ($row as $key=>$value) {
            if ($this->isSafe($row)) return true;
            $rowDumped = $row;
            unset($rowDumped[$key]);
            if ($this->isSafe($rowDumped)) return true;
        }
        return false;
    }
}
