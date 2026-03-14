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
    name: 'txurdi:2024:day10',
    description: 'Day 10 of the Advent code of 2024',
)]
class Day10Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="10";

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
        $map = $this->loadData();

        $trailheads = [];
        foreach ($map as $ny => $line) {
            foreach ($line as $nx => $height) {
                if ($height == 0) {
                    $trailheads[] = [$ny,$nx];
                }
            }
        }
//        var_dump($trailheads);

        $sum = 0;
        foreach ($trailheads as $trailhead) {
            $reachables = $this->getScore($trailhead, $map, []);
            foreach ($reachables as $reachable) {
                $sum += count($reachable);
            }
        }

        $this->io->success('RESULT: '.$sum);

        return 1;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $map = $this->loadData();

        $trailheads = [];
        foreach ($map as $ny => $line) {
            foreach ($line as $nx => $height) {
                if ($height == 0) {
                    $trailheads[] = [$ny,$nx];
                }
            }
        }
//        var_dump($trailheads);

        $sum = 0;
        foreach ($trailheads as $trailhead) {
            $score = $this->getScore2($trailhead, $map, []);
            $sum += $score;
        }

        $this->io->success('RESULT: '.$sum);

        return 1;
    }

    private function loadData(): array
    {
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

    /*
     * Devuelve a cuantos 9 diferentes se llega desde ese punto.
     */
    private function getScore(array $coordinate, array $map, array $reachables)
    {
        $initValue = $this->getCoordinateValue($coordinate, $map);
        if ($initValue == 9) {
            if (isset($reachables[$coordinate[0]][$coordinate[1]])) {
                $reachables[$coordinate[0]][$coordinate[1]] += 1;
            } else {
                $reachables[$coordinate[0]][$coordinate[1]] = 1;
            }
        }
//        echo "\nCOORD(".$coordinate[0].",".$coordinate[1].") ".$initValue." :\n";

        $toRight = [$coordinate[0],$coordinate[1]+1];
        $toLeft = [$coordinate[0],$coordinate[1]-1];
        $toUp = [$coordinate[0]-1,$coordinate[1]];
        $toDown = [$coordinate[0]+1,$coordinate[1]];

        if (
            (!$this->isOutOfMap($map, $toRight)) &&
            ($this->getCoordinateValue($toRight, $map) == $initValue+1)
        ) {
            echo "(".$toRight[0].",".$toRight[1].")->";
            $reachables = $this->getScore($toRight, $map, $reachables);
        }
        if (
            (!$this->isOutOfMap($map, $toLeft)) &&
            ($this->getCoordinateValue($toLeft, $map) == $initValue+1)
        ) {
            echo "(".$toLeft[0].",".$toLeft[1].")-<";
            $reachables = $this->getScore($toLeft, $map, $reachables);
        }
        if (
            (!$this->isOutOfMap($map, $toUp)) &&
            ($this->getCoordinateValue($toUp, $map) == $initValue+1)
        ) {
            echo "(".$toUp[0].",".$toUp[1].")-^";
            $reachables = $this->getScore($toUp, $map, $reachables);
        }
        if (
            (!$this->isOutOfMap($map, $toDown)) &&
            ($this->getCoordinateValue($toDown, $map) == $initValue+1)
        ) {
            echo "(".$toDown[0].",".$toDown[1].")-v";
            $reachables = $this->getScore($toDown, $map, $reachables);
        }

        return $reachables;
    }

    /*
 * Devuelve de cuantas formas diferentes se llega a un 9.
 */
    private function getScore2(array $coordinate, array $map) :int
    {
        $score = 0;
        $initValue = $this->getCoordinateValue($coordinate, $map);
        if ($initValue == 9) {
            return 1;
        }
//        echo "\nCOORD(".$coordinate[0].",".$coordinate[1].") ".$initValue." :\n";

        $toRight = [$coordinate[0],$coordinate[1]+1];
        $toLeft = [$coordinate[0],$coordinate[1]-1];
        $toUp = [$coordinate[0]-1,$coordinate[1]];
        $toDown = [$coordinate[0]+1,$coordinate[1]];

        if (
            (!$this->isOutOfMap($map, $toRight)) &&
            ($this->getCoordinateValue($toRight, $map) == $initValue+1)
        ) {
            echo "(".$toRight[0].",".$toRight[1].")->";
            $score += $this->getScore2($toRight, $map);
        }
        if (
            (!$this->isOutOfMap($map, $toLeft)) &&
            ($this->getCoordinateValue($toLeft, $map) == $initValue+1)
        ) {
            echo "(".$toLeft[0].",".$toLeft[1].")-<";
            $score += $this->getScore2($toLeft, $map);
        }
        if (
            (!$this->isOutOfMap($map, $toUp)) &&
            ($this->getCoordinateValue($toUp, $map) == $initValue+1)
        ) {
            echo "(".$toUp[0].",".$toUp[1].")-^";
            $score += $this->getScore2($toUp, $map);
        }
        if (
            (!$this->isOutOfMap($map, $toDown)) &&
            ($this->getCoordinateValue($toDown, $map) == $initValue+1)
        ) {
            echo "(".$toDown[0].",".$toDown[1].")-v";
            $score += $this->getScore2($toDown, $map);
        }

        return $score;
    }

    private function getCoordinateValue(array $coordinate, array $map) : string|int
    {
        return $map[$coordinate[0]][$coordinate[1]];
    }

    private function isOutOfMap(array $map, array $coordinate):bool
    {
        if ( ($coordinate[0] < 0)
            || ($coordinate[1] < 0)
            || ($coordinate[0] >= count($map[0]))
            || ($coordinate[1] >= count($map))
        ) {
            return true;
        }
        return false;
    }
}
