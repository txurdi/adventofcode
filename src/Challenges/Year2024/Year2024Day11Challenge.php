<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;
use App\util\tableDataHelper;

class Year2024Day11Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_STRING;

    private mixed $blinking = [];

    public function blink(array $stones): array
    {
        $newStones = [];
        foreach ($stones as $stone) {
            $blinked = $this->applyRules($stone);
            $newStones[] = $blinked[0];
            if (isset($blinked[1])) {
                $newStones[] = $blinked[1];
            }
        }
        return $newStones;
    }

    protected function executePart1(): void
    {
        $data = $this->dataStr;
        $stones = preg_split("/[\s,]+/", (trim($data)));
        echo tableDataHelper::dibujaSeguido($stones, '-');
        $startedAt = microtime(true);

        for ($i = 1; $i <= 25; $i++) {
            $newStones = [];
            foreach ($stones as $stone) {
                $blinked = $this->applyRules($stone);
                $newStones[] = $blinked[0];
                if (isset($blinked[1])) {
                    $newStones[] = $blinked[1];
                }
            }
            $stones = $newStones;
        }

        $finishedAt = microtime(true);
        $result = count($newStones);

        $this->result = (string)$result;
    }

    private function blinkRecursive(array $stones): array
    {
        $left = $this->applyRules($stones[0]);
        array_shift($stones);
        if (count($stones) > 1) {
            $right = $this->blinkRecursive($stones);
        } else {
            $right = $this->applyRules($stones[0]);
        }
        array_unshift($right, $left[0]);
        if (count($left) > 1) {
            array_unshift($right, $left[1]);
        }
        return $right;
    }

    protected function executePart2(): void
    {
        $data = $this->dataStr;
        $stones = preg_split("/[\s,]+/", (trim($data)));
        echo tableDataHelper::dibujaSeguido($stones, '-');
        $startedAt = microtime(true);

        $times = 75;
        $sum = 0;
        foreach ($stones as $stone) {
            $sum += $this->blinkTimes($stone, $times);
        }

        $finishedAt = microtime(true);

        $this->result = (string)$sum;
    }

    private function applyRules(int|string $number): array
    {
        echo "NUMBER: " . $number . " => ";
        if ($number == 0) {
            $result = [1];
        } else {
            $numberStr = strval($number);
            $cont = strlen($numberStr);
            if ($cont % 2 == 0) {
                $left = substr($numberStr, 0, $cont / 2);
                $right = substr($numberStr, $cont / 2);
                $result = [(int)$left, (int)$right];
                echo "$result[0]-$result[1]\n";
            } else {
                $result = [$number * 2024];
                echo "$result[0]\n";
            }
        }
        return $result;
    }

    private function blinkTimes(int $stone, int $times): int
    {
        if (isset($this->blinking[$stone][$times])) return $this->blinking[$stone][$times];
        $stones = $this->applyRules($stone);
        if ($times > 1) {
            $suma = $this->blinkTimes($stones[0], $times - 1);
            if (count($stones) > 1) {
                $suma += $this->blinkTimes($stones[1], $times - 1);
            }
        } else {
            return count($stones);
        }
        $this->blinking[$stone][$times] = $suma;
        return $suma;
    }
}
