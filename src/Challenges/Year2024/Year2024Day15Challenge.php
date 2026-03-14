<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;
use App\util\tableDataHelper;

class Year2024Day15Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_LINES;

    private const DIRECTION_UP = '^';
    private const DIRECTION_DOWN = 'v';
    private const DIRECTION_LEFT = '<';
    private const DIRECTION_RIGHT = '>';

    protected function executePart1(): void
    {
        $data = $this->data;
        $startedAt = microtime(true);
        $mapZone = true;
        $map = [];
        $instructionLines = [];
        foreach ($data as $nl => $line) {
            $lineAsArray = str_split(trim($line));
            if (count($lineAsArray) == 0) {
                $mapZone = false;
                continue;
            }
            if ($mapZone) {
                $map[$nl - 1] = $lineAsArray;
                foreach ($lineAsArray as $nr => $char) {
                    if ($char == '@') {
                        $position = [$nl - 1, $nr];
                        var_dump($position);
                    }
                }
            } else {
                $instructionLines[$nl] = $lineAsArray;
            }
        }

        foreach ($instructionLines as $nl => $instructionLine) {
            foreach ($instructionLine as $nr => $direction) {
                echo tableDataHelper::mapToString($map);
                $map = $this->move($map, $position, $direction);
            }
        }

        $this->result = '?';
    }

    protected function executePart2(): void
    {
        $this->result = '?';
    }

    private function move(array $map, array $position, string $direction): array
    {
        echo ("\np[$position[0],$position[1]=>" . $map[$position[0]][$position[1]] . "]\n ");
        $xInitial = $position[0];
        $yInitial = $position[1];
        $x1 = $xInitial;
        $y1 = $yInitial;
        while (($map[$x1][$y1] != '.') && ($map[$x1][$y1] != '#')) {
            echo ("w[$x1,$y1=>" . $map[$x1][$y1] . "] ");
            switch ($direction) {
                case self::DIRECTION_UP:
                    $x1 -= 1;
                    break;
                case self::DIRECTION_DOWN:
                    $x1 += 1;
                    break;
                case self::DIRECTION_LEFT:
                    $y1 -= 1;
                    break;
                case self::DIRECTION_RIGHT:
                    $y1 += 1;
                    break;
            }
        }
        if ($map[$x1][$y1] == '#') {
            return $map;
        }
        echo ("\n*[$x1,$y1=>" . $map[$x1][$y1] . "] ");
        if ($map[$x1][$y1] == '.') {
            while (($x1 != $xInitial) || ($y1 != $yInitial)) {
                $x2 = $x1;
                $y2 = $y1;
                switch ($direction) {
                    case self::DIRECTION_UP:
                        $x2 += 1;
                        break;
                    case self::DIRECTION_DOWN:
                        $x2 -= 1;
                        break;
                    case self::DIRECTION_LEFT:
                        $y2 += 1;
                        break;
                    case self::DIRECTION_RIGHT:
                        $y2 -= 1;
                        break;
                }
                echo ("W1donde[$x1,$y1=>" . $map[$x1][$y1] . "] ");
                echo ("W2qué[$x2,$y2=>" . $map[$x2][$y2] . "] ");
                $map[$x1][$y1] = $map[$x2][$y2];
                $position[0] = $x1;
                $position[1] = $y1;
                $x1 = $x2;
                $y1 = $y2;
            }
            $map[$position[0]][$position[1]] = '.';
        }
        return $map;
    }
}
