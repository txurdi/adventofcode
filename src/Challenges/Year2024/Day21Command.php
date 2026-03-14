<?php

namespace App\Command\y2024;

use App\Util\SolveHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'txurdi:2024:day21',
    description: 'Day 21 of the Advent code of 2024',
)]
class Day21Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="21";
    private ?string $test=null;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('half', InputArgument::REQUIRED, 'First (1) or second (2) half of the puzzle')
            ->addOption('test', 't', InputOption::VALUE_OPTIONAL, 'Test')
        ;
    }
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $half = $input->getArgument('half');
        $this->test = $input->getOption('test');

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
        $startedAt = microtime(true);
        SolveHelper::dibujaMapa($data);
        $result = 0;

        $numericControl = [ ['7','8','9'],['4','5','6'],['1','2','3'],['','0','A'] ];
        $arrowControl = [['','^','A'],['<','v','>']];

        foreach ($data as $nl=>$line) {
            $sequence = $this->findSequence($line, $numericControl);
            SolveHelper::dibujaSeguido($sequence,'',"\n",true);
            $sequence = $this->findSequence($sequence, $arrowControl);
            SolveHelper::dibujaSeguido($sequence,'',"\n",true);
            $sequence = $this->findSequence($sequence, $arrowControl);
            $nPulsations = count($sequence);
            $sequenceNumber = $this->numericZone($line);
            SolveHelper::dibujaSeguido($line,'','',true);
            echo "......";
            echo $sequenceNumber;
            echo "......";
            echo $nPulsations;
            echo "......";
            SolveHelper::dibujaSeguido($sequence,'',"\n",true);
            $result += $nPulsations * $sequenceNumber;
        }

//        $numericPaths = $this->findPaths($numericControl, '9','8');
//
//        $from1a = $numericPaths[0][0];
//        foreach ($numericPaths[0] as $to1) {
//            $arrow1Paths = $this->findPaths($arrowControl, $from1a,$to1);
//            $from2a = $arrow1Paths[0][0];
//            foreach ($arrow1Paths[0] as $to2a) {
//                $arrow2aPaths = $this->findPaths($arrowControl, $from2a,$to2a);
//            }
//            $from2b = $arrow1Paths[1][0];
//            foreach ($arrow1Paths[1] as $to2b) {
//                $arrow2bPaths = $this->findPaths($arrowControl, $from2b,$to2b);
//            }
//        }

//        foreach ($numericPaths[1]....



        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->loadData();
        $startedAt = microtime(true);

        $result = '?';

        $finishedAt = microtime(true);
        $this->io->warning('Tiempo: ' . ($finishedAt - $startedAt));
        $this->io->success('RESULT: '.$result);

        return 1;
    }

    private function loadData(): array
    {
        if (!isset($this->test)) {
            $filePath = __DIR__ . "/../../Data/2024/day" . self::DAY . ".txr";
        } else {
            $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test".$this->test.".txr";
        }
        $this->io->warning('Loading file: '.$filePath);
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

//    private function findSequence(array $line, array $control, string $start, int $nrobots) : array
//    {
//        if ($nrobots = 1) {
//            $sequence = [];
//            $from = $start;
//            $newSeq = $this->findPaths($control, $from, $to);
//            $sequence = array_merge($sequence, $newSeq, ['A']);
//            $from = $to;
//        }
//        return $sequence;
//        $start = array_shift($line);
//        $sequence = $this->findSequence($line, $control, $start, $nrobots-1);
//    } else {
//
//    }
//    }
    private function findSmallestSequence(array $line, array $control, int $times) : array
    {
        if (count($line) > 2) {
            $first =  array_shift($line);
            $posibleSequences = $this->findPaths($control, $first, $line[0]);
            foreach ($posibleSequences as $posibleSequence) {
                $seq = $this->findSmallestSequence($posibleSequence, $control, $times-1);

                // req hasta cuando?????
            }
        }



        $sequence = [];
        $from = 'A';
        foreach ($line as $nd=>$digit) {
            $to = $digit;
            $newSeq = $this->findPaths($control, $from, $to);
            $sequence = array_merge($sequence, $newSeq, ['A']);
            $from = $to;
        }
        return $sequence;
    }

    private function findPaths(array $numericControl, string $from, string $to) : array
    {
        $coordinateFrom = $this->find($from, $numericControl);
        $coordinateTo = $this->find($to, $numericControl);
        $distY = $coordinateTo[0] - $coordinateFrom[0];
        $distX = $coordinateTo[1] - $coordinateFrom[1];
        $horizontalChar = '';
        $verticalChar = '';
        if ($distX > 0) {
            $horizontalChar = '>';
        } elseif ($distX < 0) {
            $horizontalChar = '<';
        }
        if ($distY > 0) {
            $verticalChar = 'v';
        } elseif ($distY < 0) {
            $verticalChar = '^';
        }

        $horiz = array_fill(0,abs($distX), $horizontalChar);
        $vert = array_fill(0,abs($distY), $verticalChar);

        return  [
          [$horiz, $vert],
          [$vert, $horiz]
        ];


        return $path;
    }

    private function find(string $search, array $buttons) :?array
    {
//        var_dump("FIND",$search);
        foreach ($buttons as $nl=> $line) {
            foreach ($line as $nr=>$digit) {
                if ($search === $digit) {
//                    var_dump("OK",$nl,$nr);
                    return [$nl,$nr];
                }
            }
        }
        return null;
    }

    private function numericZone(array $line) :string
    {
        $result = '';
        foreach ($line as $nd=>$digit) {
            if (($digit == 0) || ($digit > 0)) {
                $result .= $digit;
            }
        }
        return (int)$result;
    }

}
