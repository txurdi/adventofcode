<?php

namespace App\Challenges;

use App\util\fileDataHelper;

class Year2025DayXChallenge extends YearDayChallenge
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

        $this->result = 0;
    }

    protected function executePart2(): void
    {

        $this->result = 0;
    }
}
