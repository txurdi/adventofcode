<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day4Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

    private const DIR_UP = 1;
    private const DIR_DOWN = 2;
    private const DIR_LEFT = 3;
    private const DIR_RIGHT = 4;
    private const DIR_DIAG_LEFT_UP = 5;
    private const DIR_DIAG_LEFT_DOWN = 6;
    private const DIR_DIAG_RIGHT_UP = 7;
    private const DIR_DIAG_RIGHT_DOWN = 8;

    private array $directions = [self::DIR_UP, self::DIR_DOWN, self::DIR_LEFT, self::DIR_RIGHT, self::DIR_DIAG_LEFT_UP, self::DIR_DIAG_LEFT_DOWN, self::DIR_DIAG_RIGHT_UP, self::DIR_DIAG_RIGHT_DOWN];

    protected function executePart1(): void
    {
        $data = $this->data;
        $result = [];
        $toFind = 'XMAS';
        foreach ($data as $nl => $line) {
            foreach ($line as $nr => $character) {
                foreach ($this->directions as $direction) {
                    if ($this->findInWordSearch($toFind, $data, $nl, $nr, $direction)) {
                        $result[] = [$nl, $nr, $direction];
                    }
                }
            }
        }

        $this->result = (string)count($result);
    }

    protected function executePart2(): void
    {
        $data = $this->data;
        $result = [];
        foreach ($data as $nl => $line) {
            foreach ($line as $nr => $character) {
                if ($character == 'A') {
                    if ($this->findXmasCross($data, $nl, $nr)) {
                        $result[] = [$nl, $nr];
                    }
                }
            }
        }

        $this->result = (string)count($result);
    }

    private function findInWordSearch(string $word, array $data, int $nl, int $nr, int $direction): int
    {
        if (!isset($data[$nl][$nr])) return 0;
        if (strlen($word) == 1) {
            if ($data[$nl][$nr] == $word) return 1;
        }
        $char = substr($word, 0, 1);
        if ($data[$nl][$nr] != $char) return 0;
        $word = substr($word, 1);
        switch ($direction) {
            case self::DIR_UP:
                $nl--;
                break;
            case self::DIR_DOWN:
                $nl++;
                break;
            case self::DIR_LEFT:
                $nr--;
                break;
            case self::DIR_RIGHT:
                $nr++;
                break;
            case self::DIR_DIAG_LEFT_UP:
                $nl--;
                $nr--;
                break;
            case self::DIR_DIAG_LEFT_DOWN:
                $nl++;
                $nr--;
                break;
            case self::DIR_DIAG_RIGHT_UP:
                $nl--;
                $nr++;
                break;
            case self::DIR_DIAG_RIGHT_DOWN:
                $nl++;
                $nr++;
                break;
            default:
                return 0;
        }
        return $this->findInWordSearch($word, $data, $nl, $nr, $direction);
    }

    private function findXmasCross(array $data, int $nl, int $nr): bool
    {
        if (!isset($data[$nl-1][$nr-1])) return false;
        if (!isset($data[$nl+1][$nr-1])) return false;
        if (!isset($data[$nl-1][$nr+1])) return false;
        if (!isset($data[$nl+1][$nr+1])) return false;
        $word1 = $data[$nl-1][$nr-1] . $data[$nl+1][$nr+1];
        $word2 = $data[$nl+1][$nr-1] . $data[$nl-1][$nr+1];
        if (
            (($word1 == 'MS') || ($word1 == 'SM')) &&
            (($word2 == 'MS') || ($word2 == 'SM'))
        ) {
            return true;
        }
        return false;
    }
}
