<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;
use App\util\tableDataHelper;

class Year2024Day21Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

    protected function executePart1(): void
    {
        $data = $this->data;
        $startedAt = microtime(true);
        echo tableDataHelper::mapToString($data);
        $result = 0;

        $numericControl = [['7', '8', '9'], ['4', '5', '6'], ['1', '2', '3'], ['', '0', 'A']];
        $arrowControl = [['', '^', 'A'], ['<', 'v', '>']];

        foreach ($data as $nl => $line) {
            $sequence = $this->findSequence($line, $numericControl);
            echo tableDataHelper::dibujaSeguido($sequence, '', "\n");
            $sequence = $this->findSequence($sequence, $arrowControl);
            echo tableDataHelper::dibujaSeguido($sequence, '', "\n");
            $sequence = $this->findSequence($sequence, $arrowControl);
            $nPulsations = count($sequence);
            $sequenceNumber = $this->numericZone($line);
            echo tableDataHelper::dibujaSeguido($line, '', '');
            echo "......";
            echo $sequenceNumber;
            echo "......";
            echo $nPulsations;
            echo "......";
            echo tableDataHelper::dibujaSeguido($sequence, '', "\n");
            $result += $nPulsations * $sequenceNumber;
        }

        $this->result = (string)$result;
    }

    protected function executePart2(): void
    {
        $this->result = '?';
    }

    private function findSmallestSequence(array $line, array $control, int $times): array
    {
        if (count($line) > 2) {
            $first = array_shift($line);
            $posibleSequences = $this->findPaths($control, $first, $line[0]);
            foreach ($posibleSequences as $posibleSequence) {
                $seq = $this->findSmallestSequence($posibleSequence, $control, $times - 1);

                // req hasta cuando?????
            }
        }

        $sequence = [];
        $from = 'A';
        foreach ($line as $nd => $digit) {
            $to = $digit;
            $newSeq = $this->findPaths($control, $from, $to);
            $sequence = array_merge($sequence, $newSeq, ['A']);
            $from = $to;
        }
        return $sequence;
    }

    private function findPaths(array $numericControl, string $from, string $to): array
    {
        $coordinateFrom = $this->find($from, $numericControl);
        $coordinateTo = $this->find($to, $numericControl);
        $distY = $coordinateTo[0] - $coordinateFrom[0];
        $distX = $coordinateTo[1] - $coordinateFrom[1];
        $horizontalChar = '';
        $verticalChar = '';
        if ($distX > 0) {
            $horizontalChar = '>';
        } elseif ($distX < 0) {
            $horizontalChar = '<';
        }
        if ($distY > 0) {
            $verticalChar = 'v';
        } elseif ($distY < 0) {
            $verticalChar = '^';
        }

        $horiz = array_fill(0, abs($distX), $horizontalChar);
        $vert = array_fill(0, abs($distY), $verticalChar);

        return [
            [$horiz, $vert],
            [$vert, $horiz]
        ];
    }

    private function find(string $search, array $buttons): ?array
    {
        foreach ($buttons as $nl => $line) {
            foreach ($line as $nr => $digit) {
                if ($search === $digit) {
                    return [$nl, $nr];
                }
            }
        }
        return null;
    }

    private function numericZone(array $line): string
    {
        $result = '';
        foreach ($line as $nd => $digit) {
            if (($digit == 0) || ($digit > 0)) {
                $result .= $digit;
            }
        }
        return (int)$result;
    }
}
