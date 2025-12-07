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

    }
}
