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
    name: 'txurdi:2024:day08',
    description: 'Day 08 of the Advent code of 2024',
)]
class Day08Command extends Command
{
    private SymfonyStyle $io;
    private const DAY="08";

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
        $antinodes = $map;

        $antennas = [];
        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($cell != '.') {
                    $antennas[$cell][] = [$nr, $nc];
                }
            }
        }

        foreach ($antennas as $type => $coordinates) {
            foreach ($coordinates as $i=>$coordinate1) {
                foreach ($coordinates as $j=>$coordinate2) {
                    if ($i==$j) continue;
                    $posibleAntinodes = $this->createAntionodes($coordinate1, $coordinate2);
//                    exit();
                    foreach ($posibleAntinodes as $posibleAntinode) {
                        if (!$this->isOutOfMap($map, $posibleAntinode)) {
                            $antinodes[$posibleAntinode[0]][$posibleAntinode[1]] = '#';
                        }
                    }
                }
            }
        }

        $nAntinode = 0;
        foreach ($antinodes as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($cell == '#') {
                    $nAntinode++;
                }
            }
        }

        SolveHelper::dibujaMapa($map);
        echo "--\n";
        SolveHelper::dibujaMapa($antinodes);

        $this->io->success('RESULT: '.$nAntinode);

        return 1;
    }

    private function isOutOfMap(array $map, array $coordinate):bool
    {
//        var_dump($coordinate);
        if ( ($coordinate[0] < 0)
            || ($coordinate[1] < 0)
            || ($coordinate[0] >= count($map[0]))
            || ($coordinate[1] >= count($map))
        ) {
            return true;
        }
        return false;
    }

    private function executeHalf2(InputInterface $input, OutputInterface $output): int
    {
        $map = $this->loadData();
        $antinodes = $map;

        $antennas = [];
        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($cell != '.') {
                    $antennas[$cell][] = [$nr, $nc];
                }
            }
        }

        foreach ($antennas as $type => $coordinates) {
            foreach ($coordinates as $i=>$coordinate1) {
                foreach ($coordinates as $j=>$coordinate2) {
                    if ($i==$j) continue;
                    $posibleAntinodes = $this->createAntionodesWithResonantHarmonics($coordinate1, $coordinate2,$map);
//                    exit();
                    foreach ($posibleAntinodes as $posibleAntinode) {
                        if (!$this->isOutOfMap($map, $posibleAntinode)) {
                            $antinodes[$posibleAntinode[0]][$posibleAntinode[1]] = '#';
                        }
                    }
                }
            }
        }

        $nAntinode = 0;
        foreach ($antinodes as $nr => $row) {
            foreach ($row as $nc => $cell) {
                if ($cell == '#') {
                    $nAntinode++;
                }
            }
        }

        SolveHelper::dibujaMapa($map);
        echo "--\n";
        SolveHelper::dibujaMapa($antinodes);

        $this->io->success('RESULT: '.$nAntinode);

        return 1;
    }

    private function loadData(): array
    {
        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY.".txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test.txr";
//        $filePath = __DIR__ . "/../../Data/2024/day".self::DAY."_test2.txr";

        return SolveHelper::fileToArrayByLineAndChar($filePath);
    }

    private function calculateDistance(array $coordinate1, array $coordinate2) : array
    {
        $x = $coordinate2[0] - $coordinate1[0];
        $y = $coordinate2[1] - $coordinate1[1];
        return [$x, $y];
    }

    private function createAntionodes(array $coordinate1, array $coordinate2) : array
    {
        $distance = $this->calculateDistance($coordinate1, $coordinate2);
        $x1 = $coordinate1[0] - $distance[0];
        $x2 = $coordinate2[0] + $distance[0];
        $y1 = $coordinate1[1] - $distance[1];
        $y2 = $coordinate2[1] + $distance[1];
        return [[$x1, $y1],[$x2, $y2]];
    }

    private function createAntionodesWithResonantHarmonics(array $coordinate1, array $coordinate2, array $map) : array
    {
        $antinodes = [];
        $distance = $this->calculateDistance($coordinate1, $coordinate2);

//        $x1 = $coordinate1[0] - $distance[0];
//        $y1 = $coordinate1[1] - $distance[1];
        $posibleAntinode1 = $coordinate1;
        while (!$this->isOutOfMap($map, $posibleAntinode1)) {
            $antinodes[] = $posibleAntinode1;
            $x1 = $posibleAntinode1[0] - $distance[0];
            $y1 = $posibleAntinode1[1] - $distance[1];
            $posibleAntinode1 = [$x1, $y1];
        }

//        $x2 = $coordinate2[0] + $distance[0];
//        $y2 = $coordinate2[1] + $distance[1];
        $posibleAntinode2 = $coordinate2;
        while (!$this->isOutOfMap($map, $posibleAntinode2)) {
            $antinodes[] = $posibleAntinode2;
            $x2 = $posibleAntinode2[0] + $distance[0];
            $y2 = $posibleAntinode2[1] + $distance[1];
            $posibleAntinode2 = [$x2, $y2];
        }
        return $antinodes;
    }

}
