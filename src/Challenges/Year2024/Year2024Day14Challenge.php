<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day14Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_COLS;

    private int $nCols = 101;
    private int $nLines = 103;

    private const SECTOR_UP_LEFT = 1;
    private const SECTOR_UP_RIGHT = 2;
    private const SECTOR_DOWN_LEFT = 3;
    private const SECTOR_DOWN_RIGHT = 4;

    protected function executePart1(): void
    {
        if ($this->test != '1') {
            $this->nCols = 11;
            $this->nLines = 7;
        } else {
            $this->nCols = 101;
            $this->nLines = 103;
        }

        $data = $this->data;
        $points = [];
        foreach ($data as $line) {
            $point = [
                'x' => (int)substr($line[0], 2),
                'y' => (int)$line[1],
                'vx' => (int)substr($line[2], 2),
                'vy' => (int)$line[3],
            ];
            $points[] = $point;
        }
        $startedAt = microtime(true);

        $newPoints = [];
        $times = 100;
        foreach ($points as $point) {
            $newPoint = $this->calculaPosicion($point['x'], $point['y'], $point['vx'], $point['vy'], $times);
            $newPoints[] = $newPoint;
        }

        $pointsPerSector = $this->sumaPuntosSectorPorSector($newPoints);

        $result = 1;
        foreach ($pointsPerSector as $points) {
            $result *= $points;
        }

        $this->result = (string)$result;
    }

    protected function executePart2(): void
    {
        $this->result = '?';
    }

    private function dibujaCoordenadas($coordenadas): void
    {
        echo "MAPA:\n";
        $map = [];
        for ($y = 0; $y < $this->nLines; $y++) {
            for ($x = 0; $x < $this->nCols; $x++) {
                $map[$x][$y] = '0';
            }
        }
        foreach ($coordenadas as $coordenada) {
            $map[$coordenada['x']][$coordenada['y']] += 1;
        }
        for ($y = 0; $y < 50; $y++) {
            for ($x = 0; $x < $this->nCols; $x++) {
                if ($map[$x][$y] > 0) {
                    echo $map[$x][$y];
                } else {
                    echo '.';
                }
            }
            echo "\n";
        }
    }

    private function calculaPosicion(mixed $x, mixed $y, mixed $vx, mixed $vy, $times): array
    {
        $newX = (($times * ($vx + $this->nCols)) + $x) % $this->nCols;
        $newY = (($times * ($vy + $this->nLines)) + $y) % $this->nLines;

        return [
            'x' => $newX,
            'y' => $newY
        ];
    }

    private function sumaPuntosSectorPorSector($points): array
    {
        $xCenter = intdiv($this->nCols, 2);
        $yCenter = intdiv($this->nLines, 2);
        $pointsPerSector = [
            self::SECTOR_UP_LEFT => 0,
            self::SECTOR_UP_RIGHT => 0,
            self::SECTOR_DOWN_LEFT => 0,
            self::SECTOR_DOWN_RIGHT => 0,
        ];
        foreach ($points as $point) {
            if (($point['x'] < $xCenter) && ($point['y'] < $yCenter)) {
                $pointsPerSector[self::SECTOR_UP_LEFT] += 1;
            }
            if (($point['x'] > $xCenter) && ($point['y'] < $yCenter)) {
                $pointsPerSector[self::SECTOR_UP_RIGHT] += 1;
            }
            if (($point['x'] < $xCenter) && ($point['y'] > $yCenter)) {
                $pointsPerSector[self::SECTOR_DOWN_LEFT] += 1;
            }
            if (($point['x'] > $xCenter) && ($point['y'] > $yCenter)) {
                $pointsPerSector[self::SECTOR_DOWN_RIGHT] += 1;
            }
        }
        return $pointsPerSector;
    }
}
