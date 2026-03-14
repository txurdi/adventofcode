<?php

namespace App\Challenges\Year2024;

use App\Challenges\YearDayChallenge;
use App\util\fileDataHelper;

class Year2024Day3Challenge extends YearDayChallenge
{
    protected string $format = fileDataHelper::DATA_FORMAT_STRING;

    private const FINDING_START = "Finding m";
    private const FINDING_MUL = "Finding mul(";
    private const FINDING_N1 = "Finding number 1";
    private const FINDING_N2 = "Finding number 2";
    private const NOT_FINDING = "DON'T";

    protected function executePart1(): void
    {
        $data = $this->dataStr;
        $sum = 0;
        $status = self::FINDING_START;
        $number1 = $number2 = $previousCharacter = '';
        $leido = '';
        foreach (str_split($data) as $pos => $character) {
            if ($status == self::FINDING_START) {
                $number1 = $number2 = $leido = '';
                if ($character === 'm') {
                    $status = self::FINDING_MUL;
                }
            } else {
                switch ($character) {
                    case 'u':
                        if ($previousCharacter != 'm') $status = self::FINDING_START;
                        break;
                    case 'l':
                        if ($previousCharacter != 'u') $status = self::FINDING_START;
                        break;
                    case '(':
                        $status = self::FINDING_N1;
                        if ($previousCharacter != 'l') $status = self::FINDING_START;
                        break;
                    case ',':
                        $status = self::FINDING_N2;
                        if (!is_numeric($previousCharacter)) $status = self::FINDING_START;
                        break;
                    case ')':
                        if ($status == self::FINDING_N2) {
                            $sum += (int)$number1 * (int)$number2;
                        }
                        $status = self::FINDING_START;
                        break;
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                    case '0':
                        if ($status == self::FINDING_N1) {
                            $number1 .= $character;
                            if (strlen($number1) > 3) $status = self::FINDING_START;
                        } else if ($status == self::FINDING_N2) {
                            $number2 .= $character;
                            if (strlen($number2) > 3) $status = self::FINDING_START;
                        } else {
                            $status = self::FINDING_START;
                        }
                        break;
                    default:
                        $status = self::FINDING_START;
                }
            }
            $previousCharacter = $character;
        }

        $this->result = (string)$sum;
    }

    protected function executePart2(): void
    {
        $data = $this->dataStr;
        $sum = 0;
        $status = self::FINDING_START;
        $number1 = $number2 = $previousCharacter = '';
        foreach (str_split($data) as $pos => $character) {
            if ($status == self::NOT_FINDING) {
                if ($character != ')') {
                    continue;
                }
                if (
                    ($data[$pos-1] != '(') ||
                    ($data[$pos-2] != 'o') ||
                    ($data[$pos-3] != 'd')
                ) {
                    continue;
                }
                $status = self::FINDING_START;
            }
            if ($character == ')') {
                if (
                    ($data[$pos-1] == '(') &&
                    ($data[$pos-2] == 't') &&
                    ($data[$pos-3] == '\'') &&
                    ($data[$pos-4] == 'n') &&
                    ($data[$pos-5] == 'o') &&
                    ($data[$pos-6] == 'd')
                ) {
                    $status = self::NOT_FINDING;
                    continue;
                }
            }
            if ($status == self::FINDING_START) {
                $number1 = $number2 = $leido = '';
                if ($character === 'm') {
                    $status = self::FINDING_MUL;
                }
            } else {
                switch ($character) {
                    case 'u':
                        if ($previousCharacter != 'm') $status = self::FINDING_START;
                        break;
                    case 'l':
                        if ($previousCharacter != 'u') $status = self::FINDING_START;
                        break;
                    case '(':
                        $status = self::FINDING_N1;
                        if ($previousCharacter != 'l') $status = self::FINDING_START;
                        break;
                    case ',':
                        $status = self::FINDING_N2;
                        if (!is_numeric($previousCharacter)) $status = self::FINDING_START;
                        break;
                    case ')':
                        if ($status == self::FINDING_N2) {
                            $sum += (int)$number1 * (int)$number2;
                        }
                        $status = self::FINDING_START;
                        break;
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                    case '0':
                        if ($status == self::FINDING_N1) {
                            $number1 .= $character;
                            if (strlen($number1) > 3) $status = self::FINDING_START;
                        } else if ($status == self::FINDING_N2) {
                            $number2 .= $character;
                            if (strlen($number2) > 3) $status = self::FINDING_START;
                        } else {
                            $status = self::FINDING_START;
                        }
                        break;
                    default:
                        $status = self::FINDING_START;
                }
            }
            $previousCharacter = $character;
        }

        $this->result = (string)$sum;
    }
}
