<?php

namespace App\Challenges;

use App\util\fileDataHelper;

class Year2025Day2Challenge extends YearDayChallenge
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

    public function execute(string $half, ?string $test='1', ?string $format='1'): string
    {
        return parent::execute($half, $test, fileDataHelper::DATA_FORMAT_STRING);
    }

    protected function executePart1(): void
    {
        parent::
        $result = trim($this->dataStr);

        var_dump($result);exit();
//        $result[$numLine] = preg_split("/[\s,]+/", (trim($linea)));

        $this->result = 0;
    }

    protected function executePart2(): void
    {

        $this->result = 0;
    }
}
