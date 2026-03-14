<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;
use App\util\tableDataHelper;

class Year2024Day1Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_COLS;

    protected function executePart1(): void
    {
        $col1 = tableDataHelper::getTableCol($this->data, 0);
        $col2 = tableDataHelper::getTableCol($this->data, 1);
        sort($col1);
        sort($col2);

        $sum = 0;
        foreach ($col1 as $key => $val1) {
            $sum += abs($col1[$key] - $col2[$key]);
        }

        $this->result = (string)$sum;
    }

    protected function executePart2(): void
    {
        $col1 = tableDataHelper::getTableCol($this->data, 0);
        $col2 = tableDataHelper::getTableCol($this->data, 1);

        $sum = 0;
        foreach ($col1 as $val1) {
            $sum += ($val1 * tableDataHelper::getNumberRepeated($col2, $val1));
        }

        $this->result = (string)$sum;
    }
}
