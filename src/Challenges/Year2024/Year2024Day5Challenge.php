<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day5Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_LINES;

    private array $rules;

    protected function executePart1(): void
    {
        $this->rules = [];
        $result = 0;

        $updates = [];
        $searchingFor = 'rules';
        foreach ($this->data as $line) {
            if ($line == "\n") {
                $searchingFor = 'updates';
                continue;
            }
            if ($searchingFor == 'rules') {
                $this->rules[] = explode('|', (trim($line)));
            } else {
                $updates[] = explode(',', (trim($line)));
            }
        }

        foreach ($updates as $update) {
            if ($this->updateIsCorrect($update)) {
                $result += $this->getTheMiddlePageNumber($update);
            }
        }

        $this->result = (string)$result;
    }

    protected function executePart2(): void
    {
        $this->rules = [];
        $result = 0;

        $updates = [];
        $searchingFor = 'rules';
        foreach ($this->data as $line) {
            if ($line == "\n") {
                $searchingFor = 'updates';
                continue;
            }
            if ($searchingFor == 'rules') {
                $this->rules[] = explode('|', (trim($line)));
            } else {
                $updates[] = explode(',', (trim($line)));
            }
        }

        foreach ($updates as $update) {
            $fixed = $this->incorrectUpdateFixed2($update);
            if (is_array($fixed)) {
                $result += $this->getTheMiddlePageNumber($fixed);
            }
        }

        $this->result = (string)$result;
    }

    private function updateIsCorrect(array $update): bool
    {
        foreach ($update as $pos => $number) {
            $rules = $this->rulesToApply($number);
            foreach ($rules['before'] as $before) {
                $found = array_search($before, $update);
                if ($found !== false) {
                    if ($pos < $found) {
                        return false;
                    }
                }
            }
            foreach ($rules['after'] as $after) {
                $found = array_search($after, $update);
                if ($found !== false) {
                    if ($pos > $found) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function incorrectUpdateFixed2(array $update): array|bool
    {
        $changed = false;
        $pos = 0;
        while ($pos < count($update)) {
            $number = $update[$pos];
            $numbersMustBeBefore = $this->numbersMustBeBefore($number);
            $lastPosition = $pos;
            foreach ($numbersMustBeBefore as $before) {
                $newPosition = array_search($before, $update);
                if ($newPosition !== false) {
                    if ($lastPosition < $newPosition) {
                        $lastPosition = $newPosition;
                    }
                }
            }
            if ($lastPosition !== $pos) {
                $update[$pos] = $update[$lastPosition];
                $update[$lastPosition] = $number;
                $changed = true;
            } else {
                $pos++;
            }
        }
        if ($changed) {
            return $update;
        }
        return false;
    }

    private function incorrectUpdateFixed(array $update): array|bool
    {
        $changed = false;
        $fixing = $update;
        foreach ($fixing as $pos => $number) {
            $rules = $this->rulesToApply($number);
            $fixingPos = $pos;
            foreach ($rules['before'] as $before) {
                $found = array_search($before, $update);
                if ($found !== false) {
                    if ($fixingPos < $found) {
                        $fixing[$fixingPos] = $before;
                        $fixing[$found] = $number;
                        $fixingPos = $found;
                        $changed = true;
                    }
                }
            }
            foreach ($rules['after'] as $after) {
                $found = array_search($after, $update);
                if ($found !== false) {
                    if ($fixingPos > $found) {
                        $fixing[$fixingPos] = $after;
                        $fixing[$found] = $number;
                        $fixingPos = $found;
                        $changed = true;
                    }
                }
            }
        }
        if ($changed) return $fixing;
        return false;
    }

    private function rulesToApply(int $number): array
    {
        $rules = ['after' => [], 'before' => []];
        foreach ($this->rules as $rule) {
            if ($number == $rule[0]) {
                $rules['after'][] = $rule[1];
            }
            if ($number == $rule[1]) {
                $rules['before'][] = $rule[0];
            }
        }
        return $rules;
    }

    private function getTheMiddlePageNumber(array $update): int
    {
        $numero = $update[intdiv(count($update), 2)];
        return $numero;
    }

    private function numbersMustBeBefore(int $number): array
    {
        $numbersMustBeBefore = [];
        foreach ($this->rules as $rule) {
            if ($rule[1] == $number) {
                $numbersMustBeBefore[] = $rule[0];
            }
        }
        return $numbersMustBeBefore;
    }
}
