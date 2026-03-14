<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day10Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

    protected function executePart1(): void
    {
        $map = $this->data;

        $trailheads = [];
        foreach ($map as $ny => $line) {
            foreach ($line as $nx => $height) {
                if ($height == 0) {
                    $trailheads[] = [$ny, $nx];
                }
            }
        }

        $sum = 0;
        foreach ($trailheads as $trailhead) {
            $reachables = $this->getScore($trailhead, $map, []);
            foreach ($reachables as $reachable) {
                $sum += count($reachable);
            }
        }

        $this->result = (string)$sum;
    }

    protected function executePart2(): void
    {
        $map = $this->data;

        $trailheads = [];
        foreach ($map as $ny => $line) {
            foreach ($line as $nx => $height) {
                if ($height == 0) {
                    $trailheads[] = [$ny, $nx];
                }
            }
        }

        $sum = 0;
        foreach ($trailheads as $trailhead) {
            $score = $this->getScore2($trailhead, $map);
            $sum += $score;
        }

        $this->result = (string)$sum;
    }

    /*
     * Devuelve a cuantos 9 diferentes se llega desde ese punto.
     */
    private function getScore(array $coordinate, array $map, array $reachables): array
    {
        $initValue = $this->getCoordinateValue($coordinate, $map);
        if ($initValue == 9) {
            if (isset($reachables[$coordinate[0]][$coordinate[1]])) {
                $reachables[$coordinate[0]][$coordinate[1]] += 1;
            } else {
                $reachables[$coordinate[0]][$coordinate[1]] = 1;
            }
        }

        $toRight = [$coordinate[0], $coordinate[1] + 1];
        $toLeft = [$coordinate[0], $coordinate[1] - 1];
        $toUp = [$coordinate[0] - 1, $coordinate[1]];
        $toDown = [$coordinate[0] + 1, $coordinate[1]];

        if (
            (!$this->isOutOfMap($map, $toRight)) &&
            ($this->getCoordinateValue($toRight, $map) == $initValue + 1)
        ) {
            echo "(" . $toRight[0] . "," . $toRight[1] . ")->";
            $reachables = $this->getScore($toRight, $map, $reachables);
        }
        if (
            (!$this->isOutOfMap($map, $toLeft)) &&
            ($this->getCoordinateValue($toLeft, $map) == $initValue + 1)
        ) {
            echo "(" . $toLeft[0] . "," . $toLeft[1] . ")-<";
            $reachables = $this->getScore($toLeft, $map, $reachables);
        }
        if (
            (!$this->isOutOfMap($map, $toUp)) &&
            ($this->getCoordinateValue($toUp, $map) == $initValue + 1)
        ) {
            echo "(" . $toUp[0] . "," . $toUp[1] . ")-^";
            $reachables = $this->getScore($toUp, $map, $reachables);
        }
        if (
            (!$this->isOutOfMap($map, $toDown)) &&
            ($this->getCoordinateValue($toDown, $map) == $initValue + 1)
        ) {
            echo "(" . $toDown[0] . "," . $toDown[1] . ")-v";
            $reachables = $this->getScore($toDown, $map, $reachables);
        }

        return $reachables;
    }

    /*
     * Devuelve de cuantas formas diferentes se llega a un 9.
     */
    private function getScore2(array $coordinate, array $map): int
    {
        $score = 0;
        $initValue = $this->getCoordinateValue($coordinate, $map);
        if ($initValue == 9) {
            return 1;
        }

        $toRight = [$coordinate[0], $coordinate[1] + 1];
        $toLeft = [$coordinate[0], $coordinate[1] - 1];
        $toUp = [$coordinate[0] - 1, $coordinate[1]];
        $toDown = [$coordinate[0] + 1, $coordinate[1]];

        if (
            (!$this->isOutOfMap($map, $toRight)) &&
            ($this->getCoordinateValue($toRight, $map) == $initValue + 1)
        ) {
            echo "(" . $toRight[0] . "," . $toRight[1] . ")->";
            $score += $this->getScore2($toRight, $map);
        }
        if (
            (!$this->isOutOfMap($map, $toLeft)) &&
            ($this->getCoordinateValue($toLeft, $map) == $initValue + 1)
        ) {
            echo "(" . $toLeft[0] . "," . $toLeft[1] . ")-<";
            $score += $this->getScore2($toLeft, $map);
        }
        if (
            (!$this->isOutOfMap($map, $toUp)) &&
            ($this->getCoordinateValue($toUp, $map) == $initValue + 1)
        ) {
            echo "(" . $toUp[0] . "," . $toUp[1] . ")-^";
            $score += $this->getScore2($toUp, $map);
        }
        if (
            (!$this->isOutOfMap($map, $toDown)) &&
            ($this->getCoordinateValue($toDown, $map) == $initValue + 1)
        ) {
            echo "(" . $toDown[0] . "," . $toDown[1] . ")-v";
            $score += $this->getScore2($toDown, $map);
        }

        return $score;
    }

    private function getCoordinateValue(array $coordinate, array $map): string|int
    {
        return $map[$coordinate[0]][$coordinate[1]];
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
}
