<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;
use App\util\tableDataHelper;

class Year2024Day25Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

    protected function executePart1(): void
    {
        $data = $this->data;
        $keys = [];
        $locks = [];
        $tipo = 0;
        foreach ($data as $nl => $row) {
            if (count($row) == 0) {
                if ($tipo == 1) {
                    $keys[] = $tmp;
                } else if ($tipo == 2) {
                    $locks[] = $tmp;
                }
                $tipo = 0;
                continue;
            }
            if (($tipo == 0) && ($row[0] == '#')) {
                $tipo = 1;
                $tmp = [1, 1, 1, 1, 1];
                continue;
            }
            if (($tipo == 0) && ($row[0] == '.')) {
                $tipo = 2;
                $tmp = [0, 0, 0, 0, 0];
                continue;
            }
            foreach ($row as $nr => $cell) {
                if ($cell == '#') {
                    $tmp[$nr] += 1;
                }
            }
        }
        if ($tipo == 1) {
            $keys[] = $tmp;
        } else if ($tipo == 2) {
            $locks[] = $tmp;
        }
        echo 'KEYs:' . count($keys) . "\n";
        foreach ($keys as $key) {
            echo tableDataHelper::dibujaSeguido($key);
        }
        echo 'LOCKs:' . count($locks) . "\n";
        foreach ($locks as $lock) {
            echo tableDataHelper::dibujaSeguido($lock);
        }
        $startedAt = microtime(true);

        $fits = 0;
        foreach ($keys as $key) {
            foreach ($locks as $lock) {
                if ($this->keyLockPair($key, $lock)) {
                    $fits++;
                }
            }
        }

        $this->result = (string)$fits;
    }

    protected function executePart2(): void
    {
        $this->result = '?';
    }

    private function keyLockPair(mixed $key, mixed $lock): bool
    {
        for ($i = 0; $i < 5; $i++) {
            if ($lock[$i] + $key[$i] > 7) {
                return false;
            }
        }
        return true;
    }
}
