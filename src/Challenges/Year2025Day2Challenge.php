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

    public function execute(string $half, ?string $test='1', ?string $format=fileDataHelper::DATA_FORMAT_STRING): string
    {
        return parent::execute($half, $test, fileDataHelper::DATA_FORMAT_STRING);
    }

    protected function executePart1(): void
    {
        $result = trim($this->dataStr);
        $ranges = explode(",", $result);

        $allInvalidIds = [];
        foreach ($ranges as $range) {
            [$from, $to] = explode("-", $range);
            $invalidIds = $this->getInvalidIds($from, $to);
            $allInvalidIds = array_merge($allInvalidIds, $invalidIds);
        }

        $this->result = array_sum($allInvalidIds);
    }

    protected function executePart2(): void
    {

        $this->result = 0;
    }

    private function getInvalidIds(string $from, string $to)
    {
        $invalidIds = [];
        for ($i = (int)$from; $i <= (int)$to; $i++) {
            $numCar = strlen((string)$i);
            if ($numCar % 2 == 1) continue;
            $mitad = $numCar / 2;
            $inicio = substr($i, 0, $mitad);
            $final = substr($i, $mitad);
            if ($inicio == $final) {
                $invalidIds[] = $i;
            }
        }

        return $invalidIds;
    }
}
