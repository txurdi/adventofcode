<?php

namespace App\Challenges;

use App\util\fileDataHelper;

class Year2025Day1Challenge extends YearDayChallenge
{
    public function __construct(
        private readonly int $year,
        private readonly int $day,
        private readonly string $projectDir,
        private readonly bool $debug
    )
    {
        parent::__construct($year, $day, $projectDir, $debug);
    }

    public function execute(string $half, ?string $test='1'): string
    {
        $this->format = fileDataHelper::DATA_FORMAT_LINES;
        return parent::execute($half, $test);
    }

    protected function executePart1(): void
    {
        $numberOfZeros = 0;
        $position = 50;
        foreach ($this->data as $line) {
            $direction = substr($line, 0, 1);
            $distance = substr($line, 1);
            if ($direction == 'L') {
                $distance *= -1;
            }
            $position += $distance;
            $position %= 100;
            if ($position < 0) {
                $position += 100;
            } else if ($position == 0) {
                $numberOfZeros ++;
            }
        }
        $this->result = $numberOfZeros;
    }

    protected function executePart2(): void
    {
        $pointToZero = 0;
        $position = 50;
        foreach ($this->data as $line) {
            $direction = substr($line, 0, 1);
            $distance = (int)substr($line, 1);
            $extraRotations = (int)($distance / 100);
            $pointToZero += abs($extraRotations);
            $realDistance = $distance % 100;
            if ($direction == 'L') {
                $realDistance *= -1;
            }
            [$extraPointToZero, $newPosition] = $this->pointsToZero($position, $realDistance);
            $pointToZero += $extraPointToZero;
            echo "{$position} {$direction} {$distance} +{$extraRotations} {$realDistance} {$extraPointToZero} {$newPosition} {$pointToZero} \n";
            $position = $newPosition;
        }
        $this->result = $pointToZero;
    }
    // 7222
    // 6790
    // 7742
    // 6671


    private function pointsToZero(int $position, int $distance): array
    {
        $pointsToZero = 0;
        $newPosition = $position + $distance;
        if ($newPosition >= 100) {
            $newPosition -= 100;
            $pointsToZero++;
        } else if ($newPosition < 0) {
            $newPosition += 100;
            $pointsToZero++;
        } else if ($newPosition == 0) {
            if ($position != 0) {
                $pointsToZero++;
            }
        }
        return [$pointsToZero, $newPosition];
    }

//    private function pointsToZero(int $position, int $distance): array
//    {
//        $pointsToZero = 0;
//        $newPosition = $position + $distance;
//        if ($newPosition == 0) {
//            $pointsToZero ++;
//        } elseif ($newPosition < 0) {
////                if (abs($position) != abs($distance))
//            $pointsToZero++;
//            $newPosition += 100;
//        } elseif ($newPosition >= 100) {
//            if (abs($newPosition) != abs($distance)) $pointsToZero++;
//            $newPosition -= 100;
//        }
//
//        return [$pointsToZero, $newPosition];
//    }
}
