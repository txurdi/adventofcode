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
    name: 'txurdi:2024:day04',
    description: 'Day 4 of the Advent code of 2024',
)]
class Day04Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="04";
    private array $directions;

    public function __construct()
    {
        $this->directions = [self::DIR_UP, self::DIR_DOWN, self::DIR_LEFT, self::DIR_RIGHT, self::DIR_DIAG_LEFT_UP, self::DIR_DIAG_LEFT_DOWN, self::DIR_DIAG_RIGHT_UP, self::DIR_DIAG_RIGHT_DOWN, ];
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
        $data = $this->loadData();
        $result = [];
        $toFind = 'XMAS';
        foreach ($data as $nl=>$line) {
            foreach ($line as $nr=>$character) {
                foreach ($this->directions as $direction) {
                    if ($this->findInWordSearch($toFind, $data, $nl, $nr, $direction)) {
                        $result[] = [$nl, $nr, $direction];
                    }
                }
            }
        }

        $this->io->success('RESULT: '.count($result));

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $result = [];
        $toFind = 'XMAS';
        foreach ($data as $nl=>$line) {
            foreach ($line as $nr=>$character) {
                if ($character ==  'A') {
                    if ($this->findXmasCross($data, $nl, $nr)) {
                        $result[] = [$nl, $nr];
                    }
                }
            }
        }

        $this->io->success('RESULT: '.count($result));

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

    private const DIR_UP = 1;
    private const DIR_DOWN = 2;
    private const DIR_LEFT = 3;
    private const DIR_RIGHT = 4;
    private const DIR_DIAG_LEFT_UP = 5;
    private const DIR_DIAG_LEFT_DOWN = 6;
    private const DIR_DIAG_RIGHT_UP = 7;
    private const DIR_DIAG_RIGHT_DOWN = 8;


    private function findInWordSearch(string $word, array $data, int $nl, int $nr, int $direction) :int
    {
//        $this->io->text($word.'-'.$nl.'-'.$nr.'#'.$direction.'#'.$data[$nl][$nr]);
        if (!isset($data[$nl][$nr])) return 0;
        if (strlen($word) == 1) {
            if ($data[$nl][$nr] == $word) return 1;
        }
        $char = substr($word, 0, 1);
        if ( $data[$nl][$nr] != $char ) return 0;
        $word = substr($word, 1);
        switch ($direction) {
            case self::DIR_UP:
                $nl--;
                break;
            case self::DIR_DOWN:
                $nl++;
                break;
            case self::DIR_LEFT:
                $nr--;
                break;
            case self::DIR_RIGHT:
                $nr++;
                break;
            case self::DIR_DIAG_LEFT_UP:
                $nl--;
                $nr--;
                break;
            case self::DIR_DIAG_LEFT_DOWN:
                $nl++;
                $nr--;
                break;
            case self::DIR_DIAG_RIGHT_UP:
                $nl--;
                $nr++;
                break;
            case self::DIR_DIAG_RIGHT_DOWN:
                $nl++;
                $nr++;
                break;
            default:
                return 0;
        }
        return $this->findInWordSearch($word, $data, $nl, $nr, $direction);
    }

    private function findXmasCross(array $data, int $nl, int $nr) :bool
    {
        if (!isset($data[$nl-1][$nr-1])) return false;
        if (!isset($data[$nl+1][$nr-1])) return false;
        if (!isset($data[$nl-1][$nr+1])) return false;
        if (!isset($data[$nl+1][$nr+1])) return false;
        $word1 = $data[$nl-1][$nr-1].$data[$nl+1][$nr+1];
        $word2 = $data[$nl+1][$nr-1].$data[$nl-1][$nr+1];
        if (
            (($word1 == 'MS') || ($word1 == 'SM')) &&
            (($word2 == 'MS') || ($word2 == 'SM'))
        ) {
            return true;
        }
        return false;
    }

}
