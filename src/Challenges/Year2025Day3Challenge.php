<?php

namespace App\Challenges;

use App\util\fileDataHelper;

class Year2025Day3Challenge extends YearDayChallenge
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
        $format = fileDataHelper::DATA_FORMAT_CHARS;
        return parent::execute($half, $test, $format);
    }

    protected function executePart1(): void
    {
        $totalOutput = 0;
        foreach ($this->data as $line) {
            if (empty($line)) continue;
            $totalOutput += $this->getLargestJoltage($line);
        }
        $this->result = $totalOutput;
    }

    protected function executePart2(): void
    {
        $bankSize = 12;
        $totalOutput = 0;
        foreach ($this->data as $line) {
            if (empty($line)) continue;
            $bank = [];
            for ($cont = 0; $cont < $bankSize; $cont++) {
                [$pos, $val] = $this->getLargestBattery($line, $bankSize, $cont);
                $bank[$pos] = $val;
            }
            $voltage = 0;
            foreach ($bank as $pos=>$val) {
                $voltage += $val*(pow(10,($bankSize-$pos-1)));
            }
            $totalOutput += $voltage;
        }
        $this->result = $totalOutput;
    }


    private function getLargestBattery(array $line, int $joltageTam, int $batteryPositionInJoltage): array
    {

        $position = $batteryPositionInJoltage;
        $value = $line[$position];
        for ($i = $position + 1; $i <= count($line) - $joltageTam; $i++) {
            if ($line[$i] > $value) {
                $position = $i;
                $value = $line[$position];
            }
        }
        return [$position, $value];

    }

    private function getLargestJoltage(array $line): int
    {
        $firstPosition = 0;
        $first = $line[$firstPosition];
        for ($i = 1; $i < count($line) - 1; $i++) {
            if ($line[$i] > $first) {
                $firstPosition = $i;
                $first = $line[$firstPosition];
            }
        }
        $secondPosition = $firstPosition+1;
        $second = $line[$secondPosition];
        for ($j = $secondPosition; $j < count($line); $j++) {
            if ($line[$j] > $second) {
                $secondPosition = $j;
                $second = $line[$secondPosition];
            }
        }
        return ($first*10) + $second;
    }
}
