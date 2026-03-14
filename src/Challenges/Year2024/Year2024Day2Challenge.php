<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day2Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_COLS;

    private const DIRECTION_NONE = 0;
    private const DIRECTION_INCREASING = 1;
    private const DIRECTION_DECREASING = 2;

    protected function executePart1(): void
    {
        $safeCont = 0;
        foreach ($this->data as $row) {
            if ($this->isSafe($row)) {
                $safeCont++;
            }
        }

        $this->result = (string)$safeCont;
    }

    protected function executePart2(): void
    {
        $safeCont = 0;
        foreach ($this->data as $row) {
            if ($this->isSafeAfterProblemDumper($row)) {
                $safeCont++;
            }
        }

        $this->result = (string)$safeCont;
    }

    private function isSafe(array $row): bool
    {
        $first = true;
        $direction = self::DIRECTION_NONE;
        foreach ($row as $value) {
            if ($first) {
                $first = false;
                $oldValue = $value;
            } else {
                if ($oldValue == $value) return false;
                if (abs($value - $oldValue) > 3) return false;
                if ($direction == self::DIRECTION_NONE) {
                    $direction = self::DIRECTION_INCREASING;
                    if ($value < $oldValue) $direction = self::DIRECTION_DECREASING;
                }
                if ($direction == self::DIRECTION_INCREASING) {
                    if ($value < $oldValue) return false;
                }
                if ($direction == self::DIRECTION_DECREASING) {
                    if ($value > $oldValue) return false;
                }
                $oldValue = $value;
            }
        }
        return true;
    }

    private function isSafeAfterProblemDumper(array $row): bool
    {
        foreach ($row as $key => $value) {
            if ($this->isSafe($row)) return true;
            $rowDumped = $row;
            unset($rowDumped[$key]);
            if ($this->isSafe($rowDumped)) return true;
        }
        return false;
    }
}
