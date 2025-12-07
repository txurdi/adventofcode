<?php

namespace App\util;

class tableDataHelper
{
    /**
     * Get a column of a table
     * @param array $table
     * @param int $colNumber
     * @return array
     */
    public static function getTableCol(array $table, int $colNumber): array
    {
        $col = [];
        foreach ($table as $key=>$row) {
            $col[$key] = $row[$colNumber];
        }
        return $col;
    }

    /**
     * Get how many times a number is repeated in a row
     * @param array $row
     * @param int $number
     * @return int
     */
    public static function getNumberRepeated(array $row, int $number): int
    {
        $repeated = 0;
        foreach ($row as $key=>$value) {
            if ($value == $number) {
                $repeated++;
            }
        }
        return $repeated;
    }


    /**
     * Returns a string with the table data in each line/row.
     * @param array $map
     * @return string
     */
    public static function mapToString(array $map): string
    {
        $result = '';
        foreach ($map as $nr => $row) {
            foreach ($row as $nc => $cell) {
                $result .= $cell;
            }
            $result .= PHP_EOL;
        }
        return $result;
    }

    /**
     * Returns a string with the string data in each line.
     * @param array $map
     * @return string
     */
    public static function arrayToString(array $list): string
    {
        $result = '';
        foreach ($list as $nr => $row) {
            $result .= $row.PHP_EOL;
        }
        return $result;
    }

    /**
     * Returns as a string the sequence of values separated by a separator.
     * @param array $sequence
     * @param string $separator
     * @param string|null $endOfLine
     * @return string
     */
    public static function dibujaSeguido(array $sequence, string $separator='', ?string $endOfLine=PHP_EOL): string
    {
        $result = '';
        foreach ($sequence as $value) {
            $result .= $value.$separator;
        }
        return $result.$endOfLine;
    }
}
