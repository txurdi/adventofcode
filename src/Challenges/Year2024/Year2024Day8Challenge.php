<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;
use App\util\tableDataHelper;

class Year2024Day8Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

    protected function executePart1(): void
    {
        $map = $this->data;
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
            foreach ($coordinates as $i => $coordinate1) {
                foreach ($coordinates as $j => $coordinate2) {
                    if ($i == $j) continue;
                    $posibleAntinodes = $this->createAntionodes($coordinate1, $coordinate2);
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

        echo tableDataHelper::mapToString($map);
        echo "--\n";
        echo tableDataHelper::mapToString($antinodes);

        $this->result = (string)$nAntinode;
    }

    protected function executePart2(): void
    {
        $map = $this->data;
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
            foreach ($coordinates as $i => $coordinate1) {
                foreach ($coordinates as $j => $coordinate2) {
                    if ($i == $j) continue;
                    $posibleAntinodes = $this->createAntionodesWithResonantHarmonics($coordinate1, $coordinate2, $map);
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

        echo tableDataHelper::mapToString($map);
        echo "--\n";
        echo tableDataHelper::mapToString($antinodes);

        $this->result = (string)$nAntinode;
    }

    private function isOutOfMap(array $map, array $coordinate): bool
    {
        if (
            ($coordinate[0] < 0)
            || ($coordinate[1] < 0)
            || ($coordinate[0] >= count($map[0]))
            || ($coordinate[1] >= count($map))
        ) {
            return true;
        }
        return false;
    }

    private function calculateDistance(array $coordinate1, array $coordinate2): array
    {
        $x = $coordinate2[0] - $coordinate1[0];
        $y = $coordinate2[1] - $coordinate1[1];
        return [$x, $y];
    }

    private function createAntionodes(array $coordinate1, array $coordinate2): array
    {
        $distance = $this->calculateDistance($coordinate1, $coordinate2);
        $x1 = $coordinate1[0] - $distance[0];
        $x2 = $coordinate2[0] + $distance[0];
        $y1 = $coordinate1[1] - $distance[1];
        $y2 = $coordinate2[1] + $distance[1];
        return [[$x1, $y1], [$x2, $y2]];
    }

    private function createAntionodesWithResonantHarmonics(array $coordinate1, array $coordinate2, array $map): array
    {
        $antinodes = [];
        $distance = $this->calculateDistance($coordinate1, $coordinate2);

        $posibleAntinode1 = $coordinate1;
        while (!$this->isOutOfMap($map, $posibleAntinode1)) {
            $antinodes[] = $posibleAntinode1;
            $x1 = $posibleAntinode1[0] - $distance[0];
            $y1 = $posibleAntinode1[1] - $distance[1];
            $posibleAntinode1 = [$x1, $y1];
        }

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
