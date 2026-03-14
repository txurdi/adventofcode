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
use function Symfony\Component\Translation\t;

#[AsCommand(
    name: 'txurdi:2024:day15',
    description: 'Day 15 of the Advent code of 2024',
)]
class Day15Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="15";
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
        $mapZone = true;
        $map = [];
        $instructionLines = [];
        foreach ($data as $nl => $line) {
            $lineAsArray = str_split(trim($line));
            if (count($lineAsArray) == 0) {
                $mapZone = false;
                continue;
            }
            if ($mapZone) {
                $map[$nl-1] = $lineAsArray;
                foreach ($lineAsArray as $nr => $char) {
                    if ($char == '@') {
                        $position = [$nl-1, $nr];
                        var_dump($position);
                    }
                }
            } else {
                $instructionLines[$nl] = $lineAsArray;
            }
        }

        foreach ($instructionLines as $nl => $instructionLine) {
            foreach ($instructionLine as $nr => $direction) {
//                var_dump($direction);
                SolveHelper::dibujaMapa($map);
                $map = $this->move($map, $position, $direction);
                $this->io->ask("Sigue: $direction");
            }
        }


        $result = '?';
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

        return SolveHelper::fileToArrayByLine($filePath);
    }

    private const DIRECTION_UP = '^';
    private const DIRECTION_DOWN = 'v';
    private const DIRECTION_LEFT = '<';
    private const DIRECTION_RIGHT = '>';
    private function move(array $map, array $position, string $direction) :array
    {
        // Si el pimer sitio al que puedo mover es un punto, muevo.
        // Si es un 0 sigo buscando a ver si hay un punto. Si encuentro punto muevo todos hasta el punto y me muevo a donde estaba el primer 0.
        // Si es un # no hago nada.
        echo ("\np[$position[0],$position[1]=>".$map[$position[0]][$position[1]]."]\n ");
        $xInitial =  $position[0];
        $yInitial =  $position[1];
        $x1 = $xInitial;
        $y1 = $yInitial;
//        var_dump($x1,$y1);
        while (($map[$x1][$y1] != '.') && ($map[$x1][$y1] != '#')) {
            echo ("w[$x1,$y1=>".$map[$x1][$y1]."] ");
            switch ($direction) {
                case self::DIRECTION_UP:
                    $x1 -= 1;
                    break;
                case self::DIRECTION_DOWN:
                    $x1 += 1;
                    break;
                case self::DIRECTION_LEFT:
                    $y1 -= 1;
                    break;
                case self::DIRECTION_RIGHT:
                    $y1 += 1;
                    break;
            }
        }
        if ($map[$x1][$y1] == '#') {
            return $map;
        }
        echo ("\n*[$x1,$y1=>".$map[$x1][$y1]."] ");
        if ($map[$x1][$y1] == '.') {
            while (($x1!=$xInitial) || ($y1!=$yInitial)) {
                $x2 = $x1;
                $y2 = $y1;
                switch ($direction) {
                    case self::DIRECTION_UP:
                        $x2 += 1;
                        break;
                    case self::DIRECTION_DOWN:
                        $x2 -= 1;
                        break;
                    case self::DIRECTION_LEFT:
                        $y2 += 1;
                        break;
                    case self::DIRECTION_RIGHT:
                        $y2 -= 1;
                        break;
                }
                echo ("W1donde[$x1,$y1=>".$map[$x1][$y1]."] ");
                echo ("W2qué[$x2,$y2=>".$map[$x2][$y2]."] ");
                $map[$x1][$y1] = $map[$x2][$y2];
                $position[0] = $x1;
                $position[1] = $y1;
                $x1 = $x2;
                $y1 = $y2;
            }
            $map[$position[0]][$position[1]] = '.';
//            echo ("\n1[$x1,$y1=>".$map[$x1][$y1]."] ");
//            echo ("\n2[$x2,$y2=>".$map[$x2][$y2]."] ");
//            echo ("\np[$position[0],$position[1]=>".$map[$position[0]][$position[1]]."] ");
        }
        return $map;
    }

}
