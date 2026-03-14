<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day7Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_LINES;

    protected function executePart1(): void
    {
        $result = 0;

        foreach ($this->data as $equation) {
            list($testValue, $numbersString) = explode(':', $equation);
            $numbers = explode(' ', trim($numbersString));
            $results = $this->calculateEquation($numbers);
            if (in_array($testValue, $results)) {
                $result += (int)$testValue;
            }
        }

        $this->result = (string)$result;
    }

    protected function executePart2(): void
    {
        $this->result = '?';
    }

    private function calculateEquation(array $numbers): array
    {
        if (count($numbers) == 2) {
            return [
                $numbers[0] + $numbers[1],
                $numbers[0] * $numbers[1],
                (int)($numbers[0] . $numbers[1]),
            ];
        } else {
            $results = [];
            $lastNumber = $numbers[count($numbers) - 1];
            array_pop($numbers);
            $posibleResults = $this->calculateEquation($numbers);
            foreach ($posibleResults as $posibleResult) {
                $results[] = $posibleResult * $lastNumber;
                $results[] = $posibleResult + $lastNumber;
                $results[] = (int)($posibleResult . $lastNumber);
            }
            return $results;
        }
    }
}
