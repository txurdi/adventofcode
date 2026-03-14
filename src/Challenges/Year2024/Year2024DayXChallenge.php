<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024DayXChallenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_STRING;

    protected function executePart1(): void
    {
        $this->result = '?';
    }

    protected function executePart2(): void
    {
        $this->result = '?';
    }
}
